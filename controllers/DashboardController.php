<?php

declare(strict_types=1);

/**
 * controllers/DashboardController.php
 *
 * CONTROLADOR DEL ÁREA PROTEGIDA — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Leer la variable global $usuario_autenticado_id inyectada por el
 *      Middleware de autenticación (a través del enrutador central).
 *   2. Consultar la tabla `usuarios` para obtener el email del usuario actual.
 *   3. Preparar $datos_vista con los datos necesarios para la vista.
 *   4. [GESTOR DE RUTAS — FASE 1] Escuchar peticiones POST para crear nuevas
 *      rutas en la BD y recompilar automáticamente config/rutas_cache.php.
 *
 * Precondición: este controlador sólo es alcanzable si el Middleware validó
 * correctamente el Split Token (cookie axe_auth), por lo que
 * $usuario_autenticado_id siempre debe tener un valor entero válido aquí.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Recuperar el ID de usuario inyectado por el Middleware ─────────────────
// La variable es declarada en el ámbito global por el enrutador central.
/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

// ── 2. Dependencias ───────────────────────────────────────────────────────────
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// ── 3. Consultar el email del usuario autenticado ─────────────────────────────
try {
    $stmt = $pdo->prepare(
        'SELECT email FROM usuarios WHERE id = :id LIMIT 1'
    );
    $stmt->execute([':id' => (int) $usuario_autenticado_id]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    // Si la consulta falla mostramos la vista con un valor de respaldo seguro.
    $usuario = ['email' => ''];
}

// ── 4. Preparar los datos base para la vista ──────────────────────────────────
// El front controller extrae este arreglo con extract($datos_vista) antes
// de incluir la vista, por lo que cada clave se convierte en una variable local.
$datos_vista = [
    'email_usuario' => $usuario['email'] ?? '',
];

// ── 5. GESTOR DE RUTAS — Escuchar peticiones POST ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── FIX A2: Validar token CSRF antes de procesar cualquier acción ──────────
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $csrf_recibido = (string) filter_input(INPUT_POST, 'csrf_token', FILTER_DEFAULT);
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_recibido)) {
        http_response_code(403);
        exit('Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
    }

    // ── 5a. Captura y Sanitización de campos ──────────────────────────────────

    // URI de la ruta (ej. /cobranza). Se elimina espacio y se fuerza string.
    $uri = trim((string) filter_input(INPUT_POST, 'uri', FILTER_DEFAULT));

    // Ruta de la vista relativa al proyecto (ej. views/cobranza.php).
    $vista = trim((string) filter_input(INPUT_POST, 'vista', FILTER_DEFAULT));

    // Ruta del controlador (puede venir vacío; se trata como NULL en BD).
    $controlador_raw = trim((string) filter_input(INPUT_POST, 'controlador', FILTER_DEFAULT));
    $controlador = ($controlador_raw !== '') ? $controlador_raw : null;

    // requiere_login: se fuerza a 1 o 0 independientemente del valor enviado.
    $requiere_login = filter_input(INPUT_POST, 'requiere_login', FILTER_VALIDATE_INT) === 1 ? 1 : 0;

    // nivel_minimo: se fuerza a entero (0 por defecto si no es numérico).
    $nivel_minimo = (int) filter_input(INPUT_POST, 'nivel_minimo', FILTER_VALIDATE_INT);
    if ($nivel_minimo < 0) {
        $nivel_minimo = 0;
    }

    // ── FIX C2: Validación estricta de las rutas ingresadas ───────────────────
    // Impide path traversal (../../) e inyección de rutas arbitrarias.

    // URI: solo letras, números, guiones y barras. Debe empezar por /
    if (!preg_match('#^/[a-zA-Z0-9/_-]*$#', $uri) || strlen($uri) < 2 || strlen($uri) > 100) {
        $datos_vista['mensaje_gestor'] = '❌ URI inválida. Usa solo letras, números, guiones y barras (ej. /mi-ruta).';
        goto fin_gestor;
    }

    // Vista: debe estar en la carpeta views/ y tener extensión .php
    if (!preg_match('#^views/[a-zA-Z0-9_-]+\.php$#', $vista)) {
        $datos_vista['mensaje_gestor'] = '❌ Ruta de vista inválida. Formato esperado: views/nombre.php';
        goto fin_gestor;
    }

    // Controlador (opcional): debe estar en controllers/ y seguir el patrón NombreController.php
    if ($controlador !== null && !preg_match('#^controllers/[a-zA-Z0-9]+\.php$#', $controlador)) {
        $datos_vista['mensaje_gestor'] = '❌ Ruta de controlador inválida. Formato esperado: controllers/NombreController.php';
        goto fin_gestor;
    }

    // ── 5b. Automatización de Assets ──────────────────────────────────────────
    // Extrae el nombre base de la vista sin extensión (ej. "cobranza").
    $base = pathinfo($vista, PATHINFO_FILENAME);

    // Genera cadenas JSON estrictas para css y js.
    $css_json = '["/assets/css/' . $base . '.css"]';
    $js_json  = '["/assets/js/'  . $base . '.js"]';

    // ── 5c. Inserción en Base de Datos ────────────────────────────────────────
    try {
        $insert = $pdo->prepare(
            'INSERT INTO rutas
                (uri, vista, plantilla, controlador, requiere_login, nivel_minimo, css, js)
             VALUES
                (:uri, :vista, :plantilla, :controlador, :requiere_login, :nivel_minimo, :css, :js)'
        );

        $insert->execute([
            ':uri'           => $uri,
            ':vista'         => $vista,
            ':plantilla'     => 'templates/default.php',
            ':controlador'   => $controlador,
            ':requiere_login' => $requiere_login,
            ':nivel_minimo'  => $nivel_minimo,
            ':css'           => $css_json,
            ':js'            => $js_json,
        ]);

        // ── 5d. Recompilación de Caché ────────────────────────────────────────
        // Recuperar todas las rutas de la tabla para regenerar el archivo cache.
        $select_rutas = $pdo->query(
            'SELECT uri, vista, plantilla, controlador, requiere_login, nivel_minimo, css, js
             FROM rutas
             ORDER BY id ASC'
        );

        $rutas_array = [];

        foreach ($select_rutas as $fila) {
            // Decodificar JSON de css; si falla o está vacío se usa arreglo vacío.
            $css_decoded = json_decode((string) $fila['css'], true);
            $css_final   = is_array($css_decoded) ? $css_decoded : [];

            // Decodificar JSON de js; si falla o está vacío se usa arreglo vacío.
            $js_decoded = json_decode((string) $fila['js'], true);
            $js_final   = is_array($js_decoded) ? $js_decoded : [];

            $rutas_array[$fila['uri']] = [
                'uri'           => $fila['uri'],
                'vista'         => $fila['vista'],
                'plantilla'     => $fila['plantilla'],
                'controlador'   => $fila['controlador'],
                'requiere_login' => (bool) $fila['requiere_login'],
                'nivel_minimo'  => (int) $fila['nivel_minimo'],
                'css'           => $css_final,
                'js'            => $js_final,
            ];
        }

        // Generar contenido PHP del archivo de caché usando var_export().
        $fecha_compilacion = date('Y-m-d H:i:s');
        $export_php        = var_export($rutas_array, true);

        $cache_contenido = <<<PHP
<?php
// Archivo auto-generado por el Compilador de Axe Framework
// Fecha de última compilación: {$fecha_compilacion}

return {$export_php};
PHP;

        // Sobrescribir config/rutas_cache.php con el nuevo contenido.
        file_put_contents(BASE_PATH . '/config/rutas_cache.php', $cache_contenido);

        // ── 5e. Mensaje de éxito ──────────────────────────────────────────────
        $mensaje_gestor = 'Ruta "' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '" creada correctamente y caché recompilada.';

    } catch (PDOException $e) {
        // FIX M2: Loguear error internamente, no exponer al usuario.
        error_log('DashboardController [crear_ruta]: ' . $e->getMessage());
        if ($e->getCode() === '23000') {
            $mensaje_gestor = '❌ La URI ingresada ya existe. Elige una diferente.';
        } else {
            $mensaje_gestor = '❌ Error interno al guardar la ruta. Contacta al administrador.';
        }
    }

    // Añadir el mensaje al arreglo de datos de la vista.
    $datos_vista['mensaje_gestor'] = $mensaje_gestor ?? null;

    fin_gestor: // Etiqueta de salida rápida para validaciones fallidas
}
