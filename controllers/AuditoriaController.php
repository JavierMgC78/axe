<?php

declare(strict_types=1);

/**
 * controllers/AuditoriaController.php
 *
 * MÓDULO DE BITÁCORA DE AUDITORÍA — AXE FRAMEWORK (SOLO LECTURA)
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidades:
 *   1. Leer $usuario_autenticado_id inyectado por el Middleware de autenticación.
 *   2. Consultar la tabla `bitacora_auditoria` con JOIN a `usuarios` para
 *      obtener el email del actor de cada evento.
 *   3. Preparar $datos_vista con los registros para la vista (solo lectura).
 *
 * Precondición: la ruta /auditoria debe estar registrada con
 *   requiere_login = true, nivel_minimo = 100 (SuperAdmin).
 *   El Middleware valida el Split Token y el nivel de acceso antes de llegar
 *   aquí, por lo que $usuario_autenticado_id siempre es un entero válido.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. Capturar el ID de usuario autenticado (inyectado por el Middleware) ────
/** @var int $usuario_autenticado_id */
global $usuario_autenticado_id;

// ── 2. Dependencias ───────────────────────────────────────────────────────────
/** @var PDO $pdo */
$pdo = require BASE_PATH . '/config/database.php';

// ── 3. Consulta forense: últimos 100 eventos con email del actor ───────────────
// Se usa LEFT JOIN para preservar eventos de usuarios eliminados del sistema.
try {
    $sql = "SELECT
                b.id,
                b.evento,
                b.recurso,
                b.detalles,
                b.ip_origen,
                b.fecha,
                u.email AS actor_email
            FROM bitacora_auditoria b
            LEFT JOIN usuarios u ON b.usuario_id = u.id
            ORDER BY b.fecha DESC
            LIMIT 100";

    $stmt    = $pdo->query($sql);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // FIX M2: Error interno; no exponer detalles al usuario autenticado.
    error_log('AuditoriaController — PDOException: ' . $e->getMessage());
    $registros       = [];
    $error_auditoria = 'Error al consultar la bitácora. Contacta al administrador.';
}

// ── 4. Inyección a la Vista ───────────────────────────────────────────────────
// El front controller llama extract($datos_vista) antes de incluir la vista.
$datos_vista = [
    'registros'       => $registros,
    'error_auditoria' => $error_auditoria ?? null,
];
