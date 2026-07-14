<?php
/**
 * views/gestor-roles.php
 *
 * VISTA PARCIAL — Módulo Gestor de Roles (CRUD Inline)
 * ─────────────────────────────────────────────────────────────────────────────
 * Variables disponibles (inyectadas por GestorRolesController via extract()):
 *   $lista_roles    (array)       — Registros de la tabla `roles` (nivel, nombre, descripcion).
 *   $mensaje_roles  (string|null) — Feedback de la operación POST/PRG.
 *   $csrf_token     (string)      — Token CSRF de sesión (inyectado por index.php).
 */
?>
<style>
    /* ── Reset parcial ── */
    *, *::before, *::after { box-sizing: border-box; }

    /* ── Cabecera de página ── */
    .gr-page-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .gr-header-icon {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, rgba(99,102,241,.2), rgba(139,92,246,.2));
        border: 1px solid rgba(99,102,241,.3);
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        box-shadow: 0 0 18px rgba(99,102,241,.15);
    }
    .gr-header-text h2 {
        font-size: 1.55rem; font-weight: 800;
        color: #f1f5f9; letter-spacing: -.02em;
        margin-bottom: .25rem;
    }
    .gr-header-text h2 span {
        background: linear-gradient(90deg, #818cf8, #c4b5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .gr-header-text p { font-size: .87rem; color: #64748b; line-height: 1.6; }

    /* ── Alerta de sistema ── */
    .gr-alert {
        display: flex; align-items: flex-start;
        gap: .85rem;
        padding: .95rem 1.3rem;
        border-radius: 11px;
        font-size: .87rem; font-weight: 500; line-height: 1.5;
        margin-bottom: 1.5rem;
        position: relative; overflow: hidden;
        animation: grAlertIn .3s cubic-bezier(.34,1.56,.64,1);
    }
    .gr-alert::before {
        content: ''; position: absolute;
        left: 0; top: 0; bottom: 0; width: 3px;
    }
    @keyframes grAlertIn {
        from { opacity: 0; transform: translateY(-10px) scale(.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .gr-alert-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: .05rem; }
    .gr-alert-success {
        background: rgba(16,185,129,.1);
        border: 1px solid rgba(16,185,129,.3);
        color: #6ee7b7;
    }
    .gr-alert-success::before { background: #10b981; }
    .gr-alert-error {
        background: rgba(239,68,68,.1);
        border: 1px solid rgba(239,68,68,.3);
        color: #fca5a5;
    }
    .gr-alert-error::before { background: #ef4444; }
    .gr-alert-warning {
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.28);
        color: #fcd34d;
    }
    .gr-alert-warning::before { background: #f59e0b; }

    /* ── Card ── */
    .gr-card {
        background: linear-gradient(145deg, #13162a, #10131f);
        border: 1px solid rgba(99,102,241,.14);
        border-radius: 17px;
        padding: 1.6rem 1.75rem;
        box-shadow: 0 10px 38px rgba(0,0,0,.4);
        margin-bottom: 1.5rem;
    }

    /* ── Cabecera de tabla ── */
    .gr-table-meta {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 1.1rem;
        border-bottom: 1px solid rgba(99,102,241,.1);
        flex-wrap: wrap;
        gap: .75rem;
    }
    .gr-table-meta-left { display: flex; align-items: center; gap: .85rem; }
    .gr-table-meta-right { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }
    .gr-table-meta-icon {
        width: 42px; height: 42px;
        background: linear-gradient(135deg, rgba(99,102,241,.18), rgba(139,92,246,.1));
        border: 1px solid rgba(99,102,241,.25);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .gr-table-meta-text h3 { font-size: 1rem; font-weight: 700; color: #e2e8f0; }
    .gr-table-meta-text p  { font-size: .78rem; color: #475569; margin-top: .1rem; }
    .gr-count-badge {
        display: inline-flex; align-items: center;
        padding: .3rem .8rem;
        background: rgba(99,102,241,.1);
        border: 1px solid rgba(99,102,241,.2);
        border-radius: 20px;
        font-size: .76rem; color: #818cf8; font-weight: 600;
    }

    /* ── Tabla ── */
    .gr-table-scroll {
        overflow-x: auto;
        border-radius: 11px;
        border: 1px solid rgba(99,102,241,.1);
    }
    .gr-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        min-width: 620px;
    }
    .gr-table thead {
        background: linear-gradient(135deg, rgba(99,102,241,.12), rgba(139,92,246,.08));
    }
    .gr-table thead th {
        padding: .85rem 1rem;
        text-align: left;
        font-size: .7rem; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
        color: #6366f1;
        border-bottom: 1px solid rgba(99,102,241,.15);
        white-space: nowrap;
    }
    .gr-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,.04);
        transition: background .18s;
    }
    .gr-table tbody tr:last-child { border-bottom: none; }
    .gr-table tbody tr:hover { background: rgba(99,102,241,.05); }
    .gr-table tbody td {
        padding: .85rem 1rem;
        font-size: .84rem;
        vertical-align: middle;
        color: #cbd5e1;
    }

    /* ── Celda nivel / badge ── */
    .gr-nivel-badge {
        display: inline-flex; align-items: center;
        padding: .22rem .65rem;
        background: rgba(99,102,241,.12);
        border: 1px solid rgba(99,102,241,.25);
        border-radius: 20px;
        font-size: .75rem; color: #a5b4fc; font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: .04em;
    }

    /* ── Celdas de texto ── */
    .gr-cell-nombre { color: #e2e8f0; font-weight: 600; }
    .gr-cell-desc   { color: #64748b; font-style: italic; font-size: .8rem; }
    .gr-cell-desc-empty { color: #334155; font-style: italic; font-size: .78rem; }

    /* ── Fila de inserción rápida (Inline Insert) ── */
    .gr-insert-row {
        background: linear-gradient(90deg, rgba(99,102,241,.07), rgba(139,92,246,.04));
        border-bottom: 1px solid rgba(99,102,241,.18) !important;
    }
    .gr-insert-row td {
        padding: .65rem 1rem !important;
        vertical-align: middle;
    }
    .gr-insert-first-td {
        box-shadow: inset 3px 0 0 0 #6366f1;
    }
    /* Inputs de la fila inline */
    .gr-insert-input {
        background: rgba(10,12,20,.8);
        border: 1px solid rgba(99,102,241,.2);
        border-radius: 7px;
        color: #e2e8f0;
        font-size: .8rem;
        padding: .38rem .6rem;
        width: 100%;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        font-family: inherit;
    }
    .gr-insert-input:focus {
        border-color: rgba(99,102,241,.55);
        box-shadow: 0 0 0 3px rgba(99,102,241,.1);
        background: rgba(10,12,20,.95);
    }
    .gr-insert-input::placeholder { color: #334155; }
    .gr-insert-input[type="number"] { font-family: 'Courier New', monospace; }
    /* Etiqueta "Nuevo" columna nivel */
    .gr-insert-id-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .2rem .55rem;
        background: rgba(99,102,241,.12);
        border: 1px solid rgba(99,102,241,.25);
        border-radius: 20px;
        font-size: .7rem; color: #818cf8; font-weight: 700;
        letter-spacing: .04em;
        white-space: nowrap;
    }
    /* Botón guardar inline */
    .gr-btn-insert-save {
        display: inline-flex; align-items: center; justify-content: center;
        gap: .3rem;
        padding: .42rem .9rem;
        background: linear-gradient(135deg, rgba(16,185,129,.25), rgba(5,150,105,.15));
        border: 1px solid rgba(16,185,129,.35);
        border-radius: 8px;
        color: #6ee7b7;
        font-size: .78rem; font-weight: 700;
        cursor: pointer;
        transition: background .18s, border-color .18s, transform .15s, box-shadow .2s;
        white-space: nowrap;
    }
    .gr-btn-insert-save:hover {
        background: linear-gradient(135deg, rgba(16,185,129,.38), rgba(5,150,105,.25));
        border-color: rgba(16,185,129,.6);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(16,185,129,.2);
    }
    .gr-btn-insert-save:active { transform: scale(.96); }

    /* ── Botón eliminar ── */
    .gr-btn-delete {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px;
        background: rgba(239,68,68,.1);
        border: 1px solid rgba(239,68,68,.25);
        border-radius: 7px;
        color: #f87171; font-size: .85rem;
        cursor: pointer;
        transition: background .18s, border-color .18s, transform .15s;
        padding: 0; line-height: 1;
    }
    .gr-btn-delete:hover {
        background: rgba(239,68,68,.22);
        border-color: rgba(239,68,68,.5);
        transform: scale(1.08);
    }
    .gr-btn-delete:active { transform: scale(.96); }
    .gr-delete-form { display: inline-flex; }

    /* ── Estado vacío ── */
    .gr-empty {
        text-align: center; padding: 3rem 1.5rem;
        color: #475569;
    }
    .gr-empty-icon { font-size: 2.4rem; margin-bottom: .9rem; opacity: .5; }
    .gr-empty p { font-size: .88rem; line-height: 1.6; }

    /* ── Responsive ── */
    @media (max-width: 860px) {
        .gr-card { padding: 1.2rem; }
    }
</style>

<!-- ── CABECERA DE PÁGINA ── -->
<div class="gr-page-header">
    <div class="gr-header-icon">🛡️</div>
    <div class="gr-header-text">
        <h2>Gestor de <span>Roles</span></h2>
        <p>Administra los niveles de acceso del sistema. Los cambios regeneran automáticamente la caché de roles.</p>
    </div>
</div>

<!-- ── ALERTA DEL SISTEMA (condicional) ── -->
<?php if (!empty($mensaje_roles)) : ?>
    <?php
        $gr_es_error    = str_starts_with($mensaje_roles, '❌');
        $gr_es_warning  = str_starts_with($mensaje_roles, '⚠️');
        $gr_cls_alert   = $gr_es_error   ? 'gr-alert-error'
                        : ($gr_es_warning ? 'gr-alert-warning' : 'gr-alert-success');
        $gr_ico_alert   = $gr_es_error   ? '✗'
                        : ($gr_es_warning ? '⚠' : '✓');
    ?>
    <div class="gr-alert <?= $gr_cls_alert ?>" role="alert" aria-live="polite" id="gr-system-alert">
        <span class="gr-alert-icon"><?= $gr_ico_alert ?></span>
        <span><?= htmlspecialchars($mensaje_roles, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
<?php endif; ?>

<!-- ── PANEL: TABLA DE ROLES ── -->
<div class="gr-card" id="panel-lista-roles">

    <?php $total_roles = count($lista_roles ?? []); ?>

    <div class="gr-table-meta">
        <div class="gr-table-meta-left">
            <div class="gr-table-meta-icon">📋</div>
            <div class="gr-table-meta-text">
                <h3>Directorio de Roles</h3>
                <p>Crea nuevos roles o elimina los existentes. La eliminación se bloquea si hay usuarios asignados.</p>
            </div>
        </div>
        <div class="gr-table-meta-right">
            <span class="gr-count-badge">
                <?= $total_roles ?> rol<?= $total_roles !== 1 ? 'es' : '' ?>
            </span>
        </div>
    </div>

    <?php $csrf_safe = htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>

    <!-- ══ FORMULARIO EXTERNO DE INSERCIÓN RÁPIDA (HTML5 form association) ══ -->
    <!-- Declarado FUERA de la tabla para no romper la estructura <tr>/<td>.   -->
    <form
        id="formNuevoRol"
        method="POST"
        action="/gestor-roles"
        novalidate
    >
        <input type="hidden" name="csrf_token" value="<?= $csrf_safe ?>">
        <input type="hidden" name="accion" value="crear">
    </form>
    <!-- ══ FIN FORMULARIO EXTERNO ══ -->

    <div class="gr-table-scroll">
        <table class="gr-table" id="tabla-roles">
            <!-- colgroup: define anchos fijos para las 4 columnas -->
            <colgroup>
                <col style="width: 110px">  <!-- Nivel -->
                <col style="width: 22%">    <!-- Nombre -->
                <col>                       <!-- Descripción (flex) -->
                <col style="width: 90px">   <!-- Acciones -->
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">Nivel</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <!-- ══ FILA DE INSERCIÓN RÁPIDA (Inline Insert) ══ -->
                <!-- Usa el atributo HTML5 form="formNuevoRol" para vincular   -->
                <!-- cada control al formulario externo sin anidar <form> en <tr>-->
                <tr class="gr-insert-row" id="fila-nuevo-rol">

                    <!-- Col 1: Nivel — input numérico -->
                    <td class="gr-insert-first-td">
                        <input
                            type="number"
                            name="nivel"
                            id="inline-nivel"
                            class="gr-insert-input"
                            placeholder="Ej: 30"
                            min="0"
                            max="999"
                            required
                            form="formNuevoRol"
                            aria-label="Nivel numérico del nuevo rol"
                        >
                    </td>

                    <!-- Col 2: Nombre -->
                    <td>
                        <input
                            type="text"
                            name="nombre"
                            id="inline-nombre"
                            class="gr-insert-input"
                            placeholder="Nombre del Rol"
                            maxlength="60"
                            required
                            form="formNuevoRol"
                            aria-label="Nombre del nuevo rol"
                        >
                    </td>

                    <!-- Col 3: Descripción (opcional) -->
                    <td>
                        <input
                            type="text"
                            name="descripcion"
                            id="inline-descripcion"
                            class="gr-insert-input"
                            placeholder="Breve descripción (opcional)"
                            maxlength="160"
                            form="formNuevoRol"
                            aria-label="Descripción del nuevo rol"
                        >
                    </td>

                    <!-- Col 4: Acción — Guardar -->
                    <td>
                        <button
                            type="submit"
                            class="gr-btn-insert-save"
                            id="btn-inline-guardar-rol"
                            form="formNuevoRol"
                            aria-label="Guardar nuevo rol"
                            title="Guardar nuevo rol"
                        >
                            <!-- Icono check -->
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            Guardar
                        </button>
                    </td>

                </tr>
                <!-- ══ FIN FILA DE INSERCIÓN RÁPIDA ══ -->

                <?php if (!empty($lista_roles)) : ?>
                    <?php foreach ($lista_roles as $rol) : ?>
                        <?php
                            $safe_nivel = htmlspecialchars((string) $rol['nivel'],       ENT_QUOTES, 'UTF-8');
                            $safe_nom   = htmlspecialchars((string) $rol['nombre'],      ENT_QUOTES, 'UTF-8');
                            $safe_desc  = htmlspecialchars((string) ($rol['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr id="fila-rol-<?= $safe_nivel ?>">

                            <!-- Nivel -->
                            <td>
                                <span class="gr-nivel-badge"><?= $safe_nivel ?></span>
                            </td>

                            <!-- Nombre -->
                            <td>
                                <span class="gr-cell-nombre"><?= $safe_nom ?></span>
                            </td>

                            <!-- Descripción -->
                            <td>
                                <?php if ($safe_desc !== '') : ?>
                                    <span class="gr-cell-desc"><?= $safe_desc ?></span>
                                <?php else : ?>
                                    <span class="gr-cell-desc-empty">— sin descripción —</span>
                                <?php endif; ?>
                            </td>

                            <!-- Acciones — Botón Eliminar -->
                            <td>
                                <form
                                    method="POST"
                                    action="/gestor-roles"
                                    class="gr-delete-form"
                                    id="form-delete-rol-<?= $safe_nivel ?>"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este rol? Si hay usuarios asignados, la acción se bloqueará.');"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_safe ?>">
                                    <input type="hidden" name="accion"     value="eliminar">
                                    <input type="hidden" name="nivel"      value="<?= $safe_nivel ?>">
                                    <button
                                        type="submit"
                                        class="gr-btn-delete"
                                        id="btn-delete-rol-<?= $safe_nivel ?>"
                                        aria-label="Eliminar rol <?= $safe_nom ?>"
                                        title="Eliminar rol"
                                    >
                                        <!-- Icono papelera -->
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">
                            <div class="gr-empty" id="gr-empty-state-roles">
                                <div class="gr-empty-icon">🛡️</div>
                                <p>No hay roles registrados todavía.<br>
                                   Usa la fila superior para crear el primero.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>
