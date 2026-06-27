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
 *   3. Eliminar el registro correspondiente de la tabla auth_tokens en la BD
 *      para invalidar la sesión de forma segura (sin dejar tokens huérfanos).
 *   4. Destruir la cookie en el navegador.
 *   5. Redirigir al usuario a /login.
 *
 * Patrón Split Token (referencia):
 *   Cookie → selector|validador_claro
 *   BD     → selector (índice único), hash SHA-256 del validador_claro
 *
 * El selector es el único dato necesario para localizar y eliminar el registro.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Verificar existencia de la cookie axe_auth ────────────────────────────
if (isset($_COOKIE['axe_auth']) && $_COOKIE['axe_auth'] !== '') {

    // ── 2. Separar el Split Token para obtener el selector ───────────────────
    // Formato esperado: selector|validador_claro
    $partes_token = explode('|', $_COOKIE['axe_auth'], 2);
    $selector     = $partes_token[0] ?? '';

    // ── 3. Invalidar el token en la base de datos ─────────────────────────────
    // Solo se procede si el selector no está vacío para evitar consultas nulas.
    if ($selector !== '') {
        /** @var PDO $pdo */
        $pdo = require BASE_PATH . '/config/database.php';

        try {
            $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE selector = ?');
            $stmt->execute([$selector]);
        } catch (PDOException $e) {
            // Si la consulta falla no bloqueamos el logout; el token
            // eventualmente expirará. El usuario debe salir de todas formas.
        }
    }
}

// ── 4. Destruir la cookie en el navegador ─────────────────────────────────────
// Se envía con tiempo de expiración en el pasado para que el navegador la elimine.
setcookie('axe_auth', '', time() - 3600, '/');

// ── 5. Redirigir al usuario a la página de login ──────────────────────────────
header('Location: /login');
exit;
