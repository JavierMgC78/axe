<?php

declare(strict_types=1);

/**
 * controllers/EditorVistasController.php
 *
 * EDITOR DE VISTAS PÚBLICAS — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. GET  sin parámetro          → lista todas las vistas en views/public/*_public.php
 *   2. GET  ?archivo=views/public/ → lee el archivo y lo pasa a la vista para edición
 *   3. POST accion=guardar_vista   → valida CSRF + nivel + whitelist → escribe archivo
 *
 * Seguridad:
 *   • Lista blanca estricta: solo archivos dentro de views/public/ con sufijo _public.php
 *   • realpath() para prevenir path traversal
 *   • CSRF en todo POST
 *   • Nivel mínimo 100
 *   • Escritura atómica: temp file → rename
 * ─────────────────────────────────────────────────────────────────────────────
 */

/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

require_once BASE_PATH . '/core/Auditoria.php';
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// ── Guardia de nivel (doble seguridad, el router ya lo comprueba) ─────────────
if ((int)($nivel_usuario ?? 0) < 100) {
    http_response_code(403);
    exit('Acceso denegado: nivel insuficiente.');
}

// ── Sesión para CSRF ──────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Directorio raíz de vistas públicas ───────────────────────────────────────
$dir_publico = BASE_PATH . '/views/public';
$dir_real    = realpath($dir_publico);

$mensaje_editor       = null;
$archivo_seleccionado = null;
$contenido_archivo    = null;

// ── Manejador POST: guardar vista ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar CSRF
    $csrf_recibido = (string) filter_input(INPUT_POST, 'csrf_token', FILTER_DEFAULT);
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_recibido)) {
        http_response_code(403);
        exit('Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
    }

    $accion = trim((string) filter_input(INPUT_POST, 'accion', FILTER_DEFAULT));

    if ($accion === 'guardar_vista') {

        $archivo_raw      = trim((string) filter_input(INPUT_POST, 'archivo', FILTER_DEFAULT));
        $contenido_nuevo  = (string) ($_POST['contenido'] ?? '');

        // Validar ruta: debe existir, estar dentro de views/public/ y tener sufijo _public.php
        $archivo_abs = realpath(BASE_PATH . '/' . $archivo_raw);

        if (
            !$archivo_abs ||
            !$dir_real    ||
            !str_starts_with($archivo_abs, $dir_real) ||
            !str_ends_with($archivo_abs, '_public.php')
        ) {
            http_response_code(400);
            exit('Archivo no permitido.');
        }

        // Escritura atómica para evitar archivos a medio escribir
        $ruta_temp = $archivo_abs . '.tmp_' . bin2hex(random_bytes(4));

        if (file_put_contents($ruta_temp, $contenido_nuevo) === false) {
            $mensaje_editor = '❌ Error: no se pudo escribir el archivo temporal.';
        } elseif (!rename($ruta_temp, $archivo_abs)) {
            @unlink($ruta_temp);
            $mensaje_editor = '❌ Error: no se pudo reemplazar el archivo.';
        } else {
            Auditoria::registrar((int) $usuario_autenticado_id, 'VISTA_GUARDADA', 'editor-vistas', [
                'archivo' => $archivo_raw,
                'bytes'   => strlen($contenido_nuevo),
            ]);
            header('Location: /editor-vistas?archivo=' . urlencode($archivo_raw) . '&ok=guardado');
            exit;
        }
    }
}

// ── Leer lista de vistas públicas disponibles ─────────────────────────────────
$archivos_glob  = glob($dir_publico . '/*_public.php') ?: [];
$vistas_publicas = [];

foreach ($archivos_glob as $ruta_fisica) {
    $nombre   = basename($ruta_fisica);
    $relativa = 'views/public/' . $nombre;
    $stat     = stat($ruta_fisica);

    $vistas_publicas[] = [
        'nombre'    => $nombre,
        'relativa'  => $relativa,
        'abs'       => $ruta_fisica,
        'bytes'     => $stat ? (int) $stat['size'] : 0,
        'modificado' => $stat ? date('d/m/Y H:i', (int) $stat['mtime']) : '—',
    ];
}

// ── GET: cargar archivo seleccionado para edición ─────────────────────────────
if (!empty($_GET['archivo'])) {
    $archivo_raw = trim((string) $_GET['archivo']);
    $archivo_abs = realpath(BASE_PATH . '/' . $archivo_raw);

    if (
        $archivo_abs &&
        $dir_real    &&
        str_starts_with($archivo_abs, $dir_real) &&
        str_ends_with($archivo_abs, '_public.php')
    ) {
        $archivo_seleccionado = $archivo_raw;
        $leido = file_get_contents($archivo_abs);
        $contenido_archivo    = ($leido !== false) ? $leido : '';

        if ($leido === false) {
            $mensaje_editor = '❌ No se pudo leer el archivo.';
        }
    } else {
        $mensaje_editor = '❌ Archivo no válido o no permitido.';
    }
}

// Mensaje de éxito PRG
if (!empty($_GET['ok']) && $mensaje_editor === null) {
    $ok_msgs = [
        'guardado' => '✅ Archivo guardado correctamente.',
    ];
    $mensaje_editor = $ok_msgs[(string) $_GET['ok']] ?? '✅ Operación realizada.';
}

// ── Inyección a la Vista ──────────────────────────────────────────────────────
$datos_vista = [
    'titulo'               => 'Editor de Vistas',
    'vistas_publicas'      => $vistas_publicas,
    'archivo_seleccionado' => $archivo_seleccionado,
    'contenido_archivo'    => $contenido_archivo,
    'mensaje_editor'       => $mensaje_editor,
];
