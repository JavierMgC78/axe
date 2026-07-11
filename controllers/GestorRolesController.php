<?php

declare(strict_types=1);

/**
 * controllers/GestorRolesController.php
 *
 * GESTOR DE ROLES (CACHÉ DE CONFIGURACIÓN) — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Implementa el patrón Caché de Configuración Híbrido:
 *   - La tabla `roles` en BD es la fuente administrativa de verdad.
 *   - El archivo `config/roles.php` es el caché estático de alto rendimiento
 *     consumido por el resto del sistema.
 *   - Toda operación de escritura (crear / eliminar) regenera el caché
 *     automáticamente mediante reconstruirCache().
 *
 * Responsabilidades:
 *   1. Leer $usuario_autenticado_id inyectado por el Middleware.
 *   2. FIX A2: Validar token CSRF en toda petición POST.
 *   3. Escuchar peticiones POST según `accion`:
 *        • crear    → INSERT en `roles` + regenera caché → redirige.
 *        • eliminar → Verifica integridad referencial en `usuarios`,
 *                     DELETE de `roles` + regenera caché → redirige.
 *   4. Consultar SELECT * FROM roles para la vista de lista.
 *   5. Inyectar $datos_vista al front controller vía extract().
 *
 * Regla de Negocio (Integridad):
 *   No se puede eliminar un rol si existen usuarios con ese nivel asignado.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Capturar el ID de usuario autenticado (inyectado por el Middleware) ────
/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

// ── 2. Dependencias ───────────────────────────────────────────────────────────
require_once BASE_PATH . '/core/Auditoria.php';
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// Variable de mensaje; se definirá sólo si hubo una acción POST.
$mensaje_roles = null;

// ── 3. Función Privada: Reconstruir la Caché Estática ────────────────────────
/**
 * reconstruirCache()
 *
 * Regenera el archivo `config/roles.php` consultando la tabla `roles` en BD.
 * Construye un arreglo asociativo [nivel => nombre] y lo serializa usando
 * var_export() para que el archivo sea un return PHP válido y de carga rápida.
 *
 * @param PDO $pdo  Instancia activa de la conexión a la base de datos.
 * @return void
 */
function reconstruirCache(PDO $pdo): void
{
    // Consultar todos los roles ordenados por nivel ascendente.
    $stmt = $pdo->prepare('SELECT nivel, nombre FROM roles ORDER BY nivel ASC');
    $stmt->execute();
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Construir el arreglo asociativo [nivel (int) => nombre (string)].
    $arreglo_roles = [];
    foreach ($filas as $fila) {
        $arreglo_roles[(int) $fila['nivel']] = (string) $fila['nombre'];
    }

    // Serializar y escribir el archivo de caché estática.
    // El resultado es un archivo PHP válido que devuelve directamente el arreglo.
    $ruta_cache  = BASE_PATH . '/config/roles.php';
    $contenido   = '<?php return ' . var_export($arreglo_roles, true) . ';' . PHP_EOL;

    file_put_contents($ruta_cache, $contenido, LOCK_EX);
}

// ── 4. Manejador de Acciones (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── FIX A2: Validar token CSRF ────────────────────────────────────────────
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $csrf_recibido = (string) filter_input(INPUT_POST, 'csrf_token', FILTER_DEFAULT);
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_recibido)) {
        http_response_code(403);
        exit('Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
    }

    // Identificar la acción solicitada.
    $accion = trim((string) filter_input(INPUT_POST, 'accion', FILTER_DEFAULT));

    // ── 4a. CREAR nuevo rol ───────────────────────────────────────────────────
    if ($accion === 'crear') {

        $nivel       = (int) filter_input(INPUT_POST, 'nivel',       FILTER_VALIDATE_INT);
        $nombre      = trim((string) filter_input(INPUT_POST, 'nombre',      FILTER_DEFAULT));
        $descripcion = trim((string) filter_input(INPUT_POST, 'descripcion', FILTER_DEFAULT));

        // Validación mínima: nivel debe ser >= 0 y nombre no debe estar vacío.
        if ($nivel < 0 || $nombre === '') {
            $mensaje_roles = '❌ Error: el nivel debe ser mayor o igual a 0 y el nombre es obligatorio.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO roles (nivel, nombre, descripcion)
                     VALUES (:nivel, :nombre, :descripcion)'
                );
                $stmt->execute([
                    ':nivel'       => $nivel,
                    ':nombre'      => $nombre,
                    ':descripcion' => $descripcion,
                ]);

                // Regenerar la caché estática inmediatamente tras el INSERT exitoso.
                reconstruirCache($pdo);

                // FIX M4: Auditar SOLO si el INSERT fue exitoso.
                Auditoria::registrar((int) $usuario_autenticado_id, 'ROL_CREADO', 'gestor-roles', [
                    'nivel'  => $nivel,
                    'nombre' => $nombre,
                ]);

                // Redirigir a la lista del Gestor de Roles (patrón Post/Redirect/Get).
                header('Location: /gestor-roles?ok=creado');
                exit;

            } catch (PDOException $e) {
                // FIX M2: Loguear internamente, mensaje genérico al usuario.
                error_log('GestorRolesController [crear]: ' . $e->getMessage());
                if ($e->getCode() === '23000') {
                    $mensaje_roles = '❌ Error: ya existe un rol con el nivel ' . $nivel . '.';
                } else {
                    $mensaje_roles = '❌ Error interno al crear el rol. Contacta al administrador.';
                }
            }
        }

    // ── 4b. ELIMINAR un rol ───────────────────────────────────────────────────
    } elseif ($accion === 'eliminar') {

        $nivel = (int) filter_input(INPUT_POST, 'nivel', FILTER_VALIDATE_INT);

        if ($nivel < 0) {
            $mensaje_roles = '❌ Error: nivel de rol inválido.';
        } else {
            try {
                // ── REGLA DE NEGOCIO: Verificar integridad referencial ─────────
                // No se puede borrar un rol si hay usuarios con ese nivel asignado.
                $stmt_check = $pdo->prepare(
                    'SELECT COUNT(*) FROM usuarios WHERE nivel_acceso = :nivel'
                );
                $stmt_check->execute([':nivel' => $nivel]);
                $usuarios_con_nivel = (int) $stmt_check->fetchColumn();

                if ($usuarios_con_nivel > 0) {
                    // Bloquear la acción: el rol está en uso.
                    $mensaje_roles = '⚠️ No se puede eliminar el rol de nivel ' . $nivel
                        . ' porque ' . $usuarios_con_nivel
                        . ' usuario(s) tienen ese nivel asignado. Reasígnalos primero.';

                } else {
                    // El rol no está en uso: proceder con el borrado.
                    $stmt_delete = $pdo->prepare(
                        'DELETE FROM roles WHERE nivel = :nivel'
                    );
                    $stmt_delete->execute([':nivel' => $nivel]);

                    // Regenerar la caché estática inmediatamente tras el DELETE exitoso.
                    reconstruirCache($pdo);

                    // FIX M4: Auditar SOLO si el DELETE fue exitoso.
                    Auditoria::registrar((int) $usuario_autenticado_id, 'ROL_ELIMINADO', 'gestor-roles', [
                        'nivel' => $nivel,
                    ]);

                    // Redirigir a la lista del Gestor de Roles (patrón Post/Redirect/Get).
                    header('Location: /gestor-roles?ok=eliminado');
                    exit;
                }

            } catch (PDOException $e) {
                // FIX M2: Loguear internamente, mensaje genérico al usuario.
                error_log('GestorRolesController [eliminar]: ' . $e->getMessage());
                $mensaje_roles = '❌ Error interno al eliminar el rol. Contacta al administrador.';
            }
        }
    }
}

// ── 5. Lectura de Datos (GET / post-POST) ─────────────────────────────────────
// Se ejecuta siempre para que la vista tenga la lista actualizada.
try {
    $stmt_lista = $pdo->prepare(
        'SELECT nivel, nombre, descripcion
         FROM roles
         ORDER BY nivel ASC'
    );
    $stmt_lista->execute();
    $lista_roles = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('GestorRolesController [listar]: ' . $e->getMessage());
    // En caso de fallo la vista recibirá un arreglo vacío.
    $lista_roles = [];
}

// Capturar mensajes de éxito transmitidos por el patrón PRG vía query string.
if ($mensaje_roles === null && isset($_GET['ok'])) {
    $acciones_ok = [
        'creado'    => '✅ Rol creado correctamente. La caché de roles ha sido regenerada.',
        'eliminado' => '✅ Rol eliminado correctamente. La caché de roles ha sido regenerada.',
    ];
    $clave_ok      = trim((string) filter_input(INPUT_GET, 'ok', FILTER_DEFAULT));
    $mensaje_roles = $acciones_ok[$clave_ok] ?? null;
}

// ── 6. Inyección a la Vista ───────────────────────────────────────────────────
// El front controller llama a extract($datos_vista) antes de incluir la vista,
// por lo que cada clave se convierte en una variable local disponible en el HTML.
$datos_vista = [
    'lista_roles'   => $lista_roles,
    'mensaje_roles' => $mensaje_roles ?? null,
];
