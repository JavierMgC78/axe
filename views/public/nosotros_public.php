<?php
/**
 * views/nosotros_public.php
 * Vista "Nosotros" — zona pública.
 * Fragmento de contenido inyectado por templates/layoutPublic.php
 */
?>

<style>
    /* ── Variables del tema Axe ──────────────────────────────────── */
    /* Heredadas de layoutPublic; se definen aquí por si se carga sola */
    :root {
        --axe-rojo:   #d92534;
        --axe-azul:   #2b5b94;
        --axe-dorado: #f5b700;
        --tarjeta:    #121a2f;
        --borde:      #1e2c4a;
        --texto-sec:  #94a3b8;
        --glow-rojo:  rgba(217, 37, 52, 0.35);
        --glow-azul:  rgba(43, 91, 148, 0.35);
    }

    /* ── Cabecera de sección ────────────────────────────────────── */
    .nos-header {
        text-align: center;
        margin-bottom: 3.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid var(--borde);
    }

    .nos-header h1 {
        font-size: clamp(2rem, 5vw, 2.8rem);
        font-weight: 900;
        letter-spacing: -0.5px;
        text-transform: uppercase;
        background: linear-gradient(90deg, var(--axe-azul), var(--axe-rojo));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .nos-header .nos-subtitulo {
        font-size: 1rem;
        color: var(--texto-sec);
        letter-spacing: 0.5px;
    }

    /* ── Secciones internas ─────────────────────────────────────── */
    .nos-section {
        max-width: 860px;
        margin: 0 auto 3rem;
    }

    .nos-section h2 {
        color: var(--axe-dorado);
        font-size: 1.15rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--axe-rojo);
        padding-left: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .nos-section p {
        color: var(--texto-sec);
        line-height: 1.75;
        font-size: 0.97rem;
    }

    /* ── Grid de pilares ────────────────────────────────────────── */
    .nos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1.2rem;
        margin-top: 1.5rem;
    }

    .nos-pilar {
        background: var(--tarjeta);
        padding: 1.4rem;
        border-radius: 8px;
        border: 1px solid var(--borde);
        position: relative;
        overflow: hidden;
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
    }

    .nos-pilar::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 3px;
        background: var(--axe-azul);
        transition: background 0.22s ease;
    }

    .nos-pilar:hover {
        transform: translateY(-4px);
        border-color: var(--axe-rojo);
        box-shadow: 0 8px 24px var(--glow-rojo);
    }

    .nos-pilar:hover::before { background: var(--axe-rojo); }

    .nos-pilar h3 {
        margin: 0 0 0.5rem;
        color: #fff;
        font-size: 0.95rem;
    }

    .nos-pilar p {
        font-size: 0.85rem;
        color: var(--texto-sec);
        margin: 0;
    }

    /* ── Perfil del arquitecto ──────────────────────────────────── */
    .nos-perfil {
        display: flex;
        align-items: center;
        gap: 2.5rem;
        background: var(--tarjeta);
        padding: 2.5rem;
        border-radius: 10px;
        border: 1px solid var(--borde);
        box-shadow: 0 4px 24px rgba(0,0,0,0.45);
        max-width: 860px;
        margin: 0 auto 3rem;
    }

    .nos-foto {
        flex-shrink: 0;
        width: 220px;
        height: 220px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid var(--axe-azul);
        box-shadow: 0 0 20px var(--glow-azul);
    }

    .nos-foto img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(20%) contrast(115%);
    }

    .nos-info h3 {
        margin: 0 0 0.75rem;
        font-size: 1.5rem;
        color: #fff;
    }

    .nos-info p {
        color: var(--texto-sec);
        line-height: 1.7;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
    }

    /* ── Responsive ─────────────────────────────────────────────── */
    @media (max-width: 680px) {
        .nos-perfil { flex-direction: column; text-align: center; }
        .nos-foto   { width: 180px; height: 180px; }
    }
</style>

<!-- ── Cabecera ──────────────────────────────────────────────────── -->
<div class="nos-header">
    <h1>Axe Framework</h1>
    <p class="nos-subtitulo">Arquitectura PHP de Alto Rendimiento. Control Absoluto.</p>
</div>

<!-- ── Filosofía ────────────────────────────────────────────────── -->
<section class="nos-section">
    <h2>La Filosofía del Sistema</h2>
    <p>Axe Framework nace de la necesidad de escapar del código inflado y las dependencias innecesarias
       de los entornos comerciales masivos. Nuestra arquitectura está diseñada bajo el protocolo
       <strong style="color:#e2e8f0;">«adorno-cero»</strong>: cada línea de código tiene un propósito
       operativo estricto. El objetivo es recuperar el control absoluto sobre la seguridad, la gestión
       de bases de datos y los ciclos de ejecución, garantizando un rendimiento óptimo sin sacrificar
       la flexibilidad estructural.</p>
</section>

<!-- ── Motores ───────────────────────────────────────────────────── -->
<section class="nos-section">
    <h2>Motores de la Arquitectura</h2>
    <div class="nos-grid">
        <div class="nos-pilar">
            <h3>Enrutador Nativo</h3>
            <p>Gestión de URIs limpia y optimizada mediante caché estricta, eliminando la sobrecarga en peticiones.</p>
        </div>
        <div class="nos-pilar">
            <h3>Núcleo de Seguridad</h3>
            <p>Criptografía y patrón Split Token para blindaje total contra suplantación y secuestro de sesiones.</p>
        </div>
        <div class="nos-pilar">
            <h3>Control IAM</h3>
            <p>Gestión de identidades por niveles jerárquicos y aislamiento vertical de privilegios.</p>
        </div>
        <div class="nos-pilar">
            <h3>Auditoría Forense</h3>
            <p>Registro inmutable y silencioso de eventos críticos operando en tiempo real sobre la base de datos.</p>
        </div>
    </div>
</section>

<!-- ── Perfil del arquitecto ─────────────────────────────────────── -->
<div class="nos-perfil">
    <div class="nos-foto">
        <img src="https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80"
             alt="Terminal de servidor">
    </div>
    <div class="nos-info">
        <h3>El Arquitecto del Sistema</h3>
        <p>Desarrollado por <strong style="color:#e2e8f0;">Javier</strong>, profesional en Marketing
           Internacional y programador backend autodidacta. Axe Framework surge de la aplicación cruzada
           entre la gestión de operaciones y la ingeniería de software.</p>
        <p>Con experiencia en la estandarización de procesos de alto volumen, el enfoque arquitectónico
           consiste en construir ecosistemas digitales resilientes, priorizando la precisión de los datos
           y la inquebrantabilidad operativa.</p>
    </div>
</div>
