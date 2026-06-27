<?php
/**
 * views/login.php
 *
 * Vista del formulario de autenticación.
 * Recibe la variable $error (si existe) inyectada por LoginController.php
 * a través del mecanismo extract() del front controller.
 */
?>

<section class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <div class="login-logo" aria-hidden="true">⬡</div>
            <h1 class="login-title">Axe Framework</h1>
            <p class="login-subtitle">Accede a tu cuenta</p>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="login-alert" role="alert" aria-live="polite">
                <span class="alert-icon" aria-hidden="true">⚠</span>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form
            id="form-login"
            class="login-form"
            method="POST"
            action="/login"
            novalidate
            autocomplete="off"
        >
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <div class="input-wrapper">
                    <span class="input-icon" aria-hidden="true">✉</span>
                    <input
                        id="email"
                        class="form-input"
                        type="email"
                        name="email"
                        placeholder="usuario@ejemplo.com"
                        required
                        autocomplete="email"
                        maxlength="255"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon" aria-hidden="true">🔒</span>
                    <input
                        id="password"
                        class="form-input"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                        maxlength="255"
                    >
                    <button
                        type="button"
                        id="btn-toggle-password"
                        class="btn-toggle-pass"
                        aria-label="Mostrar u ocultar contraseña"
                        title="Mostrar contraseña"
                    >👁</button>
                </div>
            </div>

            <button id="btn-submit-login" class="btn-login" type="submit">
                <span class="btn-text">Ingresar</span>
                <span class="btn-arrow" aria-hidden="true">→</span>
            </button>

        </form>

    </div>

</section>

<style>
    /* ── Reset base ──────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        min-height: 100vh;
        background: radial-gradient(ellipse at 20% 50%, #0f0c29 0%, #302b63 50%, #24243e 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        color: #e2e8f0;
    }

    /* ── Wrapper centrado ────────────────────────────────────── */
    .login-wrapper {
        width: 100%;
        padding: 2rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Tarjeta de cristal ──────────────────────────────────── */
    .login-card {
        width: 100%;
        max-width: 420px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        box-shadow:
            0 8px 32px rgba(0, 0, 0, 0.45),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
        animation: cardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(24px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0)    scale(1);    }
    }

    /* ── Header de la tarjeta ────────────────────────────────── */
    .login-header { text-align: center; margin-bottom: 2rem; }

    .login-logo {
        font-size: 2.8rem;
        line-height: 1;
        margin-bottom: 0.6rem;
        display: block;
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 12px rgba(167, 139, 250, 0.45));
        animation: pulse-glow 3s ease-in-out infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { filter: drop-shadow(0 0 10px rgba(167, 139, 250, 0.4)); }
        50%       { filter: drop-shadow(0 0 20px rgba(167, 139, 250, 0.8)); }
    }

    .login-title {
        font-size: 1.55rem;
        font-weight: 700;
        letter-spacing: -0.025em;
        background: linear-gradient(90deg, #e0c3fc, #8ec5fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.3rem;
    }

    .login-subtitle {
        font-size: 0.875rem;
        color: rgba(226, 232, 240, 0.55);
    }

    /* ── Alerta de error ─────────────────────────────────────── */
    .login-alert {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: #fca5a5;
        animation: shakeX 0.4s ease;
    }

    @keyframes shakeX {
        0%, 100% { transform: translateX(0); }
        20%       { transform: translateX(-6px); }
        40%       { transform: translateX(6px); }
        60%       { transform: translateX(-4px); }
        80%       { transform: translateX(4px); }
    }

    .alert-icon { font-size: 1rem; flex-shrink: 0; }

    /* ── Grupos de campo ─────────────────────────────────────── */
    .login-form { display: flex; flex-direction: column; gap: 1.25rem; }

    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }

    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(226, 232, 240, 0.65);
    }

    /* ── Wrapper de input con ícono ──────────────────────────── */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 0.9rem;
        font-size: 0.95rem;
        pointer-events: none;
        opacity: 0.45;
        transition: opacity 0.2s;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.6rem;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        color: #e2e8f0;
        font-size: 0.95rem;
        font-family: inherit;
        outline: none;
        transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
        appearance: none;
    }

    .form-input::placeholder { color: rgba(226, 232, 240, 0.3); }

    .form-input:focus {
        border-color: #a78bfa;
        background: rgba(167, 139, 250, 0.08);
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.2);
    }

    .form-input:focus ~ .input-icon,
    .input-wrapper:focus-within .input-icon { opacity: 0.75; }

    /* ── Botón toggle de contraseña ──────────────────────────── */
    .btn-toggle-pass {
        position: absolute;
        right: 0.75rem;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        opacity: 0.4;
        transition: opacity 0.2s;
        line-height: 1;
        padding: 0.25rem;
        color: inherit;
    }

    .btn-toggle-pass:hover { opacity: 0.85; }

    /* ── Botón de envío ──────────────────────────────────────── */
    .btn-login {
        margin-top: 0.5rem;
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 20px rgba(124, 58, 237, 0.45);
        transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        letter-spacing: 0.01em;
        position: relative;
        overflow: hidden;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(124, 58, 237, 0.6); filter: brightness(1.08); }
    .btn-login:hover::before { opacity: 1; }
    .btn-login:active { transform: translateY(0); box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4); }

    .btn-arrow { transition: transform 0.2s; }
    .btn-login:hover .btn-arrow { transform: translateX(4px); }
</style>

<script>
    (function () {
        'use strict';

        /* ── Toggle mostrar/ocultar contraseña ── */
        const btnToggle = document.getElementById('btn-toggle-password');
        const inputPass = document.getElementById('password');

        if (btnToggle && inputPass) {
            btnToggle.addEventListener('click', function () {
                const isHidden = inputPass.type === 'password';
                inputPass.type = isHidden ? 'text' : 'password';
                btnToggle.title = isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña';
                btnToggle.textContent = isHidden ? '🙈' : '👁';
            });
        }

        /* ── Feedback visual en submit ── */
        const form   = document.getElementById('form-login');
        const btnSub = document.getElementById('btn-submit-login');
        const btnTxt = btnSub ? btnSub.querySelector('.btn-text') : null;

        if (form && btnSub && btnTxt) {
            form.addEventListener('submit', function () {
                btnSub.disabled  = true;
                btnTxt.textContent = 'Verificando…';
            });
        }
    })();
</script>
