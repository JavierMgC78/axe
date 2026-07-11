<?php
/**
 * views/public/contacto_public.php
 *
 * Vista pública de la página de Contacto.
 * Inyectada por el layout público a través del front controller.
 */
?>

<style>
    /* ── Sección Hero de Contacto ─────────────────────────────────── */
    .ct-hero {
        text-align: center;
        padding: 4rem 1rem 2.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .ct-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.9rem;
        border-radius: 100px;
        border: 1px solid rgba(43, 91, 148, 0.4);
        background: rgba(43, 91, 148, 0.08);
        font-size: 0.78rem;
        font-weight: 600;
        color: #60a5fa;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .ct-title {
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 900;
        letter-spacing: -1px;
        line-height: 1.1;
        background: linear-gradient(135deg, #e2e8f0 30%, #60a5fa 70%, #d92534 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .ct-subtitle {
        font-size: 1.05rem;
        color: #64748b;
        max-width: 500px;
        line-height: 1.65;
    }

    /* ── Grid principal ────────────────────────────────────────────── */
    .ct-grid {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 2rem;
        max-width: 900px;
        margin: 0 auto 3rem;
    }

    @media (max-width: 700px) {
        .ct-grid { grid-template-columns: 1fr; }
    }

    /* ── Panel de info ─────────────────────────────────────────────── */
    .ct-info {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .ct-info-card {
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 1.25rem 1.4rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        transition: border-color 0.2s, transform 0.2s;
    }
    .ct-info-card:hover {
        border-color: rgba(43, 91, 148, 0.3);
        transform: translateY(-2px);
    }

    .ct-info-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(43, 91, 148, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ct-info-body h3 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #e2e8f0;
        margin-bottom: 0.2rem;
    }

    .ct-info-body p {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.5;
    }

    .ct-info-body a {
        color: #60a5fa;
        text-decoration: none;
        transition: color 0.2s;
    }
    .ct-info-body a:hover { color: #93c5fd; text-decoration: underline; }

    /* ── Formulario ────────────────────────────────────────────────── */
    .ct-form-wrap {
        background: rgba(17, 24, 39, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .ct-form {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .ct-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 500px) {
        .ct-form-row { grid-template-columns: 1fr; }
    }

    .ct-field {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .ct-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(226, 232, 240, 0.55);
    }

    .ct-input,
    .ct-textarea {
        width: 100%;
        padding: 0.72rem 0.95rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #e2e8f0;
        font-size: 0.92rem;
        font-family: inherit;
        outline: none;
        transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
        appearance: none;
        resize: vertical;
    }

    .ct-input::placeholder,
    .ct-textarea::placeholder { color: rgba(226, 232, 240, 0.25); }

    .ct-input:focus,
    .ct-textarea:focus {
        border-color: #2b5b94;
        background: rgba(43, 91, 148, 0.08);
        box-shadow: 0 0 0 3px rgba(43, 91, 148, 0.2);
    }

    .ct-textarea { min-height: 130px; }

    /* ── Botón enviar ───────────────────────────────────────────────── */
    .ct-btn-submit {
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #2b5b94, #1e3a5f);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 20px rgba(43, 91, 148, 0.4);
        transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        position: relative;
        overflow: hidden;
    }

    .ct-btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .ct-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(43, 91, 148, 0.6);
        filter: brightness(1.1);
    }
    .ct-btn-submit:hover::before { opacity: 1; }
    .ct-btn-submit:active { transform: translateY(0); }

    .ct-btn-arrow { transition: transform 0.2s; }
    .ct-btn-submit:hover .ct-btn-arrow { transform: translateX(4px); }

    /* ── Mensaje de éxito ───────────────────────────────────────────── */
    .ct-success {
        display: none;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 2rem;
        text-align: center;
        animation: fadeInUp 0.4s ease both;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ct-success-icon {
        font-size: 3rem;
        filter: drop-shadow(0 0 16px rgba(16, 185, 129, 0.6));
        animation: bounceIn 0.5s ease both;
    }

    @keyframes bounceIn {
        0%   { transform: scale(0.5); opacity: 0; }
        70%  { transform: scale(1.15); }
        100% { transform: scale(1);   opacity: 1; }
    }

    .ct-success h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #10b981;
    }

    .ct-success p {
        font-size: 0.88rem;
        color: #64748b;
    }
</style>

<!-- ── Hero ─────────────────────────────────────────────────────────── -->
<section class="ct-hero">
    <span class="ct-badge">📡 Contacto</span>
    <h1 class="ct-title">¿Hablamos?</h1>
    <p class="ct-subtitle">
        Estamos disponibles para cualquier duda, reporte técnico o
        propuesta de colaboración con el equipo de Axe Framework.
    </p>
</section>

<!-- ── Grid: info + formulario ─────────────────────────────────────── -->
<div class="ct-grid">

    <!-- Panel izquierdo: datos de contacto -->
    <aside class="ct-info" aria-label="Información de contacto">

        <div class="ct-info-card">
            <div class="ct-info-icon" aria-hidden="true">✉️</div>
            <div class="ct-info-body">
                <h3>Correo electrónico</h3>
                <p><a href="mailto:hola@axeframework.dev">hola@axeframework.dev</a></p>
            </div>
        </div>

        <div class="ct-info-card">
            <div class="ct-info-icon" aria-hidden="true">🐙</div>
            <div class="ct-info-body">
                <h3>Repositorio</h3>
                <p><a href="https://github.com/axeframework" target="_blank" rel="noopener noreferrer">github.com/axeframework</a></p>
            </div>
        </div>

        <div class="ct-info-card">
            <div class="ct-info-icon" aria-hidden="true">🕐</div>
            <div class="ct-info-body">
                <h3>Tiempo de respuesta</h3>
                <p>Respondemos en un plazo máximo de 48 horas hábiles.</p>
            </div>
        </div>

        <div class="ct-info-card">
            <div class="ct-info-icon" aria-hidden="true">🛡️</div>
            <div class="ct-info-body">
                <h3>Reporte de seguridad</h3>
                <p>Para vulnerabilidades críticas escríbenos directamente a <a href="mailto:security@axeframework.dev">security@axeframework.dev</a></p>
            </div>
        </div>

    </aside>

    <!-- Formulario de contacto -->
    <div class="ct-form-wrap">

        <!-- Estado inicial: formulario -->
        <div id="ct-form-state">
            <form
                id="form-contacto"
                class="ct-form"
                method="POST"
                action="/contacto"
                novalidate
            >
                <div class="ct-form-row">
                    <div class="ct-field">
                        <label class="ct-label" for="ct-nombre">Nombre</label>
                        <input
                            id="ct-nombre"
                            class="ct-input"
                            type="text"
                            name="nombre"
                            placeholder="Tu nombre"
                            required
                            maxlength="100"
                            autocomplete="given-name"
                        >
                    </div>
                    <div class="ct-field">
                        <label class="ct-label" for="ct-email">Correo</label>
                        <input
                            id="ct-email"
                            class="ct-input"
                            type="email"
                            name="email"
                            placeholder="tu@correo.com"
                            required
                            maxlength="255"
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="ct-field">
                    <label class="ct-label" for="ct-asunto">Asunto</label>
                    <input
                        id="ct-asunto"
                        class="ct-input"
                        type="text"
                        name="asunto"
                        placeholder="¿En qué podemos ayudarte?"
                        required
                        maxlength="200"
                    >
                </div>

                <div class="ct-field">
                    <label class="ct-label" for="ct-mensaje">Mensaje</label>
                    <textarea
                        id="ct-mensaje"
                        class="ct-textarea"
                        name="mensaje"
                        placeholder="Escribe tu mensaje aquí…"
                        required
                        maxlength="2000"
                    ></textarea>
                </div>

                <button id="ct-btn-send" class="ct-btn-submit" type="submit">
                    <span class="ct-btn-text">Enviar mensaje</span>
                    <span class="ct-btn-arrow" aria-hidden="true">→</span>
                </button>
            </form>
        </div>

        <!-- Estado de éxito (se muestra al enviar) -->
        <div id="ct-success-state" class="ct-success" aria-live="polite">
            <div class="ct-success-icon" aria-hidden="true">✅</div>
            <h3>¡Mensaje enviado!</h3>
            <p>Gracias por escribirnos. Te responderemos a la brevedad posible.</p>
        </div>

    </div>
</div>

<script>
    (function () {
        'use strict';

        const form         = document.getElementById('form-contacto');
        const btnSend      = document.getElementById('ct-btn-send');
        const btnTxt       = btnSend ? btnSend.querySelector('.ct-btn-text') : null;
        const formState    = document.getElementById('ct-form-state');
        const successState = document.getElementById('ct-success-state');

        if (!form || !btnSend) return;

        form.addEventListener('submit', function (e) {
            // Validación HTML5 nativa
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Feedback visual de carga
            e.preventDefault();
            btnSend.disabled  = true;
            if (btnTxt) btnTxt.textContent = 'Enviando…';

            // Simulación de envío (reemplazar por fetch real cuando exista endpoint)
            setTimeout(function () {
                if (formState && successState) {
                    formState.style.display    = 'none';
                    successState.style.display = 'flex';
                }
            }, 1200);
        });
    })();
</script>
