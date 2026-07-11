<?php
/**
 * views/acerca-de_public.php
 * Vista "Acerca de / System Info" — zona pública.
 * Fragmento de contenido inyectado por templates/layout_public.php
 */
?>

<style>
    /* ── Tipografía monoespaciada para el reporte técnico ──────── */
    .acd-wrap {
        font-family: 'Courier New', Courier, monospace;
        max-width: 800px;
        margin: 0 auto;
    }

    .acd-panel {
        background: rgba(18, 26, 47, 0.85);
        border: 1px solid #1e2c4a;
        border-left: 4px solid #2b5b94;
        padding: 2rem;
        box-shadow: 0 10px 32px rgba(0,0,0,0.5);
        border-radius: 6px;
    }

    .acd-cabecera {
        border-bottom: 1px dashed #1e2c4a;
        padding-bottom: 1.2rem;
        margin-bottom: 1.2rem;
    }

    .acd-cabecera h1 {
        font-family: system-ui, -apple-system, sans-serif;
        color: #f5b700;
        margin: 0 0 0.3rem;
        text-transform: uppercase;
        font-size: 1.8rem;
        letter-spacing: 2px;
    }

    .acd-subtitulo {
        color: #94a3b8;
        font-size: 0.85rem;
        margin: 0;
    }

    /* ── Grid de specs ──────────────────────────────────────────── */
    .acd-specs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
        margin-bottom: 2rem;
    }

    .acd-spec {
        background: rgba(0,0,0,0.3);
        padding: 0.75rem 1rem;
        border: 1px solid #1e2c4a;
        border-radius: 3px;
    }

    .acd-label {
        color: #64748b;
        font-size: 0.78rem;
        text-transform: uppercase;
        display: block;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .acd-value {
        color: #e2e8f0;
        font-size: 1rem;
        font-weight: bold;
    }

    /* ── Lista de módulos ───────────────────────────────────────── */
    .acd-modulos {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem;
        border-top: 1px dashed #1e2c4a;
        padding-top: 1.2rem;
    }

    .acd-modulos h3 {
        font-family: system-ui, -apple-system, sans-serif;
        color: #2b5b94;
        margin: 0 0 0.8rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .acd-modulos li {
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(30, 44, 74, 0.4);
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #94a3b8;
    }

    .estado-ok  { color: #10b981; text-shadow: 0 0 5px rgba(16,185,129,0.4); }
    .estado-err { color: #d92534; }

    /* ── Legal ──────────────────────────────────────────────────── */
    .acd-legal {
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 0.78rem;
        color: #64748b;
        text-align: justify;
        border-top: 1px solid #1e2c4a;
        padding-top: 1.2rem;
        line-height: 1.6;
    }

    @media (max-width: 560px) {
        .acd-specs { grid-template-columns: 1fr; }
    }
</style>

<div class="acd-wrap">
    <div class="acd-panel">

        <div class="acd-cabecera">
            <h1>Axe Core Info</h1>
            <p class="acd-subtitulo">REPORTE DE DIAGNÓSTICO DEL SISTEMA EN TIEMPO REAL</p>
        </div>

        <!-- Especificaciones -->
        <div class="acd-specs">
            <div class="acd-spec">
                <span class="acd-label">Versión del Núcleo</span>
                <span class="acd-value">v1.2.0 (Build: Caucel)</span>
            </div>
            <div class="acd-spec">
                <span class="acd-label">Entorno de Ejecución</span>
                <span class="acd-value">PHP <?= phpversion() ?></span>
            </div>
            <div class="acd-spec">
                <span class="acd-label">Software del Servidor</span>
                <span class="acd-value"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido') ?></span>
            </div>
            <div class="acd-spec">
                <span class="acd-label">Huella de Memoria (RAM)</span>
                <span class="acd-value"><?= round(memory_get_peak_usage(true) / 1024 / 1024, 2) ?> MB</span>
            </div>
            <div class="acd-spec">
                <span class="acd-label">Arquitecto</span>
                <span class="acd-value">Javier</span>
            </div>
            <div class="acd-spec">
                <span class="acd-label">Estampa de Tiempo</span>
                <span class="acd-value"><?= date('Y-m-d H:i:s') ?></span>
            </div>
        </div>

        <!-- Estado de módulos -->
        <ul class="acd-modulos">
            <h3>Estado de Módulos</h3>
            <li>
                <span>Enrutador Nativo (Caché Compilada)</span>
                <span class="estado-ok">[ EN LÍNEA ]</span>
            </li>
            <li>
                <span>Núcleo de Seguridad (Split Token)</span>
                <span class="estado-ok">[ ACTIVO ]</span>
            </li>
            <li>
                <span>Motor IAM (Control de Niveles)</span>
                <span class="estado-ok">[ EN LÍNEA ]</span>
            </li>
            <li>
                <span>Bitácora Forense Base de Datos</span>
                <span class="estado-ok">[ REGISTRANDO ]</span>
            </li>
        </ul>

        <!-- Aviso legal -->
        <div class="acd-legal">
            <strong>Aviso de Privacidad y Licencia:</strong> Axe Framework es una arquitectura propietaria
            desarrollada bajo el protocolo estricto «adorno-cero» para entornos de alta seguridad y control
            de identidades. La copia, distribución o manipulación del código fuente sin autorización del
            arquitecto del sistema está estrictamente prohibida.
        </div>

    </div>
</div>
