<?php
/**
 * Vista: homePublic.php
 * Vista de inicio para visitantes (zona pública).
 * Esta vista es inyectada por layoutPublic.php como fragmento de contenido.
 */
?>

<style>
    /* ── Variables heredadas de layoutPublic ───────────────────────── */
    /* Se usan las mismas custom-properties definidas en la plantilla   */

    /* ── Sección Hero ─────────────────────────────────────────────── */
    .hp-hero {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 5rem 1rem 4rem;
        gap: 1.5rem;
    }

    /* Badge / etiqueta superior */
    .hp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 100px;
        border: 1px solid rgba(245, 183, 0, 0.3);
        background: rgba(245, 183, 0, 0.06);
        font-size: 0.78rem;
        font-weight: 600;
        color: #f5b700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .hp-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #f5b700;
        animation: pulso 2s ease-in-out infinite;
    }
    @keyframes pulso {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.4; transform: scale(0.7); }
    }

    /* Título principal */
    .hp-title {
        font-size: clamp(2.6rem, 6vw, 4.2rem);
        font-weight: 900;
        letter-spacing: -1.5px;
        line-height: 1.08;
        background: linear-gradient(135deg, #e2e8f0 30%, #2b5b94 70%, #d92534 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
    }

    /* Subtítulo */
    .hp-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        max-width: 540px;
        line-height: 1.65;
    }

    /* ── Terminal ─────────────────────────────────────────────────── */
    .hp-terminal {
        background: #05080f;
        border: 1px solid rgba(30, 44, 74, 0.9);
        border-radius: 10px;
        padding: 1.2rem 1.5rem;
        width: 100%;
        max-width: 580px;
        text-align: left;
        box-shadow:
            0 10px 40px rgba(0, 0, 0, 0.7),
            inset 0 1px 0 rgba(255, 255, 255, 0.04);
        position: relative;
    }

    .hp-term-bar {
        display: flex;
        gap: 7px;
        margin-bottom: 1rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid rgba(30, 44, 74, 0.7);
    }

    .hp-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        transition: filter 0.2s;
    }
    .hp-dot.r { background: #d92534; }
    .hp-dot.g { background: #f5b700; }
    .hp-dot.b { background: #2b5b94; }

    .hp-code {
        font-family: 'Courier New', Courier, monospace;
        color: #10b981;
        font-size: 0.9rem;
        line-height: 1.6;
    }
    .hp-code .c  { color: #475569; }
    .hp-code .v  { color: #f5b700; }
    .hp-code .fn { color: #2b5b94; }
    .hp-code .s  { color: #d92534; }

    /* ── Botones de acción ────────────────────────────────────────── */
    .hp-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
    }

    .hp-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.6rem;
        border-radius: 7px;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-decoration: none;
        text-transform: uppercase;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .hp-btn.primary {
        color: #fff;
        background: #d92534;
        border-color: #d92534;
    }
    .hp-btn.primary:hover {
        background: #f03040;
        border-color: #f03040;
        box-shadow: 0 0 20px rgba(217, 37, 52, 0.5);
        transform: translateY(-2px);
    }

    .hp-btn.secondary {
        color: #f5b700;
        background: transparent;
        border-color: rgba(245, 183, 0, 0.4);
    }
    .hp-btn.secondary:hover {
        background: #f5b700;
        color: #0b101a;
        border-color: #f5b700;
        box-shadow: 0 0 18px rgba(245, 183, 0, 0.4);
        transform: translateY(-2px);
    }

    .hp-btn:active { transform: translateY(0) !important; }

    /* ── Separador decorativo ─────────────────────────────────────── */
    .hp-divider {
        width: 100%;
        max-width: 580px;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
        margin: 1rem auto 0;
    }

    /* ── Tarjetas de características ──────────────────────────────── */
    .hp-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.2rem;
        max-width: 800px;
        width: 100%;
        margin: 0 auto 3rem;
    }

    .hp-card {
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        padding: 1.4rem;
        transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    }
    .hp-card:hover {
        border-color: rgba(217, 37, 52, 0.25);
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.4);
    }

    .hp-card-icon {
        font-size: 1.6rem;
        margin-bottom: 0.6rem;
    }

    .hp-card h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #e2e8f0;
        margin-bottom: 0.35rem;
    }

    .hp-card p {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.55;
    }

    /* ── Responsive ───────────────────────────────────────────────── */
    @media (max-width: 600px) {
        .hp-hero { padding: 3.5rem 1rem 2.5rem; }
        .hp-btn  { padding: 0.6rem 1.2rem; font-size: 0.82rem; }
    }
</style>

<!-- ── Hero ────────────────────────────────────────────────────────── -->
<section class="hp-hero">

    <span class="hp-badge">
        <span class="hp-badge-dot"></span>
        Sistema activo y seguro
    </span>

    <h1 class="hp-title">Axe Framework</h1>
    <p> INICIO PUBLICO </P>
    <p class="hp-subtitle">
        Secure Routing System. Arquitectura de adorno-cero construida
        para la velocidad, control absoluto y blindaje forense.
    </p>

    <!-- Terminal decorativa -->
    <div class="hp-terminal" role="img" aria-label="Ejemplo de código de Axe Framework">
        <div class="hp-term-bar" aria-hidden="true">
            <span class="hp-dot r"></span>
            <span class="hp-dot g"></span>
            <span class="hp-dot b"></span>
        </div>
        <div class="hp-code">
            <span class="c">// Inicializando motor de enrutamiento estricto</span><br>
            <span class="v">$router</span>-><span class="fn">get</span>(<span class="s">'/panel'</span>, <span class="s">'Dashboard@index'</span>, [<span class="s">'nivel_minimo'</span> => <span class="v">1</span>]);<br>
            <span class="v">$router</span>-><span class="fn">get</span>(<span class="s">'/auditoria'</span>, <span class="s">'Forense@ver'</span>, [<span class="s">'nivel_minimo'</span> => <span class="v">10</span>]);<br>
            <br>
            <span class="c">// Ejecución sin dependencias externas</span><br>
            <span class="v">$router</span>-><span class="fn">compilar</span>();<br>
            <span class="c">&gt;&gt; Carga completada en 0.012s. Sistema asegurado. ✓</span>
        </div>
    </div>

    <!-- Acciones -->
    <div class="hp-actions">
        <a href="/login" class="hp-btn primary">
            <!-- Candado SVG -->
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Ingresar al Sistema
        </a>
        <a href="/nosotros" class="hp-btn secondary">
            Conocer la Arquitectura
        </a>
    </div>

    <div class="hp-divider" aria-hidden="true"></div>
</section>

<!-- ── Tarjetas de características ─────────────────────────────────── -->
<div class="hp-features">
    <article class="hp-card">
        <div class="hp-card-icon" aria-hidden="true">🔒</div>
        <h3>Autenticación Robusta</h3>
        <p>Sistema de tokens Split-Token con validación jerárquica de niveles de acceso por ruta.</p>
    </article>
    <article class="hp-card">
        <div class="hp-card-icon" aria-hidden="true">⚡</div>
        <h3>Enrutamiento Compilado</h3>
        <p>Rutas pre-compiladas en caché para rendimiento máximo sin overhead en tiempo de ejecución.</p>
    </article>
    <article class="hp-card">
        <div class="hp-card-icon" aria-hidden="true">🛡️</div>
        <h3>Blindaje Forense</h3>
        <p>Registro de auditoría completo, protección CSRF y cabeceras HTTP de seguridad estrictas.</p>
    </article>
</div>
