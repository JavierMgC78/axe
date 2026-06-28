<?php
// ── LÓGICA DE INSTALACIÓN ─────────────────────────────────────────────────────
// Este archivo opera de forma autónoma: no depende del bootstrap del framework.
// Se ejecuta únicamente cuando config.php no existe en la raíz del proyecto.

$install_error   = null;
$install_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Captura de todos los datos del formulario ──────────────────────────────
    $db_host     = trim($_POST['db_host']     ?? 'localhost');
    $db_name     = trim($_POST['db_name']     ?? '');
    $db_user     = trim($_POST['db_user']     ?? 'root');
    $db_pass     = trim($_POST['db_pass']     ?? '');
    $app_name    = trim($_POST['app_name']    ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_pass  = trim($_POST['admin_pass']  ?? '');

    // ── Validación mínima de campos requeridos ─────────────────────────────────
    if (empty($db_name) || empty($app_name)) {
        $install_error = 'Los campos <strong>Nombre de la Base de Datos</strong> y <strong>Nombre de la Aplicación</strong> son obligatorios.';
    } elseif (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $install_error = 'Ingresa un <strong>correo electrónico válido</strong> para el Super Administrador.';
    } elseif (empty($admin_pass) || strlen($admin_pass) < 8) {
        $install_error = 'La <strong>contraseña del administrador</strong> debe tener al menos 8 caracteres.';
    } else {
        // ── Conexión PDO activa (se conserva para inyectar el esquema) ─────────
        try {
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT            => 5,
            ]);

            // ── Ejecución del esquema maestro de base de datos ────────────────
            // Se lee el archivo SQL canónico del framework y se ejecuta íntegro.
            $ruta_esquema = __DIR__ . '/../database/esquema_base.sql';
            $sql = file_get_contents($ruta_esquema);
            $pdo->exec($sql);

            // ── Inyección del Super Administrador ─────────────────────────────
            // nivel_acceso = 100 otorga permisos de Super Admin en el enrutador.
            $admin_pass_hash = password_hash($admin_pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO `usuarios` (`email`, `password_hash`, `nivel_acceso`, `activo`)
                 VALUES (?, ?, 100, 1)"
            );
            $stmt->execute([$admin_email, $admin_pass_hash]);

            // ── Generación del archivo config.php ─────────────────────────────
            // La ruta apunta a la raíz del proyecto, un nivel arriba de /views.
            $config_path = dirname(__DIR__) . '/config.php';

            // Se sanean los valores para su escritura literal como constantes PHP.
            $safe_host     = addslashes($db_host);
            $safe_name     = addslashes($db_name);
            $safe_user     = addslashes($db_user);
            $safe_pass     = addslashes($db_pass);
            $safe_app_name = addslashes($app_name);

            $install_date  = date('Y-m-d H:i:s');

            $config_content  = "<?php\n";
            $config_content .= "// config.php — Generado automáticamente por el instalador de Axe Framework.\n";
            $config_content .= "// Fecha de instalación: {$install_date}\n";
            $config_content .= "// ¡NO edites este archivo manualmente a menos que sepas lo que haces!\n\n";
            $config_content .= "define('DB_HOST',  '{$safe_host}');\n";
            $config_content .= "define('DB_NAME',  '{$safe_name}');\n";
            $config_content .= "define('DB_USER',  '{$safe_user}');\n";
            $config_content .= "define('DB_PASS',  '{$safe_pass}');\n";
            $config_content .= "define('APP_NAME', '{$safe_app_name}');\n";

            $bytes_written = file_put_contents($config_path, $config_content);

            if ($bytes_written === false) {
                $install_error = 'La conexión fue exitosa, pero el sistema no pudo escribir <code>config.php</code>. Verifica que el servidor tenga permisos de escritura en el directorio raíz del proyecto.';
            } else {
                $install_success = true;
            }

        } catch (PDOException $e) {
            $install_error = 'Error de conexión con la base de datos: <strong>' . htmlspecialchars($e->getMessage()) . '</strong>';
        }
    }
}

// Re-poblar el formulario con los valores enviados para UX.
$val_host        = htmlspecialchars($_POST['db_host']     ?? 'localhost');
$val_name        = htmlspecialchars($_POST['db_name']     ?? '');
$val_user        = htmlspecialchars($_POST['db_user']     ?? 'root');
$val_app_name    = htmlspecialchars($_POST['app_name']    ?? '');
$val_admin_email = htmlspecialchars($_POST['admin_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación — Axe Framework</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-void:       #060d18;
            --bg-deep:       #0b101a;
            --bg-panel:      #0f1825;
            --bg-input:      #111c2e;
            --border-dim:    #1e2c4a;
            --border-glow:   #2a4070;
            --gold:          #c9a84c;
            --gold-light:    #e8c56e;
            --gold-dim:      rgba(201,168,76,0.15);
            --blue:          #4d8fdb;
            --blue-light:    #6fb3f5;
            --blue-dim:      rgba(77,143,219,0.12);
            --green:         #3ec97a;
            --green-dim:     rgba(62,201,122,0.12);
            --red:           #e05252;
            --red-dim:       rgba(224,82,82,0.12);
            --violet:        #9b6df0;
            --violet-dim:    rgba(155,109,240,0.12);
            --text-primary:  #dce8ff;
            --text-secondary:#7a95c0;
            --text-dim:      #3d5580;
            --font-mono:     "JetBrains Mono", monospace;
            --font-ui:       "Outfit", sans-serif;
        }

        html, body {
            min-height: 100vh;
            background-color: var(--bg-void);
            color: var(--text-primary);
            font-family: var(--font-ui);
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(77,143,219,0.08) 0%, transparent 60%),
                repeating-linear-gradient(0deg, transparent, transparent 39px, rgba(30,44,74,0.4) 40px),
                repeating-linear-gradient(90deg, transparent, transparent 39px, rgba(30,44,74,0.4) 40px);
            background-size: 100% 100%, 40px 40px, 40px 40px;
        }

        .installer-wrapper {
            width: 100%;
            max-width: 520px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            animation: fadeSlideIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .installer-header { text-align: center; }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 1rem;
            border: 1px solid var(--border-dim);
            border-radius: 999px;
            background: var(--bg-panel);
            margin-bottom: 1.2rem;
        }

        .brand-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 6px var(--gold);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1;   box-shadow: 0 0 6px var(--gold); }
            50%       { opacity: 0.4; box-shadow: 0 0 2px var(--gold); }
        }

        .brand-badge span {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-secondary);
        }

        .installer-header h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.1;
            color: var(--text-primary);
        }

        .installer-header h1 span { color: var(--gold); }

        .installer-header p {
            margin-top: 0.6rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .installer-panel {
            background: var(--bg-panel);
            border: 1px solid var(--border-dim);
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .installer-panel::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), var(--blue), transparent);
            opacity: 0.7;
        }

        .corner-tl, .corner-br { position: absolute; width: 14px; height: 14px; }
        .corner-tl { top: 10px; left: 10px; border-top: 1px solid var(--gold); border-left: 1px solid var(--gold); opacity: 0.4; }
        .corner-br { bottom: 10px; right: 10px; border-bottom: 1px solid var(--gold); border-right: 1px solid var(--gold); opacity: 0.4; }

        /* ── Divisor de sección ───────────────────────────────────────────────── */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border-dim);
            margin: 1.25rem 0;
            position: relative;
        }

        .field-section { margin-bottom: 1rem; }

        .section-label {
            font-family: var(--font-mono);
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-dim);
            margin-bottom: 0.75rem;
            padding-left: 0.25rem;
        }

        /* Sección de admin con acento violeta */
        .section-label.admin-label { color: var(--violet); opacity: 0.8; }

        .field-group { display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 1rem; }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; }

        .field label {
            display: block;
            font-family: var(--font-mono);
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
            padding-left: 2px;
        }

        .field label .required-mark { color: var(--gold); margin-left: 2px; }

        .field input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            background: var(--bg-input);
            border: 1px solid var(--border-dim);
            border-radius: 7px;
            color: var(--text-primary);
            font-family: var(--font-mono);
            font-size: 0.82rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            outline: none;
        }

        .field input::placeholder { color: var(--text-dim); }

        .field input:focus {
            border-color: var(--blue);
            background: rgba(77, 143, 219, 0.05);
            box-shadow: 0 0 0 3px rgba(77,143,219,0.1);
        }

        .field input:hover:not(:focus) { border-color: var(--border-glow); }

        /* Inputs del admin con foco violeta */
        .admin-field input:focus {
            border-color: var(--violet);
            background: rgba(155,109,240,0.05);
            box-shadow: 0 0 0 3px rgba(155,109,240,0.12);
        }

        /* Indicador de fortaleza de contraseña */
        .password-strength {
            margin-top: 0.4rem;
            height: 3px;
            border-radius: 999px;
            background: var(--border-dim);
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .strength-hint {
            font-family: var(--font-mono);
            font-size: 0.6rem;
            letter-spacing: 0.06em;
            margin-top: 0.3rem;
            color: var(--text-dim);
            min-height: 0.9rem;
            transition: color 0.3s ease;
        }

        /* Badge nivel_acceso */
        .access-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-family: var(--font-mono);
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            color: var(--violet);
            background: var(--violet-dim);
            border: 1px solid rgba(155,109,240,0.25);
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
            margin-top: 0.75rem;
        }

        .access-badge-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--violet);
            box-shadow: 0 0 5px var(--violet);
            animation: pulse-dot 1.8s ease-in-out infinite;
        }

        .alert {
            border-radius: 8px;
            padding: 0.9rem 1rem;
            font-size: 0.82rem;
            line-height: 1.6;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            border: 1px solid;
            animation: alertIn 0.3s ease both;
            margin-bottom: 1.25rem;
        }

        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

        .alert-error {
            background: var(--red-dim);
            border-color: rgba(224,82,82,0.3);
            color: #f09090;
        }

        .alert-error code {
            background: rgba(224,82,82,0.15);
            padding: 1px 5px;
            border-radius: 4px;
            font-family: var(--font-mono);
            font-size: 0.78rem;
        }

        .btn-install {
            width: 100%;
            padding: 0.85rem;
            margin-top: 0.5rem;
            background: transparent;
            border: 1px solid var(--gold);
            border-radius: 8px;
            color: var(--gold);
            font-family: var(--font-ui);
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            position: relative;
            overflow: hidden;
            transition: color 0.25s ease, box-shadow 0.25s ease;
        }

        .btn-install::before {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--gold-dim);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .btn-install:hover::before { transform: scaleX(1); }

        .btn-install:hover {
            color: var(--gold-light);
            box-shadow: 0 0 20px rgba(201,168,76,0.2);
        }

        .btn-install:active { transform: scale(0.98); }

        .btn-install span { position: relative; z-index: 1; }

        .btn-install svg { position: relative; z-index: 1; }

        /* ── Pantalla de éxito ───────────────────────────────────────────────── */
        .success-screen {
            text-align: center;
            padding: 1rem 0;
            animation: fadeSlideIn 0.5s ease both;
        }

        .success-icon-ring {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 2px solid var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 24px rgba(62,201,122,0.25), inset 0 0 12px rgba(62,201,122,0.08);
            animation: ringPulse 2.5s ease-in-out infinite;
        }

        @keyframes ringPulse {
            0%, 100% { box-shadow: 0 0 24px rgba(62,201,122,0.25), inset 0 0 12px rgba(62,201,122,0.08); }
            50%       { box-shadow: 0 0 40px rgba(62,201,122,0.4),  inset 0 0 18px rgba(62,201,122,0.15); }
        }

        .success-icon-ring svg {
            width: 32px; height: 32px;
            stroke: var(--green);
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .success-screen h2 { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; }

        .success-screen p { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.35rem; }

        .success-screen code {
            font-family: var(--font-mono);
            font-size: 0.78rem;
            background: var(--green-dim);
            color: var(--green);
            padding: 2px 7px;
            border-radius: 4px;
            border: 1px solid rgba(62,201,122,0.2);
        }

        .success-admin-card {
            margin: 1rem auto 0;
            padding: 0.85rem 1.2rem;
            background: var(--violet-dim);
            border: 1px solid rgba(155,109,240,0.25);
            border-radius: 8px;
            font-size: 0.82rem;
            color: var(--text-secondary);
            text-align: left;
            max-width: 300px;
        }

        .success-admin-card .card-title {
            font-family: var(--font-mono);
            font-size: 0.63rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--violet);
            margin-bottom: 0.5rem;
        }

        .success-admin-card .card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            padding: 0.2rem 0;
        }

        .success-admin-card .card-key {
            font-family: var(--font-mono);
            font-size: 0.7rem;
            color: var(--text-dim);
        }

        .success-admin-card .card-val {
            font-family: var(--font-mono);
            font-size: 0.75rem;
            color: var(--violet);
            background: rgba(155,109,240,0.12);
            padding: 1px 6px;
            border-radius: 4px;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-launch {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 1.75rem;
            padding: 0.8rem 2rem;
            background: var(--blue-dim);
            border: 1px solid var(--blue);
            border-radius: 8px;
            color: var(--blue-light);
            font-family: var(--font-ui);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.03em;
            transition: background 0.25s ease, box-shadow 0.25s ease, color 0.25s ease;
        }

        .btn-launch:hover {
            background: rgba(77,143,219,0.2);
            box-shadow: 0 0 24px rgba(77,143,219,0.3);
            color: #fff;
        }

        .installer-footer {
            text-align: center;
            font-family: var(--font-mono);
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            color: var(--text-dim);
        }

        .installer-footer a { color: var(--text-secondary); text-decoration: none; transition: color 0.2s; }
        .installer-footer a:hover { color: var(--gold); }
    </style>
</head>
<body>

<div class="installer-wrapper">

    <header class="installer-header">
        <div class="brand-badge">
            <span class="brand-dot"></span>
            <span>Axe Framework — Setup Wizard</span>
        </div>
        <h1>Instalación del <span>Sistema</span></h1>
        <p>Configura la base de datos y el Super Administrador para completar el despliegue.<br>
           El archivo <code style="font-family:var(--font-mono);font-size:0.78rem;color:var(--gold);">config.php</code> será generado automáticamente.</p>
    </header>

    <div class="installer-panel">
        <div class="corner-tl"></div>
        <div class="corner-br"></div>

        <?php if ($install_success): ?>

        <div class="success-screen">
            <div class="success-icon-ring">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h2>Instalación Completada</h2>
            <p>La conexión fue verificada y <code>config.php</code> fue generado.</p>
            <p style="margin-top:0.6rem;">Las tablas del sistema fueron creadas:</p>
            <p style="margin-top:0.25rem;">
                <code>usuarios</code> &nbsp;&amp;&nbsp; <code>bitacora_forense</code>
            </p>

            <div class="success-admin-card">
                <div class="card-title">▸ Super Administrador registrado</div>
                <div class="card-row">
                    <span class="card-key">email</span>
                    <span class="card-val" title="<?= htmlspecialchars($admin_email) ?>"><?= htmlspecialchars($admin_email) ?></span>
                </div>
                <div class="card-row">
                    <span class="card-key">nivel_acceso</span>
                    <span class="card-val">100 — SUPER ADMIN</span>
                </div>
            </div>

            <p style="margin-top:0.75rem; font-size:0.8rem; color:var(--text-dim);">
                El instalador se desactivará automáticamente en el próximo acceso.
            </p>
            <a href="/" class="btn-launch">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
                Iniciar Sistema
            </a>
        </div>

        <?php else: ?>

        <?php if ($install_error): ?>
        <div class="alert alert-error">
            <span class="alert-icon">⚠</span>
            <div><?= $install_error ?></div>
        </div>
        <?php endif; ?>

        <form method="POST" action="?action=install" id="install-form" autocomplete="off">

            <!-- // 01 — Aplicación ──────────────────────────────────────────── -->
            <div class="field-section">
                <div class="section-label">// 01 — Aplicación</div>
                <div class="field-group">
                    <div class="field">
                        <label for="app_name">Nombre de la Aplicación <span class="required-mark">*</span></label>
                        <input type="text" id="app_name" name="app_name" value="<?= $val_app_name ?>" placeholder="Mi Sistema Axe" required autofocus>
                    </div>
                </div>
            </div>

            <hr class="section-divider">

            <!-- // 02 — Base de Datos ──────────────────────────────────────── -->
            <div class="field-section">
                <div class="section-label">// 02 — Base de Datos</div>
                <div class="field-group">
                    <div class="field-row">
                        <div class="field">
                            <label for="db_host">Host</label>
                            <input type="text" id="db_host" name="db_host" value="<?= $val_host ?>" placeholder="localhost">
                        </div>
                        <div class="field">
                            <label for="db_name">Nombre de la BD <span class="required-mark">*</span></label>
                            <input type="text" id="db_name" name="db_name" value="<?= $val_name ?>" placeholder="axe_db" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="db_user">Usuario</label>
                            <input type="text" id="db_user" name="db_user" value="<?= $val_user ?>" placeholder="root">
                        </div>
                        <div class="field">
                            <label for="db_pass">Contraseña</label>
                            <input type="password" id="db_pass" name="db_pass" placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="section-divider">

            <!-- // 03 — Super Administrador ───────────────────────────────── -->
            <div class="field-section">
                <div class="section-label admin-label">// 03 — Super Administrador</div>
                <div class="field-group">
                    <div class="field admin-field">
                        <label for="admin_email">Correo Electrónico <span class="required-mark">*</span></label>
                        <input type="email" id="admin_email" name="admin_email"
                               value="<?= $val_admin_email ?>"
                               placeholder="admin@midominio.com" required>
                    </div>
                    <div class="field admin-field">
                        <label for="admin_pass">Contraseña <span class="required-mark">*</span></label>
                        <input type="password" id="admin_pass" name="admin_pass"
                               placeholder="Mínimo 8 caracteres" required
                               minlength="8" autocomplete="new-password">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strength-bar"></div>
                        </div>
                        <div class="strength-hint" id="strength-hint"></div>
                    </div>
                </div>
                <div style="text-align:center;">
                    <span class="access-badge">
                        <span class="access-badge-dot"></span>
                        nivel_acceso = 100 · SUPER ADMIN
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-install" id="btn-install">
                <span>Verificar y Ejecutar Instalación</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>

        </form>
        <?php endif; ?>
    </div>

    <footer class="installer-footer">
        AXE FRAMEWORK &nbsp;·&nbsp; SETUP v2.0 &nbsp;·&nbsp;
        <a href="https://github.com/JavierMgC78/axe" target="_blank" rel="noopener">GitHub</a>
    </footer>

</div>

<script>
    // ── Deshabilitar botón al enviar ─────────────────────────────────────────
    document.getElementById('install-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('btn-install');
        btn.style.opacity = '0.6';
        btn.style.pointerEvents = 'none';
        btn.querySelector('span').textContent = 'Procesando\u2026';
    });

    // ── Indicador de fortaleza de contraseña ─────────────────────────────────
    const passInput  = document.getElementById('admin_pass');
    const strengthBar  = document.getElementById('strength-bar');
    const strengthHint = document.getElementById('strength-hint');

    if (passInput) {
        passInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            let hint  = '';
            let color = '';

            if (val.length >= 8)  score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            if (val.length === 0) {
                strengthBar.style.width = '0%';
                strengthHint.textContent = '';
                return;
            }

            if (score <= 1) {
                color = '#e05252'; hint = '▸ DÉBIL — añade longitud y variedad';
            } else if (score === 2) {
                color = '#e09052'; hint = '▸ REGULAR — incluye mayúsculas o números';
            } else if (score === 3) {
                color = '#c9a84c'; hint = '▸ MODERADA — casi lista';
            } else if (score === 4) {
                color = '#3ec97a'; hint = '▸ FUERTE';
            } else {
                color = '#6fb3f5'; hint = '▸ MUY FUERTE — excelente';
            }

            strengthBar.style.width  = (score * 20) + '%';
            strengthBar.style.background = color;
            strengthHint.style.color     = color;
            strengthHint.textContent     = hint;
        });
    }
</script>

</body>
</html>
