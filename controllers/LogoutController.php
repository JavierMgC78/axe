<?php

declare(strict_types=1);

/**
 * controllers/LogoutController.php
 *
 * CONTROLADOR DE CIERRE DE SESIÓN — AXE FRAMEWORK
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Verificar si existe la cookie axe_auth.
 *   2. Extraer el selector (parte izquierda del Split Token).
 *   3. FIX B2: Eliminar TODOS los tokens del usuario (logout global),
 *      invalidando sesiones en otros dispositivos/navegadores.
 *   4. Destruir la cookie en el navegador.
 *   5. Destruir la sesión PHP (donde se almacena el token CSRF).
 *   6. Redirigir al usuario a /login.
 *
 * Patrón Split Token (referencia):
 *   Cookie → selector|validador_claro   (FIX M1: separador centralizado)
 *   BD     → selector (índice único), hash SHA-256 del validador_claro
 * ─────────────────────────────────────────────────────────────────────────────
 */

require_once BASE_PATH . '/core/Seguridad.php';

// ── 1. Verificar existencia de la cookie axe_auth ────────────────────────────
if (isset($_COOKIE['axe_auth']) && $_COOKIE['axe_auth'] !== '') {

    // ── 2. FIX M1: Separar el Split Token usando la constante centralizada ────
    $partes_token = explode(Seguridad::TOKEN_SEPARATOR, $_COOKIE['axe_auth'], 2);
    $selector     = $partes_token[0] ?? '';

    // ── 3. FIX B2: Logout global — invalidar TODOS los tokens del usuario ─────
    // Primero se obtiene el usuario_id del selector actual,
    // luego se eliminan TODOS sus tokens para cerrar sesión en todos los
    // dispositivos/navegadores donde tenga sesión activa.
    if ($selector !== '') {
        /** @var PDO $pdo */
        $pdo = require BASE_PATH . '/config/database.php';

        try {
            // Obtener el usuario_id del selector actual
            $stmt_user = $pdo->prepare(
                'SELECT usuario_id FROM auth_tokens WHERE selector = ? LIMIT 1'
            );
            $stmt_user->execute([$selector]);
            $fila = $stmt_user->fetch(PDO::FETCH_ASSOC);

            if ($fila && isset($fila['usuario_id'])) {
                // Eliminar TODOS los tokens del usuario (logout global)
                $pdo->prepare('DELETE FROM auth_tokens WHERE usuario_id = ?')
                    ->execute([(int) $fila['usuario_id']]);
            } else {
                // Fallback: si no se encuentra el usuario, borrar solo este token
                $pdo->prepare('DELETE FROM auth_tokens WHERE selector = ?')
                    ->execute([$selector]);
            }
        } catch (PDOException $e) {
            error_log('LogoutController: ' . $e->getMessage());
            // Si la consulta falla no bloqueamos el logout; el token
            // eventualmente expirará. El usuario debe salir de todas formas.
        }
    }
}

// ── 4. Destruir la cookie axe_auth en el navegador ───────────────────────────
// Se usa el mismo flag secure condicional que en el login.
$es_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
setcookie('axe_auth', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'secure'   => $es_https,
    'httponly' => true,
    'samesite' => 'Strict',
]);

// ── 5. Destruir la sesión PHP (token CSRF) ────────────────────────────────────
// FIX A2: Al hacer logout se invalida también el token CSRF de la sesión.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_destroy();

// ── 6. Redirigir al usuario a la página de login ──────────────────────────────
header('Location: /login');
exit;
