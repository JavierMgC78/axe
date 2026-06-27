-- Estructura base de Axe Framework (con RBAC - Control de Acceso por Roles)

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

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nivel_acceso` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validador_hash` varchar(255) NOT NULL,
  `expiracion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_selector` (`selector`),
  CONSTRAINT `fk_usuario_token` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos por defecto para el enrutador (El Dashboard exige nivel 100)
INSERT IGNORE INTO `rutas` (`uri`, `vista`, `plantilla`, `controlador`, `requiere_login`, `nivel_minimo`) VALUES
('/', 'views/home.php', 'templates/default.php', 'controllers/HomeController.php', 0, 0),
('/login', 'views/login.php', 'templates/default.php', 'controllers/LoginController.php', 0, 0),
('/logout', 'views/home.php', 'templates/default.php', 'controllers/LogoutController.php', 0, 0),
('/dashboard', 'views/dashboard.php', 'templates/default.php', 'controllers/DashboardController.php', 1, 100);