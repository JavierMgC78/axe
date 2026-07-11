<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Axe Framework</title>
    <link rel="stylesheet" href="../templates/layoutAdmin.css">

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
            </ul>

            <!-- Acciones dinámicas según sesión -->
            <div class="nav-actions">
                <?php if (isset($_SESSION['usuario_id'])): /* Ajustar si tu variable de sesión tiene otro nombre */ ?>
                    <!-- Usuario logueado: Mostrar botón al Panel -->
                    <a href="/panel" class="btn btn-primary" title="Ir al Panel de Control">
                        <i class="fas fa-cog"></i> Panel
                    </a>
                <?php else: ?>
                    <!-- Visitante: Mostrar botón de Iniciar Sesión -->
                    <a href="/login" class="btn btn-primary" title="Iniciar Sesión">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
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