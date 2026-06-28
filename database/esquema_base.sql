-- ==============================================================================
-- Axe Framework - Esquema Base Oficial (Unificado)
-- Motor InnoDB | Codificación utf8mb4
-- ==============================================================================

-- 1. Tabla de Usuarios (Identidades y Control de Acceso)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_acceso` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de Tokens (Arquitectura Split Token)
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `selector` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validador_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiracion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_selector` (`selector`),
  CONSTRAINT `fk_usuario_token` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de Rutas (Motor del Front Controller)
CREATE TABLE IF NOT EXISTS `rutas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vista` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plantilla` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `controlador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requiere_login` tinyint(1) NOT NULL DEFAULT 0,
  `nivel_minimo` int(11) NOT NULL DEFAULT 0,
  `css` json DEFAULT NULL,
  `js` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uri_unique` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla de Defensa contra Fuerza Bruta (Rate Limiting - FIX A3)
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(45) NOT NULL COMMENT 'IPv4 o IPv6 del cliente',
  `exitoso` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = login exitoso, 0 = fallo',
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ip_fecha` (`ip`, `creado_en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de intentos de autenticación para rate limiting (FIX A3)';

-- 5. Bitácora de Auditoría Forense
CREATE TABLE IF NOT EXISTS `bitacora_auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `evento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurso` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_origen` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalles` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- Inserción de Datos Estructurales (Rutas del Core)
-- ==============================================================================
INSERT IGNORE INTO `rutas` (`uri`, `vista`, `plantilla`, `controlador`, `requiere_login`, `nivel_minimo`) VALUES
('/', 'views/home.php', 'templates/default.php', 'controllers/HomeController.php', 0, 0),
('/login', 'views/login.php', 'templates/default.php', 'controllers/LoginController.php', 0, 0),
('/logout', 'views/home.php', 'templates/default.php', 'controllers/LogoutController.php', 0, 0),
('/dashboard', 'views/dashboard.php', 'templates/default.php', 'controllers/DashboardController.php', 1, 100);