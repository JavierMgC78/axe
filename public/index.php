<?php
define('BASE_PATH', dirname(__DIR__));
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$rutas = require BASE_PATH . '/config/rutas_cache.php';

if (!array_key_exists($uri, $rutas)) {
    http_response_code(404);
    exit("404 - Ruta no encontrada.");
}

$ruta_activa = $rutas[$uri];

// 5. Middleware: Autenticación, Autorización e Identidad Global
$usuario_autenticado_id = null;
$usuario_nivel = 0;
$usuario_activo = 0;

if (isset($_COOKIE['axe_auth'])) {
    require_once BASE_PATH . '/config/database.php';
    require_once BASE_PATH . '/core/Seguridad.php';
    
    $partes_cookie = explode('|', $_COOKIE['axe_auth']);
    if (count($partes_cookie) === 2) {
        $selector = $partes_cookie[0];
        $validador_claro = $partes_cookie[1];
        
        // JOIN para traer la jerarquía y el estatus del usuario
        $sql = "SELECT t.usuario_id, t.validador_hash, t.expiracion, u.nivel_acceso, u.activo 
                FROM auth_tokens t 
                JOIN usuarios u ON t.usuario_id = u.id 
                WHERE t.selector = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$selector]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($registro && $registro['expiracion'] > date('Y-m-d H:i:s')) {
            if (Seguridad::verificarToken($registro['validador_hash'], $validador_claro)) {
                $usuario_autenticado_id = $registro['usuario_id'];
                $usuario_nivel = (int) $registro['nivel_acceso'];
                $usuario_activo = (int) $registro['activo'];
            }
        }
    }
}

// B. Aduana de Acceso y Autorización Jerárquica
if (isset($ruta_activa['requiere_login']) && $ruta_activa['requiere_login'] == 1) {
    
    // Filtro 1: Identidad
    if (!$usuario_autenticado_id) {
        setcookie('axe_auth', '', time() - 3600, '/');
        header("Location: /login");
        exit;
    }
    
    // Filtro 2: Estatus (¿Está suspendido?)
    if ($usuario_activo !== 1) {
        http_response_code(403);
        $titulo_error = "Cuenta Suspendida";
        $mensaje_error = "Tu acceso al sistema ha sido revocado. Por favor, contacta a coordinación para más detalles.";
        require BASE_PATH . '/views/403.php';
        exit;
    }
    
    // Filtro 3: Jerarquía Matemática
    $nivel_requerido = isset($ruta_activa['nivel_minimo']) ? (int) $ruta_activa['nivel_minimo'] : 0;
    if ($usuario_nivel < $nivel_requerido) {
        http_response_code(403);
        $titulo_error = "Acceso Restringido";
        $mensaje_error = "Esta área requiere un nivel de autorización superior (Nivel $nivel_requerido). Tu nivel actual es $usuario_nivel.";
        require BASE_PATH . '/views/403.php';
        exit;
    }
}

// 6. Carga de vistas y controladores
$ruta_vista = BASE_PATH . '/' . $ruta_activa['vista'];
$ruta_plantilla = BASE_PATH . '/' . $ruta_activa['plantilla'];

if (isset($ruta_activa['controlador']) && !empty($ruta_activa['controlador'])) {
    $ruta_controlador = BASE_PATH . '/' . $ruta_activa['controlador'];
    require $ruta_controlador;
    if (isset($datos_vista) && is_array($datos_vista)) {
        extract($datos_vista);
    }
}

ob_start();
require $ruta_vista;
$contenido_vista = ob_get_clean();

$css_vista = $ruta_activa['css'] ?? [];
$js_vista = $ruta_activa['js'] ?? [];

require $ruta_plantilla;