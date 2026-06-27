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
 *      a. Buscar al usuario por email en la BD.
 *      b. Verificar la contraseña con Seguridad::verificarPassword().
 *      c. Si las credenciales son válidas, generar un Split Token,
 *         persistirlo en la tabla auth_tokens y enviarlo al cliente
 *         como cookie HttpOnly + Secure.
 *      d. Si las credenciales son inválidas, exponer $error con un mensaje
 *         genérico (no revela qué campo fue incorrecto → anti-enumeración).
 *
 * Patrón Split Token:
 *   Cookie → selector|validador_claro
 *   BD     → selector (índice único), hash SHA-256 del validador_claro
 *
 * El front controller (public/index.php) realiza:
 *   require $ruta_controlador;
 *   if (isset($datos_vista) && is_array($datos_vista)) { extract($datos_vista); }
 *
 * Por eso $error debe incluirse dentro de $datos_vista para que la
 * vista la reciba correctamente.
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

// ── 3. Búsqueda del usuario por email ─────────────────────────────────────────
try {
    $stmt = $pdo->prepare(
        'SELECT id, password_hash FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    // Error de BD: mensaje genérico al usuario, no se filtra información interna.
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 4. Verificación de contraseña ─────────────────────────────────────────────
// Mensaje idéntico tanto si el usuario no existe como si la contraseña falla
// → mitiga ataques de enumeración de usuarios.
if (
    $usuario === false ||
    !Seguridad::verificarPassword($password, (string) $usuario['password_hash'])
) {
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 5. Credenciales correctas: generar Split Token ────────────────────────────
try {
    $token = Seguridad::generarSplitToken();
} catch (\Random\RandomException $e) {
    // Si el CSPRNG falla, no se puede autenticar de forma segura.
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

$selector        = $token['selector'];
$validador_claro = $token['validador_claro'];
$validador_hash  = $token['validador_hash'];

// ── 6. Fecha de expiración (30 días) ──────────────────────────────────────────
$segundos_expiracion = 30 * 24 * 60 * 60;                         // 2 592 000 s
$timestamp_expiracion = time() + $segundos_expiracion;
$fecha_expiracion_db  = date('Y-m-d H:i:s', $timestamp_expiracion);

// ── 7. Persistir el token en la base de datos ─────────────────────────────────
try {
    $insert = $pdo->prepare(
        'INSERT INTO auth_tokens (usuario_id, selector, validador_hash, expiracion)
         VALUES (:usuario_id, :selector, :validador_hash, :expiracion)'
    );
    $insert->execute([
        ':usuario_id'    => (int) $usuario['id'],
        ':selector'      => $selector,
        ':validador_hash' => $validador_hash,
        ':expiracion'    => $fecha_expiracion_db,
    ]);
} catch (PDOException $e) {
    // Fallo al persistir: no dejamos autenticar al usuario si no podemos
    // guardar el token (integridad del sistema de sesiones).
    $datos_vista = ['error' => 'Credenciales incorrectas.'];
    return;
}

// ── 8. Componer el valor de la cookie (Split Token para el cliente) ────────────
// Formato: selector|validador_claro
// El servidor reconoce la sesión buscando el selector en la BD y
// verificando el validador_claro contra el validador_hash almacenado.
$cookie_token = $selector . '|' . $validador_claro;

// ── 9. Enviar cookie segura al navegador ──────────────────────────────────────
setcookie(
    'axe_auth',           // Nombre de la cookie
    $cookie_token,        // Valor: selector|validador_claro
    [
        'expires'   => $timestamp_expiracion,
        'path'      => '/',
        'domain'    => '',      // Dominio actual
        'secure'    => false,    // Sólo HTTPS
        'httponly'  => true,    // Inaccesible desde JavaScript
        'samesite'  => 'Strict' // Protección CSRF adicional
    ]
);

// ── 10. Redirección post-login ────────────────────────────────────────────────
header('Location: /');
exit;
