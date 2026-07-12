<?php

declare(strict_types=1);

/**
 * controllers/GestorRutasController.php
 *
 * GESTOR DE RUTAS — MÓDULO CRUD COMPLETO — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Leer $usuario_autenticado_id inyectado por el Middleware.
 *   2. FIX A2: Validar token CSRF en toda petición POST.
 *   3. Escuchar peticiones POST según `accion`:
 *        • actualizar_layout     → UPDATE plantilla            + recompila caché.
 *        • actualizar_nivel      → UPDATE nivel_minimo          + recompila caché.
 *        • actualizar_vista_ctrl → UPDATE vista y controlador   + recompila caché.
 *        • crear_ruta            → INSERT nueva ruta            + recompila caché.
 *        • eliminar_ruta         → DELETE ruta                  + recompila caché.
 *   4. Consultar SELECT * FROM rutas para la vista de lista.
 *   5. Escanear templates/*.php, views/*.php y controllers/*.php
 *      para generar las listas blancas de selects dinámicos.
 *   6. Inyectar $datos_vista al front controller vía extract().
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Capturar el ID de usuario autenticado (inyectado por el Middleware) ────
/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

// ── 2. Dependencias ───────────────────────────────────────────────────────────
require_once BASE_PATH . '/core/Auditoria.php';
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// ── 3. Escanear recursos disponibles desde el sistema de archivos ─────────────
// Plantillas (templates/*.php)
$archivos_plantillas    = glob(BASE_PATH . '/templates/*.php');
$plantillas_disponibles = [];
if (is_array($archivos_plantillas)) {
    foreach ($archivos_plantillas as $ruta_fisica) {
        $plantillas_disponibles[] = basename($ruta_fisica);
    }
}

// Vistas (views/*.php)
$archivos_vistas    = glob(BASE_PATH . '/views/*.php');
$vistas_disponibles = [];
if (is_array($archivos_vistas)) {
    foreach ($archivos_vistas as $ruta_fisica) {
        $vistas_disponibles[] = basename($ruta_fisica);
    }
}

// Controladores (controllers/*.php)
$archivos_controladores    = glob(BASE_PATH . '/controllers/*.php');
$controladores_disponibles = [];
if (is_array($archivos_controladores)) {
    foreach ($archivos_controladores as $ruta_fisica) {
        $controladores_disponibles[] = basename($ruta_fisica);
    }
}

// Roles disponibles (fuente única de verdad) — carga desde config/roles.php
$roles_disponibles  = require BASE_PATH . '/config/roles.php';
// Lista blanca de niveles numéricos válidos (extraída dinámicamente del mapa)
$niveles_permitidos = array_keys($roles_disponibles);

// Variable de mensaje para la vista; sólo se define si hubo acción POST.
$mensaje_gestor_rutas = null;

// ── Helper: Recompilar config/rutas_cache.php de forma atómica ───────────────
function recompilar_cache_rutas(PDO $pdo): bool
{
    $select_rutas = $pdo->query(
        'SELECT uri, vista, plantilla, controlador, requiere_login, nivel_minimo, css, js
         FROM rutas
         ORDER BY id ASC'
    );

    $rutas_array = [];
    foreach ($select_rutas as $fila) {
        $css_decoded = json_decode((string) $fila['css'], true);
        $css_final   = is_array($css_decoded) ? $css_decoded : [];

        $js_decoded = json_decode((string) $fila['js'], true);
        $js_final   = is_array($js_decoded) ? $js_decoded : [];

        $rutas_array[$fila['uri']] = [
            'uri'            => trim($fila['uri']),
            'vista'          => trim($fila['vista']),
            'plantilla'      => trim($fila['plantilla']),
            'controlador'    => $fila['controlador'] ? trim($fila['controlador']) : null,
            'requiere_login' => (bool) $fila['requiere_login'],
            'nivel_minimo'   => (int) $fila['nivel_minimo'],
            'css'            => $css_final,
            'js'             => $js_final,
        ];
    }

    $fecha_compilacion = date('Y-m-d H:i:s');
    $export_php        = var_export($rutas_array, true);

    $cache_contenido = <<<PHP
<?php
// Archivo auto-generado por el Compilador de Axe Framework
// Fecha de última compilación: {$fecha_compilacion}

return {$export_php};
PHP;

    $ruta_temp  = BASE_PATH . '/config/temp_rutas.php';
    $ruta_cache = BASE_PATH . '/config/rutas_cache.php';

    if (file_put_contents($ruta_temp, $cache_contenido) === false) {
        error_log('GestorRutasController [recompilar_cache]: No se pudo escribir el archivo temporal.');
        return false;
    }

    if (!rename($ruta_temp, $ruta_cache)) {
        @unlink($ruta_temp);
        error_log('GestorRutasController [recompilar_cache]: No se pudo reemplazar el archivo de caché.');
        return false;
    }

    return true;
}

// ── 4. Manejador de Acciones POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── FIX A2: Validar token CSRF antes de procesar cualquier acción ─────────
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

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Actualizar plantilla (layout) — accion=actualizar_layout
    // ══════════════════════════════════════════════════════════════════════════
    if ($accion === 'actualizar_layout') {

        $id = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id < 1) {
            $mensaje_gestor_rutas = '❌ ID de ruta inválido.';
            goto fin_post;
        }

        $plantilla_raw     = trim((string) filter_input(INPUT_POST, 'plantilla', FILTER_DEFAULT));
        $archivo_plantilla = basename($plantilla_raw);
        $valor_canonico    = 'templates/' . $archivo_plantilla;

        if (
            $plantilla_raw !== $valor_canonico ||
            !in_array($archivo_plantilla, $plantillas_disponibles, true)
        ) {
            $mensaje_gestor_rutas = '❌ Plantilla no válida. Selecciona una opción del listado.';
            goto fin_post;
        }

        try {
            $stmt = $pdo->prepare('UPDATE rutas SET plantilla = :plantilla WHERE id = :id');
            $stmt->execute([':plantilla' => $valor_canonico, ':id' => $id]);

            if (!recompilar_cache_rutas($pdo)) {
                $mensaje_gestor_rutas = '❌ Error al recompilar la caché. La BD fue actualizada.';
                goto fin_post;
            }

            Auditoria::registrar((int) $usuario_autenticado_id, 'RUTA_LAYOUT_ACTUALIZADO', 'gestor-rutas', [
                'ruta_id'         => $id,
                'nueva_plantilla' => $valor_canonico,
            ]);

            header('Location: /gestor-rutas?ok=layout');
            exit;

        } catch (PDOException $e) {
            error_log('GestorRutasController [actualizar_layout]: ' . $e->getMessage());
            $mensaje_gestor_rutas = '❌ Error interno al actualizar la plantilla.';
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Actualizar nivel mínimo — accion=actualizar_nivel
    // ══════════════════════════════════════════════════════════════════════════
    elseif ($accion === 'actualizar_nivel') {

        $id = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id < 1) {
            $mensaje_gestor_rutas = '❌ ID de ruta inválido.';
            goto fin_post;
        }

        $nivel_raw = filter_input(INPUT_POST, 'nivel_minimo', FILTER_VALIDATE_INT);
        if ($nivel_raw === false || $nivel_raw === null || !in_array((int) $nivel_raw, $niveles_permitidos, true)) {
            $mensaje_gestor_rutas = '❌ Nivel mínimo no válido.';
            goto fin_post;
        }
        $nivel = (int) $nivel_raw;

        try {
            $stmt = $pdo->prepare('UPDATE rutas SET nivel_minimo = :nivel WHERE id = :id');
            $stmt->execute([':nivel' => $nivel, ':id' => $id]);

            if (!recompilar_cache_rutas($pdo)) {
                $mensaje_gestor_rutas = '❌ Error al recompilar la caché. La BD fue actualizada.';
                goto fin_post;
            }

            Auditoria::registrar((int) $usuario_autenticado_id, 'RUTA_NIVEL_ACTUALIZADO', 'gestor-rutas', [
                'ruta_id'      => $id,
                'nuevo_nivel'  => $nivel,
            ]);

            header('Location: /gestor-rutas?ok=nivel');
            exit;

        } catch (PDOException $e) {
            error_log('GestorRutasController [actualizar_nivel]: ' . $e->getMessage());
            $mensaje_gestor_rutas = '❌ Error interno al actualizar el nivel mínimo.';
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Crear nueva ruta — accion=crear_ruta
    // ══════════════════════════════════════════════════════════════════════════
    elseif ($accion === 'crear_ruta') {

        // URI
        $uri_raw = trim((string) filter_input(INPUT_POST, 'uri', FILTER_DEFAULT));
        if (empty($uri_raw) || !str_starts_with($uri_raw, '/')) {
            $mensaje_gestor_rutas = '❌ La URI es inválida. Debe comenzar con /.';
            goto fin_post;
        }
        $uri = $uri_raw;

        // Vista — validar por lista blanca
        $vista_raw     = trim((string) filter_input(INPUT_POST, 'vista', FILTER_DEFAULT));
        $archivo_vista = basename(str_replace('views/', '', $vista_raw));
        $valor_vista   = 'views/' . $archivo_vista;

        if ($vista_raw !== $valor_vista || !in_array($archivo_vista, $vistas_disponibles, true)) {
            $mensaje_gestor_rutas = '❌ Vista no válida.';
            goto fin_post;
        }

        // Controlador — opcional
        $ctrl_raw = trim((string) filter_input(INPUT_POST, 'controlador', FILTER_DEFAULT));
        $valor_ctrl = null;
        if (!empty($ctrl_raw)) {
            $archivo_ctrl = basename(str_replace('controllers/', '', $ctrl_raw));
            $valor_ctrl_candidato = 'controllers/' . $archivo_ctrl;
            if ($ctrl_raw !== $valor_ctrl_candidato || !in_array($archivo_ctrl, $controladores_disponibles, true)) {
                $mensaje_gestor_rutas = '❌ Controlador no válido.';
                goto fin_post;
            }
            $valor_ctrl = $valor_ctrl_candidato;
        }

        // Nivel mínimo
        $nivel_raw = filter_input(INPUT_POST, 'nivel_minimo', FILTER_VALIDATE_INT);
        if ($nivel_raw === false || $nivel_raw === null || !in_array((int) $nivel_raw, $niveles_permitidos, true)) {
            $mensaje_gestor_rutas = '❌ Nivel mínimo no válido.';
            goto fin_post;
        }
        $nivel = (int) $nivel_raw;

        // Plantilla — validar por lista blanca
        $plantilla_raw     = trim((string) filter_input(INPUT_POST, 'plantilla', FILTER_DEFAULT));
        $archivo_plantilla = basename(str_replace('templates/', '', $plantilla_raw));
        $valor_plantilla   = 'templates/' . $archivo_plantilla;

        if ($plantilla_raw !== $valor_plantilla || !in_array($archivo_plantilla, $plantillas_disponibles, true)) {
            $mensaje_gestor_rutas = '❌ Plantilla no válida.';
            goto fin_post;
        }

        try {
            // Verificar que la URI no exista ya
            $check = $pdo->prepare('SELECT COUNT(*) FROM rutas WHERE uri = :uri');
            $check->execute([':uri' => $uri]);
            if ((int) $check->fetchColumn() > 0) {
                $mensaje_gestor_rutas = '❌ Ya existe una ruta con esa URI.';
                goto fin_post;
            }

            $stmt = $pdo->prepare(
                'INSERT INTO rutas (uri, vista, controlador, nivel_minimo, plantilla, requiere_login, css, js)
                 VALUES (:uri, :vista, :controlador, :nivel, :plantilla, 0, "[]", "[]")'
            );
            $stmt->execute([
                ':uri'         => $uri,
                ':vista'       => $valor_vista,
                ':controlador' => $valor_ctrl,
                ':nivel'       => $nivel,
                ':plantilla'   => $valor_plantilla,
            ]);

            $nuevo_id = (int) $pdo->lastInsertId();

            if (!recompilar_cache_rutas($pdo)) {
                $mensaje_gestor_rutas = '❌ Ruta creada pero error al recompilar la caché.';
                goto fin_post;
            }

            Auditoria::registrar((int) $usuario_autenticado_id, 'RUTA_CREADA', 'gestor-rutas', [
                'ruta_id' => $nuevo_id,
                'uri'     => $uri,
            ]);

            header('Location: /gestor-rutas?ok=creada');
            exit;

        } catch (PDOException $e) {
            error_log('GestorRutasController [crear_ruta]: ' . $e->getMessage());
            $mensaje_gestor_rutas = '❌ Error interno al crear la ruta.';
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Actualizar vista y controlador — accion=actualizar_vista_ctrl
    // ══════════════════════════════════════════════════════════════════════════
    elseif ($accion === 'actualizar_vista_ctrl') {

        $id = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id < 1) {
            $mensaje_gestor_rutas = '❌ ID de ruta inválido.';
            goto fin_post;
        }

        // Vista — validar por lista blanca
        $vista_raw     = trim((string) filter_input(INPUT_POST, 'vista', FILTER_DEFAULT));
        $archivo_vista = basename(str_replace('views/', '', $vista_raw));
        $valor_vista   = 'views/' . $archivo_vista;

        if ($vista_raw !== $valor_vista || !in_array($archivo_vista, $vistas_disponibles, true)) {
            $mensaje_gestor_rutas = '❌ Vista no válida. Selecciona una opción del listado.';
            goto fin_post;
        }

        // Controlador — opcional
        $ctrl_raw   = trim((string) filter_input(INPUT_POST, 'controlador', FILTER_DEFAULT));
        $valor_ctrl = null;
        if (!empty($ctrl_raw)) {
            $archivo_ctrl         = basename(str_replace('controllers/', '', $ctrl_raw));
            $valor_ctrl_candidato = 'controllers/' . $archivo_ctrl;
            if ($ctrl_raw !== $valor_ctrl_candidato || !in_array($archivo_ctrl, $controladores_disponibles, true)) {
                $mensaje_gestor_rutas = '❌ Controlador no válido. Selecciona una opción del listado.';
                goto fin_post;
            }
            $valor_ctrl = $valor_ctrl_candidato;
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE rutas SET vista = :vista, controlador = :controlador WHERE id = :id'
            );
            $stmt->execute([
                ':vista'       => $valor_vista,
                ':controlador' => $valor_ctrl,
                ':id'          => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                $mensaje_gestor_rutas = '❌ No se encontró la ruta a actualizar.';
                goto fin_post;
            }

            // ACCIÓN CRÍTICA: forzar regeneración del archivo de caché
            if (!recompilar_cache_rutas($pdo)) {
                $mensaje_gestor_rutas = '❌ Campos actualizados pero error al recompilar la caché.';
                goto fin_post;
            }

            Auditoria::registrar((int) $usuario_autenticado_id, 'RUTA_VISTA_CTRL_ACTUALIZADO', 'gestor-rutas', [
                'ruta_id'          => $id,
                'nueva_vista'      => $valor_vista,
                'nuevo_controlador' => $valor_ctrl,
            ]);

            header('Location: /gestor-rutas?ok=vista_ctrl');
            exit;

        } catch (PDOException $e) {
            error_log('GestorRutasController [actualizar_vista_ctrl]: ' . $e->getMessage());
            $mensaje_gestor_rutas = '❌ Error interno al actualizar vista y controlador.';
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Refrescar caché — accion=refrescar_cache
    // ══════════════════════════════════════════════════════════════════════════
    // Consulta la tabla rutas y sobreescribe config/rutas_cache.php de forma
    // atómica (archivo temporal → rename). No borra el archivo antes de escribir
    // para evitar la ventana de caché vacío en requests concurrentes.
    // ══════════════════════════════════════════════════════════════════════════
    elseif ($accion === 'refrescar_cache') {

        if (!recompilar_cache_rutas($pdo)) {
            $mensaje_gestor_rutas = '❌ Error al regenerar la caché de rutas.';
            goto fin_post;
        }

        Auditoria::registrar((int) $usuario_autenticado_id, 'CACHE_RUTAS_REFRESCADA', 'gestor-rutas', [
            'accion' => 'refrescar_cache',
        ]);

        header('Location: /gestor-rutas?ok=cache_refrescada');
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ── HANDLER: Eliminar ruta — accion=eliminar_ruta
    // ══════════════════════════════════════════════════════════════════════════
    elseif ($accion === 'eliminar_ruta') {

        $id = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id < 1) {
            $mensaje_gestor_rutas = '❌ ID de ruta inválido.';
            goto fin_post;
        }

        try {
            // Recuperar URI para auditoría antes de borrar
            $stmt_uri = $pdo->prepare('SELECT uri FROM rutas WHERE id = :id');
            $stmt_uri->execute([':id' => $id]);
            $uri_eliminada = (string) ($stmt_uri->fetchColumn() ?: '(desconocida)');

            $stmt = $pdo->prepare('DELETE FROM rutas WHERE id = :id');
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                $mensaje_gestor_rutas = '❌ No se encontró la ruta a eliminar.';
                goto fin_post;
            }

            if (!recompilar_cache_rutas($pdo)) {
                $mensaje_gestor_rutas = '❌ Ruta eliminada pero error al recompilar la caché.';
                goto fin_post;
            }

            Auditoria::registrar((int) $usuario_autenticado_id, 'RUTA_ELIMINADA', 'gestor-rutas', [
                'ruta_id'       => $id,
                'uri_eliminada' => $uri_eliminada,
            ]);

            header('Location: /gestor-rutas?ok=eliminada');
            exit;

        } catch (PDOException $e) {
            error_log('GestorRutasController [eliminar_ruta]: ' . $e->getMessage());
            $mensaje_gestor_rutas = '❌ Error interno al eliminar la ruta.';
        }
    }

    fin_post: // Etiqueta de salida rápida para validaciones fallidas
}

// ── 5. Lectura de Datos (GET / post-POST sin redirección) ─────────────────────
try {
    $stmt_lista = $pdo->query(
        'SELECT id, uri, vista, controlador, nivel_minimo, plantilla
         FROM rutas
         ORDER BY id ASC'
    );
    $lista_rutas = $stmt_lista->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('GestorRutasController [listar]: ' . $e->getMessage());
    $lista_rutas = [];
}

// Mensajes de éxito desde el parámetro GET (después del PRG).
if (!empty($_GET['ok'])) {
    $mensajes_ok = [
        'layout'           => '✅ Plantilla actualizada correctamente y caché recompilada.',
        'nivel'            => '✅ Nivel mínimo actualizado correctamente y caché recompilada.',
        'vista_ctrl'       => '✅ Vista y controlador actualizados correctamente y caché recompilada.',
        'creada'           => '✅ Nueva ruta creada exitosamente y caché recompilada.',
        'eliminada'        => '✅ Ruta eliminada correctamente y caché recompilada.',
        'cache_refrescada' => '✅ rutas_cache.php actualizado correctamente desde la tabla rutas.',
        '1'                => '✅ Operación realizada correctamente.',
    ];
    $ok_key = (string) $_GET['ok'];
    $mensaje_gestor_rutas = $mensajes_ok[$ok_key] ?? '✅ Operación realizada correctamente.';
}

// ── 6. Inyección a la Vista ───────────────────────────────────────────────────
$datos_vista = [
    'lista_rutas'              => $lista_rutas,
    'roles_disponibles'        => $roles_disponibles,
    'plantillas_disponibles'   => $plantillas_disponibles,
    'vistas_disponibles'       => $vistas_disponibles,
    'controladores_disponibles'=> $controladores_disponibles,
    'mensaje_gestor_rutas'     => $mensaje_gestor_rutas,
];
