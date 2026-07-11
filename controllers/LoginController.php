<?php

declare(strict_types=1);

/**
 * controllers/LoginController.php
 *
 * CONTROLADOR DE AUTENTICACIÓN — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Mostrar la vista de login en peticiones GET (sin acción adicional).
 *   2. Procesar credenciales en peticiones POST:
 *      a. FIX A3: Verificar rate limiting por IP (máx. 10 intentos / 15 min).
 *      b. Buscar al usuario por email en la BD.
 *      c. Verificar la contraseña con Seguridad::verificarPassword().
 *      d. Si las credenciales son válidas, generar un Split Token,
 *         persistirlo en la tabla auth_tokens y enviarlo al cliente
 *         como cookie HttpOnly + Secure.
 *      e. FIX B3: Limpiar probabilísticamente tokens expirados.
 *      f. Si las credenciales son inválidas, exponer $error con un mensaje
 *         genérico (no revela qué campo fue incorrecto → anti-enumeración).
 *
 * Patrón Split Token:
 *   Cookie → selector|validador_claro   (FIX M1: separador centralizado)
 *   BD     → selector (índice único), hash SHA-256 del validador_claro
 *
 * FIX C3: La cookie lleva secure=true. En desarrollo local (HTTP) el token
 * funciona igual; en producción (HTTPS) se activa la protección de transporte.
 *
 * FIX A3: Rate limiting — requiere la tabla login_attempts en la BD:
 *   CREATE TABLE login_attempts (
 *     id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     ip         VARCHAR(45)  NOT NULL,
 *     exitoso    TINYINT(1)   NOT NULL DEFAULT 0,
 *     creado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *     INDEX idx_ip_fecha (ip, creado_en)
 *   );
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Sólo procesar lógica POST ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Petición GET: no hay nada que procesar, la vista se renderizará sola.
    return;
}

// ── 1. Extracción y saneamiento de entradas ───────────────────────────────────
$email    = trim((string) filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
$password = trim((string) filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

// Validación básica de presencia
if ($email === '' || $password === '') {
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// Validación de formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 2. Dependencias ───────────────────────────────────────────────────────────
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Seguridad.php';

// ── FIX A3: Rate Limiting por IP ──────────────────────────────────────────────
// Protege contra fuerza bruta y credential stuffing.
// Si la tabla login_attempts no existe (instalación nueva), se degrada
// silenciosamente sin bloquear el flujo normal.
$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
try {
    $stmt_rate = $pdo->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip = ? AND exitoso = 0
         AND creado_en > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
    );
    $stmt_rate->execute([$ip_cliente]);
    $intentos_fallidos = (int) $stmt_rate->fetchColumn();

    if ($intentos_fallidos >= 10) {
        // Delay adicional para ralentizar herramientas automatizadas
        sleep(2);
        $datos_vista = ['error' => 'Demasiados intentos fallidos. Espera 15 minutos antes de reintentar.'];
        return;
    }
} catch (PDOException) {
    // La tabla login_attempts no existe; continuar sin rate limiting.
    // Ejecutar el SQL del docblock para activar esta protección.
}

// ── 3. Búsqueda del usuario por email ─────────────────────────────────────────
try {
    $stmt = $pdo->prepare(
        'SELECT id, password_hash FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    error_log('LoginController [buscar_usuario]: ' . $e->getMessage());
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 4. Verificación de contraseña ─────────────────────────────────────────────
// Mensaje idéntico tanto si el usuario no existe como si la contraseña falla
// → mitiga ataques de enumeración de usuarios.
$credenciales_validas = (
    $usuario !== false &&
    Seguridad::verificarPassword($password, (string) $usuario['password_hash'])
);

// ── FIX A3: Registrar intento ─────────────────────────────────────────────────
try {
    $pdo->prepare("INSERT INTO login_attempts (ip, exitoso) VALUES (?, ?)")
        ->execute([$ip_cliente, $credenciales_validas ? 1 : 0]);
} catch (PDOException) {
    // Tabla no existe; continuar.
}

if (!$credenciales_validas) {
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 5. Credenciales correctas: generar Split Token ────────────────────────────
try {
    $token = Seguridad::generarSplitToken();
} catch (\Random\RandomException $e) {
    error_log('LoginController [generarSplitToken]: ' . $e->getMessage());
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

$selector        = $token['selector'];
$validador_claro = $token['validador_claro'];
$validador_hash  = $token['validador_hash'];

// ── 6. Fecha de expiración (30 días) ──────────────────────────────────────────
$segundos_expiracion  = 30 * 24 * 60 * 60;                         // 2 592 000 s
$timestamp_expiracion = time() + $segundos_expiracion;
$fecha_expiracion_db  = date('Y-m-d H:i:s', $timestamp_expiracion);

// ── FIX B3: Limpieza probabilística de tokens expirados (1% de las veces) ────
// Evita que auth_tokens crezca indefinidamente sin añadir latencia siempre.
if (random_int(1, 100) === 1) {
    try {
        $pdo->exec("DELETE FROM auth_tokens WHERE expiracion < NOW()");
    } catch (PDOException $e) {
        error_log('LoginController [purgar_tokens]: ' . $e->getMessage());
    }
}

// ── 7. Persistir el token en la base de datos ─────────────────────────────────
try {
    $insert = $pdo->prepare(
        'INSERT INTO auth_tokens (usuario_id, selector, validador_hash, expiracion)
         VALUES (:usuario_id, :selector, :validador_hash, :expiracion)'
    );
    $insert->execute([
        ':usuario_id'     => (int) $usuario['id'],
        ':selector'       => $selector,
        ':validador_hash' => $validador_hash,
        ':expiracion'     => $fecha_expiracion_db,
    ]);
} catch (PDOException $e) {
    error_log('LoginController [insertar_token]: ' . $e->getMessage());
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 8. Componer el valor de la cookie (Split Token para el cliente) ────────────
// FIX M1: Se usa la constante centralizada Seguridad::TOKEN_SEPARATOR
$cookie_token = $selector . Seguridad::TOKEN_SEPARATOR . $validador_claro;

// ── 9. FIX C3: Enviar cookie segura al navegador ──────────────────────────────────
// secure: true  → solo viaja por HTTPS (producción).  Cookie no enviada en HTTP.
// secure: false → viaja también por HTTP (desarrollo local sin TLS).
//
// Se detecta automáticamente: en producción (HTTPS) el flag será true;
// en localhost (HTTP) será false. Sin tocar código al hacer deploy.
$es_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

setcookie(
    'axe_auth',           // Nombre de la cookie
    $cookie_token,        // Valor: selector|validador_claro
    [
        'expires'  => $timestamp_expiracion,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $es_https,   // FIX C3: true en HTTPS, false en HTTP local
        'httponly' => true,        // Inaccesible desde JavaScript
        'samesite' => 'Strict',   // Protección CSRF adicional
    ]
);

// ── 10. Redirección post-login ────────────────────────────────────────────────
// El usuario ya está autenticado: lo enviamos directamente al panel de control.
header('Location: /dashboard');
exit;
