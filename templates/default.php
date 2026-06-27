<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Axe Framework</title>

    <style>
        /* ── Reset y base ────────────────────────────────────────────────── */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-bg:       #0f1117;
            --color-surface:  #1a1d27;
            --color-border:   rgba(255, 255, 255, 0.08);
            --color-accent:   #6c63ff;
            --color-accent-h: #857dff;
            --color-text:     #e2e4ed;
            --color-muted:    #8b8fa8;
            --nav-height:     64px;
            --radius:         8px;
            --transition:     0.22s ease;
            --font:           'Inter', system-ui, -apple-system, sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font);
            background-color: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
        }

        /* ── Barra de Navegación ─────────────────────────────────────────── */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--nav-height);
            background: rgba(26, 29, 39, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.35);
        }

        .nav-inner {
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }

        /* Logo / marca */
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--color-text);
            transition: opacity var(--transition);
        }

        .nav-brand:hover { opacity: 0.8; }

        .nav-brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--color-accent), #a78bfa);
            font-size: 0.85rem;
            font-weight: 900;
            color: #fff;
        }

        /* Lista de enlaces */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .nav-links a {
            display: inline-block;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-muted);
            text-decoration: none;
            transition: color var(--transition), background var(--transition);
        }

        .nav-links a:hover {
            color: var(--color-text);
            background: rgba(255, 255, 255, 0.06);
        }

        /* Zona de acciones (botones) */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Botón base */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1.1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background var(--transition), color var(--transition),
                        border-color var(--transition), box-shadow var(--transition);
        }

        /* Botón: enlace de panel */
        .btn-ghost {
            color: var(--color-muted);
            border-color: var(--color-border);
            background: transparent;
        }

        .btn-ghost:hover {
            color: var(--color-text);
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Botón: acción principal (login / logout) */
        .btn-primary {
            color: #fff;
            background: var(--color-accent);
            border-color: var(--color-accent);
        }

        .btn-primary:hover {
            background: var(--color-accent-h);
            border-color: var(--color-accent-h);
            box-shadow: 0 0 16px rgba(108, 99, 255, 0.45);
        }

        /* ── Contenido principal ─────────────────────────────────────────── */
        main {
            max-width: 1160px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* ── Responsive ──────────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .nav-links { display: none; }
        }
    </style>

    <?php
    // Inyección de Assets: CSS por vista
    if (!empty($css_vista) && is_array($css_vista)) {
        foreach ($css_vista as $css) {
            echo "<link rel='stylesheet' href='{$css}'>\n";
        }
    }
    ?>
</head>
<body>

    <!-- ══ HEADER / NAVEGACIÓN ══════════════════════════════════════════════ -->
    <header class="site-header">
        <nav class="nav-inner" aria-label="Navegación principal">

            <!-- Marca -->
            <a href="/" class="nav-brand">
                <span class="nav-brand-icon">Ax</span>
                Axe Framework
            </a>

            <!-- Enlac es estáticos -->
            <ul class="nav-links">
                <li><a href="/">Inicio</a></li>
                <li><a href="/nosotros">Nosotros</a></li>
                <li><a href="/acerca-de">Acerca de Axe</a></li>
                <?php if (isset($usuario_autenticado_id)): ?>
                    <li><a href="/dashboard">Panel</a></li>
                <?php endif; ?>
            </ul>

            <!-- Acciones dinámicas según sesión -->
            <div class="nav-actions">
                <?php if (isset($usuario_autenticado_id)): ?>
                    <a href="/logout" class="btn btn-primary">
                        <!-- icono de salida -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Cerrar Sesión
                    </a>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary">
                        <!-- icono de entrada -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>

        </nav>
    </header>
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <main>
        <?php echo $contenido_vista ?? 'Error: Vista no capturada.'; ?>
    </main>

    <?php
    // Inyección de Assets: JavaScript por vista
    if (!empty($js_vista) && is_array($js_vista)) {
        foreach ($js_vista as $js) {
            echo "<script src='{$js}'></script>\n";
        }
    }
    ?>
</body>
</html>