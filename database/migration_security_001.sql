-- ═══════════════════════════════════════════════════════════════════════════════
-- FIX A3: Tabla para Rate Limiting de intentos de login
-- Axe Framework — Migración de Seguridad
-- Fecha: 2026-06-27
-- ═══════════════════════════════════════════════════════════════════════════════
--
-- INSTRUCCIONES:
--   Ejecutar esta migración en la base de datos axe_db una sola vez.
--   Después de ejecutarla, el rate limiting quedará activo automáticamente.
--
--   mysql -u root axe_db < database/migration_security_001.sql
--
-- ═══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ip`         VARCHAR(45)      NOT NULL COMMENT 'IPv4 o IPv6 del cliente',
    `exitoso`    TINYINT(1)       NOT NULL DEFAULT 0 COMMENT '1 = login exitoso, 0 = fallo',
    `creado_en`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ip_fecha` (`ip`, `creado_en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de intentos de autenticación para rate limiting (FIX A3)';

-- Limpieza automática de registros mayores a 30 días (opcional, requiere EVENT scheduler):
-- CREATE EVENT IF NOT EXISTS `purgar_login_attempts`
--   ON SCHEDULE EVERY 1 DAY
--   DO DELETE FROM `login_attempts` WHERE `creado_en` < DATE_SUB(NOW(), INTERVAL 30 DAY);
