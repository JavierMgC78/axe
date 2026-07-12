<?php
/**
 * views/editor-vistas.php
 *
 * VISTA PARCIAL — Editor de Vistas Públicas
 * ─────────────────────────────────────────────────────────────────────────────
 * Inyectada dentro de templates/layoutAdmin.php.
 * NO contiene <html>, <head>, <body> ni estructura de layout.
 *
 * Variables disponibles (via EditorVistasController → extract()):
 *   $vistas_publicas      (array)        — Lista de archivos en views/public/
 *   $archivo_seleccionado (string|null)  — Ruta relativa del archivo a editar
 *   $contenido_archivo    (string|null)  — Contenido leído del archivo
 *   $mensaje_editor       (string|null)  — Feedback de la última operación
 *   $csrf_token           (string)       — Token CSRF de sesión
 */
?>

<!-- ══ ESTILOS DEL EDITOR ════════════════════════════════════════════════════ -->
<style>
    /* ── CodeMirror local ────────────────────────────────────────────────── */
    @import url('/assets/vendor/codemirror/codemirror.min.css');
    @import url('/assets/vendor/codemirror/theme/dracula.min.css');

    /* ── Reset base ──────────────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── Mensaje flash ───────────────────────────────────────────────────── */
    .ev-flash {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .75rem 1.1rem;
        border-radius: 8px;
        font-size: .875rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        animation: slideDown .3s ease;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ev-flash.success {
        background: rgba(52, 211, 153, .1);
        border: 1px solid rgba(52, 211, 153, .3);
        color: #34d399;
    }
    .ev-flash.error {
        background: rgba(248, 113, 113, .1);
        border: 1px solid rgba(248, 113, 113, .3);
        color: #f87171;
    }

    /* ══ MODO LISTA ══════════════════════════════════════════════════════════ */
    .ev-list-header {
        margin-bottom: 1.75rem;
    }
    .ev-list-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f1f5f9;
        margin-bottom: .35rem;
    }
    .ev-list-header p {
        font-size: .875rem;
        color: #64748b;
    }

    .ev-table-wrap {
        background: #141824;
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 12px;
        overflow: hidden;
    }

    .ev-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .ev-table thead tr {
        background: rgba(99,102,241,.08);
        border-bottom: 1px solid rgba(255,255,255,.07);
    }
    .ev-table thead th {
        padding: .85rem 1.2rem;
        text-align: left;
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
    }
    .ev-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,.04);
        transition: background .15s;
    }
    .ev-table tbody tr:last-child { border-bottom: none; }
    .ev-table tbody tr:hover { background: rgba(255,255,255,.03); }
    .ev-table tbody td {
        padding: .9rem 1.2rem;
        color: #e2e8f0;
        vertical-align: middle;
    }

    .ev-filename {
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .ev-file-icon {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        background: rgba(99,102,241,.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .ev-file-name {
        font-weight: 600;
        color: #c7d2fe;
        font-family: 'Courier New', monospace;
        font-size: .82rem;
    }
    .ev-file-path {
        font-size: .72rem;
        color: #475569;
        margin-top: .15rem;
    }
    .ev-meta { color: #475569; font-size: .8rem; }
    .ev-size-badge {
        display: inline-block;
        padding: .2rem .55rem;
        background: rgba(99,102,241,.1);
        border: 1px solid rgba(99,102,241,.2);
        border-radius: 20px;
        font-size: .72rem;
        color: #818cf8;
        font-weight: 500;
    }

    .btn-editar {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .9rem;
        background: rgba(99,102,241,.12);
        border: 1px solid rgba(99,102,241,.3);
        border-radius: 7px;
        color: #a5b4fc;
        font-size: .8rem;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s, color .2s, border-color .2s, transform .15s;
        cursor: pointer;
    }
    .btn-editar:hover {
        background: rgba(99,102,241,.25);
        border-color: rgba(99,102,241,.5);
        color: #c7d2fe;
        transform: translateY(-1px);
    }

    /* ══ MODO EDITOR ═════════════════════════════════════════════════════════ */
    .ev-editor-wrap { display: flex; flex-direction: column; gap: 0; height: calc(100vh - 200px); min-height: 500px; }

    /* Toolbar superior del editor */
    .ev-toolbar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem 1rem;
        background: #0d0f18;
        border: 1px solid rgba(255,255,255,.07);
        border-bottom: none;
        border-radius: 12px 12px 0 0;
        flex-wrap: wrap;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .85rem;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 7px;
        color: #94a3b8;
        font-size: .8rem;
        font-weight: 500;
        text-decoration: none;
        transition: all .2s;
    }
    .btn-back:hover {
        background: rgba(255,255,255,.1);
        color: #e2e8f0;
    }
    .ev-breadcrumb {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .82rem;
        color: #475569;
        flex: 1;
    }
    .ev-breadcrumb span:last-child {
        color: #c7d2fe;
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }
    .ev-breadcrumb .sep { color: #334155; }

    .ev-file-info {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .75rem;
        color: #475569;
        margin-left: auto;
    }
    .ev-file-info .dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #334155;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem 1.1rem;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        border: none;
        border-radius: 7px;
        color: #fff;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity .2s, transform .15s, box-shadow .2s;
        box-shadow: 0 0 0 rgba(99,102,241,0);
    }
    .btn-save:hover {
        opacity: .9;
        transform: translateY(-1px);
        box-shadow: 0 4px 18px rgba(99,102,241,.45);
    }
    .btn-save:active { transform: translateY(0); }
    .btn-save.saving { opacity: .6; pointer-events: none; }

    /* Statusbar del editor */
    .ev-statusbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .35rem 1rem;
        background: #080a12;
        border: 1px solid rgba(255,255,255,.07);
        border-top: 1px solid rgba(255,255,255,.04);
        border-radius: 0 0 12px 12px;
        font-size: .72rem;
        color: #334155;
        font-family: 'Courier New', monospace;
    }
    .ev-statusbar .status-left { display: flex; gap: 1rem; }
    .ev-statusbar span { color: #475569; }
    .ev-statusbar strong { color: #64748b; }

    /* CodeMirror override: ocupa todo el espacio */
    .ev-codemirror-host {
        flex: 1;
        overflow: hidden;
        border-left: 1px solid rgba(255,255,255,.07);
        border-right: 1px solid rgba(255,255,255,.07);
    }
    .ev-codemirror-host .CodeMirror {
        height: 100%;
        font-family: 'Fira Code', 'Cascadia Code', 'Courier New', monospace;
        font-size: 13.5px;
        line-height: 1.65;
        background: #12131f;
    }
    .ev-codemirror-host .CodeMirror-gutters {
        background: #0d0f1a;
        border-right: 1px solid rgba(255,255,255,.06);
    }
    .ev-codemirror-host .CodeMirror-linenumber { color: #2d3748; }
    .ev-codemirror-host .CodeMirror-cursor { border-left-color: #818cf8; }
    .ev-codemirror-host .CodeMirror-selected { background: rgba(99,102,241,.2) !important; }
    .ev-codemirror-host .CodeMirror-activeline-background { background: rgba(255,255,255,.025) !important; }
</style>

<?php
// ── Determinar si el mensaje es éxito o error ─────────────────────────────────
$flash_clase = '';
if ($mensaje_editor !== null) {
    $flash_clase = str_starts_with($mensaje_editor, '✅') ? 'success' : 'error';
}
?>

<!-- ── Flash Message ─────────────────────────────────────────────────────── -->
<?php if ($mensaje_editor !== null): ?>
    <div class="ev-flash <?= $flash_clase ?>">
        <?= htmlspecialchars($mensaje_editor, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>


<?php if ($archivo_seleccionado === null): ?>
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- ── MODO LISTA ─────────────────────────────────────────────────────────── -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->

<div class="ev-list-header">
    <h2>🖊️ Editor de Vistas Públicas</h2>
    <p>Selecciona una vista para editar su contenido directamente desde el navegador. Los cambios se guardan en el archivo físico.</p>
</div>

<div class="ev-table-wrap">
    <table class="ev-table">
        <thead>
            <tr>
                <th>Archivo</th>
                <th>Última modificación</th>
                <th>Tamaño</th>
                <th style="text-align:right;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vistas_publicas)): ?>
            <tr>
                <td colspan="4" style="text-align:center;padding:2rem;color:#475569;">
                    No se encontraron vistas públicas en <code>views/public/</code>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($vistas_publicas as $v): ?>
            <tr>
                <td>
                    <div class="ev-filename">
                        <div class="ev-file-icon">📄</div>
                        <div>
                            <div class="ev-file-name"><?= htmlspecialchars($v['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="ev-file-path"><?= htmlspecialchars($v['relativa'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                </td>
                <td class="ev-meta"><?= htmlspecialchars($v['modificado'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="ev-size-badge">
                        <?= number_format($v['bytes'] / 1024, 1) ?> KB
                    </span>
                </td>
                <td style="text-align:right;">
                    <a href="/editor-vistas?archivo=<?= urlencode($v['relativa']) ?>"
                       class="btn-editar"
                       title="Editar <?= htmlspecialchars($v['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        ✏️ Editar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<?php else: ?>
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- ── MODO EDITOR ────────────────────────────────────────────────────────── -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->

<?php
    $nombre_archivo  = basename($archivo_seleccionado);
    $bytes_archivo   = strlen($contenido_archivo ?? '');
    $lineas_archivo  = substr_count($contenido_archivo ?? '', "\n") + 1;
    $csrf_safe       = htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8');
    $archivo_safe    = htmlspecialchars($archivo_seleccionado, ENT_QUOTES, 'UTF-8');
    $contenido_safe  = htmlspecialchars($contenido_archivo ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="ev-editor-wrap">

    <!-- Toolbar -->
    <div class="ev-toolbar">
        <a href="/editor-vistas" class="btn-back" title="Volver a la lista">
            ← Volver
        </a>

        <div class="ev-breadcrumb">
            <span>Editor</span>
            <span class="sep">/</span>
            <span>views/public</span>
            <span class="sep">/</span>
            <span><?= htmlspecialchars($nombre_archivo, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="ev-file-info">
            <span><?= $lineas_archivo ?> líneas</span>
            <div class="dot"></div>
            <span><?= number_format($bytes_archivo / 1024, 1) ?> KB</span>
            <div class="dot"></div>
            <span>PHP / HTML</span>
        </div>

        <button type="button" id="btn-guardar" class="btn-save"
                onclick="guardarContenido()">
            💾 Guardar cambios
        </button>
    </div>

    <!-- Área del editor CodeMirror -->
    <div class="ev-codemirror-host">
        <textarea id="ev-codemirror" name="contenido"><?= $contenido_safe ?></textarea>
    </div>

    <!-- Statusbar -->
    <div class="ev-statusbar" id="ev-statusbar">
        <div class="status-left">
            <span>Línea <strong id="sb-line">1</strong>, Col <strong id="sb-col">1</strong></span>
            <span>Selección: <strong id="sb-sel">0</strong> chars</span>
        </div>
        <div>
            <span><?= htmlspecialchars($archivo_safe, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

</div>

<!-- Formulario oculto para enviar el POST -->
<form id="form-editor" method="POST" action="/editor-vistas" style="display:none;">
    <input type="hidden" name="accion"     value="guardar_vista">
    <input type="hidden" name="csrf_token" value="<?= $csrf_safe ?>">
    <input type="hidden" name="archivo"    value="<?= $archivo_safe ?>">
    <textarea name="contenido" id="form-contenido"></textarea>
</form>

<!-- CodeMirror: assets locales -->
<link rel="stylesheet" href="/assets/vendor/codemirror/codemirror.min.css">
<link rel="stylesheet" href="/assets/vendor/codemirror/theme/dracula.min.css">
<script src="/assets/vendor/codemirror/codemirror.min.js"></script>
<script src="/assets/vendor/codemirror/mode/xml/xml.min.js"></script>
<script src="/assets/vendor/codemirror/mode/javascript/javascript.min.js"></script>
<script src="/assets/vendor/codemirror/mode/css/css.min.js"></script>
<script src="/assets/vendor/codemirror/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="/assets/vendor/codemirror/mode/clike/clike.min.js"></script>
<script src="/assets/vendor/codemirror/mode/php/php.min.js"></script>

<script>
(function () {
    'use strict';

    /* ── Inicializar CodeMirror ──────────────────────────────────────────── */
    var editor = CodeMirror.fromTextArea(document.getElementById('ev-codemirror'), {
        mode          : { name: 'php', htmlMode: true },
        theme         : 'dracula',
        lineNumbers   : true,
        indentUnit    : 4,
        tabSize       : 4,
        indentWithTabs: false,
        lineWrapping  : false,
        autofocus     : true,
        matchBrackets : true,
        autoCloseBrackets: true,
        styleActiveLine  : true,
        extraKeys     : {
            'Ctrl-S': function (cm) { guardarContenido(); },
            'Cmd-S' : function (cm) { guardarContenido(); },
        }
    });

    /* ── Forzar altura completa del host ─────────────────────────────────── */
    function ajustarAltura() {
        var host    = document.querySelector('.ev-codemirror-host');
        var toolbar = document.querySelector('.ev-toolbar');
        var status  = document.querySelector('.ev-statusbar');
        var wrap    = document.querySelector('.ev-editor-wrap');
        if (!host || !toolbar || !status || !wrap) return;

        var disponible = wrap.offsetHeight - toolbar.offsetHeight - status.offsetHeight;
        host.style.height = disponible + 'px';
        editor.refresh();
    }
    window.addEventListener('resize', ajustarAltura);
    setTimeout(ajustarAltura, 50);

    /* ── Actualizar statusbar en cada cambio de cursor ───────────────────── */
    editor.on('cursorActivity', function () {
        var cur = editor.getCursor();
        document.getElementById('sb-line').textContent = cur.line + 1;
        document.getElementById('sb-col').textContent  = cur.ch + 1;

        var sel = editor.getSelection();
        document.getElementById('sb-sel').textContent = sel.length;
    });

    /* ── Función global de guardado ──────────────────────────────────────── */
    window.guardarContenido = function () {
        var btn = document.getElementById('btn-guardar');
        btn.classList.add('saving');
        btn.textContent = '⏳ Guardando…';

        // Vuelca el contenido del editor al textarea oculto del form
        document.getElementById('form-contenido').value = editor.getValue();
        document.getElementById('form-editor').submit();
    };
})();
</script>

<?php endif; ?>
