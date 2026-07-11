<?php

declare(strict_types=1);

/**
 * controllers/UsuariosController.php
 *
 * GESTOR DE USUARIOS (IAM) — AXE FRAMEWORK — FASE 1 (BACKEND)
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Leer $usuario_autenticado_id inyectado por el Middleware.
 *   2. FIX A2: Validar token CSRF en toda petición POST.
 *   3. Escuchar peticiones POST e identificar la acción a ejecutar:
 *        • crear         → Registra un nuevo usuario en la tabla `usuarios`.
 *        • cambiar_nivel → Actualiza el nivel_acceso de un usuario existente.
 *        • toggle_estatus → Invierte el campo `activo` (Soft Delete / reactivación).
 *   4. FIX M4: Auditar SOLO si la operación en BD fue exitosa (dentro del try).
 *   5. FIX M2: Los errores de BD se loguean internamente; el usuario recibe
 *              mensajes genéricos sin detalle de infraestructura.
 *   6. Consultar todos los usuarios (GET) y exponerlos en $datos_vista.
 *
 * Precondición: el Middleware validó el Split Token; $usuario_autenticado_id
 * siempre contiene un entero válido al llegar aquí.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Capturar el ID de usuario autenticado (inyectado por el Middleware) ────
/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

// ── 2. Dependencias ───────────────────────────────────────────────────────────
require_once BASE_PATH . '/core/Seguridad.php';
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// Auditoria siempre disponible para todos los bloques de acción POST.
require_once BASE_PATH . '/core/Auditoria.php';

// Variable de mensaje; se definirá sólo si hubo una acción POST.
$mensaje_usuarios = null;

// ── 3. Manejador de Acciones (POST) ──────────────────────────────────────────
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

    // ── 3a. CREAR nuevo usuario ───────────────────────────────────────────────
    if ($accion === 'crear') {

        // Captura y saneamiento de campos.
        $email        = trim((string) filter_input(INPUT_POST, 'email',        FILTER_DEFAULT));
        $password     = trim((string) filter_input(INPUT_POST, 'password',     FILTER_DEFAULT));
        $nivel_acceso = (int) filter_input(INPUT_POST, 'nivel_acceso', FILTER_VALIDATE_INT);

        // Forzar nivel_acceso a 0 si el valor no es un entero válido.
        if ($nivel_acceso < 0) {
            $nivel_acceso = 0;
        }

        // Hashear la contraseña
        $password_hash_seguro = Seguridad::hashearPassword($password);

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (email, password_hash, nivel_acceso, activo)
                 VALUES (:email, :password_hash, :nivel_acceso, 1)'
            );

            $stmt->execute([
                ':email'         => $email,
                ':password_hash' => $password_hash_seguro,
                ':nivel_acceso'  => $nivel_acceso,
            ]);

            // FIX M4: Auditar SOLO si el INSERT fue exitoso
            Auditoria::registrar((int) $usuario_autenticado_id, 'USUARIO_CREADO', 'iam', [
                'nuevo_email'    => $email,
                'nivel_asignado' => $nivel_acceso
            ]);

            $mensaje_usuarios = '✅ Usuario "' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '" creado correctamente.';

        } catch (PDOException $e) {
            // FIX M2: Loguear internamente, mensaje genérico al usuario
            error_log('UsuariosController [crear]: ' . $e->getMessage());
            if ($e->getCode() === '23000') {
                $mensaje_usuarios = '❌ Error: el email "' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '" ya está registrado.';
            } else {
                $mensaje_usuarios = '❌ Error interno al crear el usuario. Contacta al administrador.';
            }
        }

    // ── 3b. CAMBIAR NIVEL DE ACCESO de un usuario ─────────────────────────────
    } elseif ($accion === 'cambiar_nivel') {

        $usuario_id  = (int) filter_input(INPUT_POST, 'usuario_id',  FILTER_VALIDATE_INT);
        $nuevo_nivel = (int) filter_input(INPUT_POST, 'nuevo_nivel',  FILTER_VALIDATE_INT);

        // Forzar valores seguros mínimos.
        if ($usuario_id  < 1) { $usuario_id  = 0; }
        if ($nuevo_nivel < 0) { $nuevo_nivel = 0; }

        try {
            $stmt = $pdo->prepare(
                'UPDATE usuarios SET nivel_acceso = ? WHERE id = ?'
            );
            $stmt->execute([$nuevo_nivel, $usuario_id]);

            // FIX M4: Auditar SOLO si el UPDATE fue exitoso (dentro del try)
            Auditoria::registrar((int) $usuario_autenticado_id, 'CAMBIO_NIVEL', 'iam', [
                'usuario_afectado_id' => $usuario_id,
                'nuevo_nivel'         => $nuevo_nivel
            ]);

            $mensaje_usuarios = 'Nivel de acceso del usuario #' . $usuario_id . ' actualizado a ' . $nuevo_nivel . '.';

        } catch (PDOException $e) {
            // FIX M2: Loguear internamente, mensaje genérico al usuario
            error_log('UsuariosController [cambiar_nivel]: ' . $e->getMessage());
            $mensaje_usuarios = '❌ Error interno al cambiar el nivel de acceso. Contacta al administrador.';
        }

    // ── 3c. TOGGLE ESTATUS (Soft Delete / Reactivación) ──────────────────────
    } elseif ($accion === 'toggle_estatus') {

        $usuario_id    = (int) filter_input(INPUT_POST, 'usuario_id',    FILTER_VALIDATE_INT);
        $estatus_actual = (int) filter_input(INPUT_POST, 'estatus_actual', FILTER_VALIDATE_INT);

        // Forzar valor seguro mínimo para el ID.
        if ($usuario_id < 1) { $usuario_id = 0; }

        // Invertir el estatus: 1 → 0 (suspender), 0 → 1 (reactivar).
        $nuevo_estatus = ($estatus_actual === 1) ? 0 : 1;

        try {
            $stmt = $pdo->prepare(
                'UPDATE usuarios SET activo = ? WHERE id = ?'
            );
            $stmt->execute([$nuevo_estatus, $usuario_id]);

            // FIX M4: Auditar SOLO si el UPDATE fue exitoso (dentro del try)
            Auditoria::registrar((int) $usuario_autenticado_id, 'TOGGLE_ESTATUS', 'iam', [
                'usuario_afectado_id' => $usuario_id,
                'estatus_asignado'    => $nuevo_estatus
            ]);

            $etiqueta         = ($nuevo_estatus === 0) ? 'suspendido' : 'reactivado';
            $mensaje_usuarios = 'Usuario #' . $usuario_id . ' ' . $etiqueta . ' correctamente.';

        } catch (PDOException $e) {
            // FIX M2: Loguear internamente, mensaje genérico al usuario
            error_log('UsuariosController [toggle_estatus]: ' . $e->getMessage());
            $mensaje_usuarios = '❌ Error interno al cambiar el estatus del usuario. Contacta al administrador.';
        }
    }
}

// ── 4. Lectura de Datos (GET / post-POST) ─────────────────────────────────────
// Se ejecuta siempre para que la vista tenga la lista actualizada.
try {
    $stmt_lista = $pdo->prepare(
        'SELECT id, email, nivel_acceso, activo
         FROM usuarios
         ORDER BY id DESC'
    );
    $stmt_lista->execute();
    $lista_usuarios = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('UsuariosController [listar]: ' . $e->getMessage());
    // En caso de fallo la vista recibirá un arreglo vacío.
    $lista_usuarios = [];
}

// ── 5. Inyección a la Vista ───────────────────────────────────────────────────
// El front controller llama a extract($datos_vista) antes de incluir la vista,
// por lo que cada clave se convierte en una variable local disponible en el HTML.

// Cargar lista maestra de roles desde el archivo de configuración central.
$roles_config = require BASE_PATH . '/config/roles.php';

$datos_vista = [
    'lista_usuarios'   => $lista_usuarios,
    'mensaje_usuarios' => $mensaje_usuarios ?? null,
    'roles_config'     => $roles_config,
];
