<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Área Segura | Axe</title>
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top Bar ── */
        .topbar {
            background: linear-gradient(135deg, #1a1d2e 0%, #16213e 100%);
            border-bottom: 1px solid rgba(99, 102, 241, 0.25);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-brand .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -1px;
        }

        .topbar-brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(90deg, #818cf8, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.02em;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-user span {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.6);
            color: #fca5a5;
            transform: translateY(-1px);
        }

        /* ── Main Layout ── */
        .main-wrapper {
            flex: 1;
            padding: 2rem;
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── Welcome Section ── */
        .welcome-section {
            margin-bottom: 1.75rem;
        }

        .welcome-section h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.4rem;
        }

        .welcome-section h2 span {
            color: #818cf8;
        }

        .welcome-section p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.6rem;
            padding: 0.3rem 0.75rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 20px;
            font-size: 0.78rem;
            color: #34d399;
            font-weight: 500;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #34d399;
            border-radius: 50%;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        /* ── System Alert ── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.75rem;
            font-size: 0.88rem;
            font-weight: 500;
            line-height: 1.5;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0);     }
        }

        .alert-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        /* ── Card ── */
        .card {
            background: linear-gradient(145deg, #1a1d2e, #161927);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.12);
        }

        .card-header-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(139, 92, 246, 0.25));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .card-header-text h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #e2e8f0;
        }

        .card-header-text p {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.1rem;
        }

        /* ── Form Grid ── */
        .form-section-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6366f1;
            margin-bottom: 0.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .form-grid.cols-1 {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group.span-2 {
            grid-column: span 2;
        }

        label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        label .required-mark {
            color: #f87171;
            font-size: 0.75rem;
        }

        label .optional-badge {
            font-size: 0.68rem;
            font-weight: 500;
            color: #475569;
            background: rgba(71, 85, 105, 0.2);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            letter-spacing: 0.04em;
        }

        input[type="text"],
        input[type="number"] {
            background: rgba(15, 17, 23, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 9px;
            padding: 0.65rem 0.9rem;
            color: #e2e8f0;
            font-size: 0.88rem;
            font-family: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            outline: none;
            width: 100%;
        }

        input[type="text"]::placeholder,
        input[type="number"]::placeholder {
            color: #334155;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: rgba(99, 102, 241, 0.6);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
            background: rgba(15, 17, 23, 0.9);
        }

        /* ── Divider ── */
        .form-divider {
            border: none;
            border-top: 1px dashed rgba(99, 102, 241, 0.15);
            margin: 1.25rem 0 1.5rem;
        }

        /* ── Checkbox Row ── */
        .checkbox-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            cursor: pointer;
        }

        .checkbox-item input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(99, 102, 241, 0.4);
            border-radius: 5px;
            background: rgba(15, 17, 23, 0.6);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
        }

        .checkbox-item input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-color: #6366f1;
        }

        .checkbox-item input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 900;
        }

        .checkbox-item label {
            font-size: 0.88rem;
            color: #cbd5e1;
            cursor: pointer;
            font-weight: 500;
            margin: 0;
        }

        /* ── Submit Button ── */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
            letter-spacing: 0.02em;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 6px 22px rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .btn-submit svg {
            width: 16px;
            height: 16px;
        }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .topbar { padding: 0.9rem 1.1rem; }
            .main-wrapper { padding: 1.25rem 1rem; }
            .card { padding: 1.25rem; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.span-2 { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <!-- ══════════════ TOP BAR ══════════════ -->
    <header class="topbar">
        <div class="topbar-brand">
            <div class="logo-icon">Ax</div>
            <h1>Axe Framework</h1>
        </div>
        <div class="topbar-user">
            <span>⚙️ Panel de Control</span>
            <a href="/logout" class="btn-logout">
                <!-- Icono de salida -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 12H9m0 0l3-3m-3 3l3 3"/></svg>
                Cerrar sesión
            </a>
        </div>
    </header>

    <!-- ══════════════ CONTENIDO PRINCIPAL ══════════════ -->
    <main class="main-wrapper">

        <!-- Bienvenida -->
        <section class="welcome-section">
            <h2>Bienvenido, <span><?= htmlspecialchars($email_usuario ?? 'Usuario') ?></span></h2>
            <p>Esta es una zona protegida. Si estás viendo esto, el Middleware de validación criptográfica funcionó correctamente.</p>
            <div class="status-badge">Sesión autenticada</div>
            <div style="margin-top: 20px; display: flex; gap: 15px;">
                <a href="/usuarios" style="background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; transition: background 0.3s ease;">
                    👥 Gestor de Usuarios
                </a>
            </div>
        </section>

        <!-- ── Alerta del Sistema (condicional) ── -->
        <?php if (isset($mensaje_gestor)) : ?>
            <?php
                // Determinar si es éxito o error según el prefijo de la clave
                $es_error = isset($tipo_gestor) && $tipo_gestor === 'error';
                $clase_alerta = $es_error ? 'alert-error' : 'alert-success';
                $icono_alerta = $es_error ? '✗' : '✓';
            ?>
            <div class="alert <?= $clase_alerta ?>" role="alert" aria-live="polite">
                <span class="alert-icon"><?= $icono_alerta ?></span>
                <span><?= htmlspecialchars($mensaje_gestor) ?></span>
            </div>
        <?php endif; ?>

        <!-- ── Formulario Gestor de Rutas ── -->
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon">🗺️</div>
                <div class="card-header-text">
                    <h3>Gestor de Rutas</h3>
                    <p>Registra una nueva ruta y compila la caché automáticamente</p>
                </div>
            </div>

            <form method="POST" action="/dashboard" novalidate>

                <!-- CAMPOS OBLIGATORIOS -->
                <p class="form-section-label">Campos obligatorios</p>
                <div class="form-grid">

                    <div class="form-group">
                        <label for="uri">
                            URI
                            <span class="required-mark" aria-label="requerido">*</span>
                        </label>
                        <input
                            type="text"
                            id="uri"
                            name="uri"
                            placeholder="/nueva-ruta"
                            required
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>

                    <div class="form-group">
                        <label for="vista">
                            Vista
                            <span class="required-mark" aria-label="requerido">*</span>
                        </label>
                        <input
                            type="text"
                            id="vista"
                            name="vista"
                            placeholder="views/nueva.php"
                            required
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>

                    <div class="form-group">
                        <label for="nivel_minimo">
                            Nivel mínimo de acceso
                            <span class="required-mark" aria-label="requerido">*</span>
                        </label>
                        <input
                            type="number"
                            id="nivel_minimo"
                            name="nivel_minimo"
                            value="0"
                            min="0"
                            required
                        >
                    </div>

                </div>

                <hr class="form-divider">

                <!-- CAMPOS OPCIONALES -->
                <p class="form-section-label">Campos opcionales</p>
                <div class="form-grid cols-1">

                    <div class="form-group">
                        <label for="controlador">
                            Controlador
                            <span class="optional-badge">Opcional</span>
                        </label>
                        <input
                            type="text"
                            id="controlador"
                            name="controlador"
                            placeholder="controllers/NuevaController.php"
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>

                </div>

                <!-- CASILLAS DE VERIFICACIÓN -->
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input
                            type="checkbox"
                            id="requiere_login"
                            name="requiere_login"
                            value="1"
                        >
                        <label for="requiere_login">Requiere Login</label>
                    </div>
                </div>

                <!-- BOTÓN DE ACCIÓN -->
                <button type="submit" class="btn-submit" id="btn-anadir-ruta">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Añadir Ruta y Compilar
                </button>

            </form>
        </div>

    </main>

</body>
</html>
