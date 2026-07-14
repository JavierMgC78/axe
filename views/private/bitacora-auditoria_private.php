<?php
/**
 * views/auditoria.php — Vista parcial del módulo de Bitácora de Auditoría.
 * Solo lectura. Variables inyectadas por AuditoriaController:
 *   $registros       (array)       — Filas de bitacora_auditoria con email del actor.
 *   $error_auditoria (string|null) — Mensaje de error de BD, si lo hay.
 */
?>
<style>
    /* ── Cabecera de módulo ───────────────────────────────────────────────── */
    .alog-page-header { display:flex; align-items:flex-start; gap:1rem; margin-bottom:1.75rem; }
    .alog-header-icon {
        width:52px; height:52px;
        background:linear-gradient(135deg,rgba(239,68,68,.18),rgba(251,146,60,.12));
        border:1px solid rgba(239,68,68,.28);
        border-radius:13px; display:flex; align-items:center; justify-content:center;
        font-size:1.5rem; flex-shrink:0;
        box-shadow:0 0 22px rgba(239,68,68,.12);
    }
    .alog-header-text h2 {
        font-size:1.55rem; font-weight:800; color:#f1f5f9;
        letter-spacing:-.02em; margin-bottom:.25rem;
    }
    .alog-header-text h2 span {
        background:linear-gradient(90deg,#f87171,#fb923c);
        -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }
    .alog-header-text p { font-size:.87rem; color:#64748b; line-height:1.6; }

    /* ── Alerta de error de BD ───────────────────────────────────────────── */
    .alog-alert {
        display:flex; align-items:flex-start; gap:.85rem;
        padding:.95rem 1.3rem; border-radius:11px; font-size:.87rem;
        font-weight:500; line-height:1.5; margin-bottom:1.5rem;
        position:relative; overflow:hidden;
        animation:alogAlertIn .3s cubic-bezier(.34,1.56,.64,1);
    }
    .alog-alert::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; }
    @keyframes alogAlertIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
    .alog-alert-error { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:#fca5a5; }
    .alog-alert-error::before { background:#ef4444; }
    .alog-alert-icon { font-size:1.1rem; flex-shrink:0; }

    /* ── Tarjeta contenedora ─────────────────────────────────────────────── */
    .alog-card {
        background:linear-gradient(145deg,#13162a,#10131f);
        border:1px solid rgba(239,68,68,.12);
        border-radius:17px; padding:1.6rem 1.75rem;
        box-shadow:0 10px 38px rgba(0,0,0,.4);
    }

    /* ── Meta de la tabla ────────────────────────────────────────────────── */
    .alog-table-meta {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:1.25rem; padding-bottom:1.1rem;
        border-bottom:1px solid rgba(239,68,68,.1);
        flex-wrap:wrap; gap:.75rem;
    }
    .alog-table-meta-left { display:flex; align-items:center; gap:.85rem; }
    .alog-table-meta-icon {
        width:42px; height:42px;
        background:linear-gradient(135deg,rgba(239,68,68,.15),rgba(251,146,60,.1));
        border:1px solid rgba(239,68,68,.22);
        border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;
    }
    .alog-table-meta-text h3 { font-size:1rem; font-weight:700; color:#e2e8f0; }
    .alog-table-meta-text p  { font-size:.78rem; color:#475569; margin-top:.1rem; }
    .alog-count-badge {
        display:inline-flex; align-items:center;
        padding:.3rem .8rem;
        background:rgba(239,68,68,.09); border:1px solid rgba(239,68,68,.18);
        border-radius:20px; font-size:.76rem; color:#f87171; font-weight:600;
    }

    /* ── Tabla ───────────────────────────────────────────────────────────── */
    .alog-table-scroll { overflow-x:auto; border-radius:11px; border:1px solid rgba(239,68,68,.1); }
    .alog-table { width:100%; border-collapse:collapse; min-width:820px; }
    .alog-table thead {
        background:linear-gradient(135deg,rgba(239,68,68,.1),rgba(251,146,60,.06));
    }
    .alog-table thead th {
        padding:.85rem 1rem; text-align:left;
        font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
        color:#f87171; border-bottom:1px solid rgba(239,68,68,.14); white-space:nowrap;
    }
    .alog-table tbody tr {
        border-bottom:1px solid rgba(255,255,255,.04); transition:background .18s;
    }
    .alog-table tbody tr:last-child { border-bottom:none; }
    .alog-table tbody tr:hover { background:rgba(239,68,68,.04); }
    .alog-table tbody td { padding:.9rem 1rem; font-size:.85rem; vertical-align:middle; color:#cbd5e1; }

    /* ── Celdas específicas ──────────────────────────────────────────────── */
    .alog-cell-id { font-family:'Courier New',monospace; font-size:.77rem; color:#64748b; font-weight:600; }
    .alog-cell-fecha { color:#94a3b8; font-size:.82rem; white-space:nowrap; }
    .alog-cell-actor {
        color:#e2e8f0; font-weight:500; max-width:180px;
        overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block;
    }
    .alog-cell-actor-deleted { color:#64748b; font-style:italic; font-size:.82rem; }
    .alog-cell-ip  { font-family:'Courier New',monospace; font-size:.8rem; color:#64748b; white-space:nowrap; }
    .alog-cell-detalles {
        font-family:'Courier New',monospace; font-size:.78rem; color:#94a3b8;
        max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        display:block; cursor:help;
    }

    /* ── Badges de evento ────────────────────────────────────────────────── */
    .alog-evento {
        display:inline-flex; align-items:center; gap:.38rem;
        padding:.26rem .7rem; border-radius:7px;
        font-size:.73rem; font-weight:700; white-space:nowrap; letter-spacing:.03em;
    }
    .alog-ev-created  { background:rgba(16,185,129,.1);  color:#34d399; border:1px solid rgba(16,185,129,.22); }
    .alog-ev-deleted  { background:rgba(239,68,68,.1);   color:#f87171; border:1px solid rgba(239,68,68,.22); }
    .alog-ev-login    { background:rgba(99,102,241,.1);  color:#818cf8; border:1px solid rgba(99,102,241,.22); }
    .alog-ev-update   { background:rgba(245,158,11,.1);  color:#fbbf24; border:1px solid rgba(245,158,11,.22); }
    .alog-ev-default  { background:rgba(100,116,139,.1); color:#94a3b8; border:1px solid rgba(100,116,139,.2); }

    /* ── Badge de recurso ────────────────────────────────────────────────── */
    .alog-recurso {
        display:inline-flex; align-items:center;
        padding:.18rem .55rem; border-radius:5px;
        font-size:.72rem; font-weight:600;
        background:rgba(99,102,241,.08); color:#6366f1;
        border:1px solid rgba(99,102,241,.15);
    }

    /* ── Estado vacío ────────────────────────────────────────────────────── */
    .alog-empty { text-align:center; padding:3.5rem 1.5rem; color:#475569; }
    .alog-empty-icon { font-size:2.8rem; margin-bottom:1rem; opacity:.4; }
    .alog-empty p { font-size:.88rem; line-height:1.7; }

    /* ── Pie de módulo (volver) ──────────────────────────────────────────── */
    .alog-footer { margin-top:1.5rem; display:flex; justify-content:flex-end; }
    .alog-btn-back {
        display:inline-flex; align-items:center; gap:.45rem;
        padding:.6rem 1.3rem;
        background:rgba(100,116,139,.1); color:#94a3b8;
        border:1px solid rgba(100,116,139,.2); border-radius:9px;
        font-size:.84rem; font-weight:600; text-decoration:none;
        transition:all .22s;
    }
    .alog-btn-back:hover {
        background:rgba(100,116,139,.2); color:#cbd5e1;
        border-color:rgba(100,116,139,.35); transform:translateY(-1px);
    }

    /* ── Responsive ──────────────────────────────────────────────────────── */
    @media(max-width:700px){ .alog-card{padding:1.2rem;} }
</style>

<!-- ── CABECERA ──────────────────────────────────────────────────────────── -->
<div class="alog-page-header" id="auditoria-header">
    <div class="alog-header-icon">&#x1F50E;&#xFE0F;</div>
    <div class="alog-header-text">
        <h2>Registro Forense de <span>Actividad</span></h2>
        <p>Vista de solo lectura &mdash; Últimos 100 eventos del sistema ordenados por fecha descendente.</p>
    </div>
</div>

<!-- ── ALERTA DE ERROR DE BD ────────────────────────────────────────────── -->
<?php if (!empty($error_auditoria)): ?>
    <div class="alog-alert alog-alert-error" role="alert" aria-live="assertive" id="alog-error-bd">
        <span class="alog-alert-icon">&#x26A0;</span>
        <span><?= htmlspecialchars($error_auditoria, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
<?php endif; ?>

<!-- ── TABLA DE BITÁCORA ─────────────────────────────────────────────────── -->
<div class="alog-card" id="panel-bitacora-auditoria">
    <?php $total = count($registros ?? []); ?>
    <div class="alog-table-meta">
        <div class="alog-table-meta-left">
            <div class="alog-table-meta-icon">&#x1F4DC;</div>
            <div class="alog-table-meta-text">
                <h3>Bitácora de Auditoría</h3>
                <p>Eventos de acceso, mutaciones y operaciones críticas del sistema</p>
            </div>
        </div>
        <span class="alog-count-badge" id="alog-total-badge">
            <?= $total ?> evento<?= $total !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (!empty($registros)): ?>
        <div class="alog-table-scroll" id="alog-table-scroll">
            <table class="alog-table" id="tabla-auditoria">
                <thead>
                    <tr>
                        <th scope="col"># ID</th>
                        <th scope="col">Fecha y Hora</th>
                        <th scope="col">Actor (Email)</th>
                        <th scope="col">Evento</th>
                        <th scope="col">Recurso</th>
                        <th scope="col">IP Origen</th>
                        <th scope="col">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $fila): ?>
                        <?php
                            // ── Clasificación del badge de evento ─────────────
                            $ev  = strtoupper((string) $fila['evento']);
                            if (str_contains($ev, 'CREADO') || str_contains($ev, 'CREADA') || str_contains($ev, 'LOGIN')) {
                                if (str_contains($ev, 'LOGIN')) {
                                    $ev_clase = 'alog-ev-login';
                                } else {
                                    $ev_clase = 'alog-ev-created';
                                }
                            } elseif (str_contains($ev, 'ELIMINADO') || str_contains($ev, 'ELIMINADA') || str_contains($ev, 'SUSPENDIDO')) {
                                $ev_clase = 'alog-ev-deleted';
                            } elseif (str_contains($ev, 'ACTUALIZADO') || str_contains($ev, 'ACTUALIZADA') || str_contains($ev, 'NIVEL') || str_contains($ev, 'ESTATUS')) {
                                $ev_clase = 'alog-ev-update';
                            } else {
                                $ev_clase = 'alog-ev-default';
                            }

                            $safe_id      = htmlspecialchars((string) $fila['id'],         ENT_QUOTES, 'UTF-8');
                            $safe_fecha   = htmlspecialchars((string) $fila['creado_en'],  ENT_QUOTES, 'UTF-8');
                            $safe_evento  = htmlspecialchars((string) $fila['evento'],     ENT_QUOTES, 'UTF-8');
                            $safe_recurso = htmlspecialchars((string) $fila['recurso'],    ENT_QUOTES, 'UTF-8');
                            $safe_ip      = htmlspecialchars((string) $fila['ip_origen'],  ENT_QUOTES, 'UTF-8');
                            $safe_actor   = $fila['actor_email'] !== null
                                ? htmlspecialchars((string) $fila['actor_email'], ENT_QUOTES, 'UTF-8')
                                : null;
                            $safe_detalle = htmlspecialchars($fila['detalles'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr id="fila-alog-<?= $safe_id ?>">
                            <td class="alog-cell-id">#<?= $safe_id ?></td>
                            <td class="alog-cell-fecha"><?= $safe_fecha ?></td>
                            <td>
                                <?php if ($safe_actor !== null): ?>
                                    <span class="alog-cell-actor" title="<?= $safe_actor ?>"><?= $safe_actor ?></span>
                                <?php else: ?>
                                    <span class="alog-cell-actor-deleted">&#x1F6AB; Usuario eliminado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="alog-evento <?= $ev_clase ?>">
                                    <?= $safe_evento ?>
                                </span>
                            </td>
                            <td>
                                <span class="alog-recurso"><?= $safe_recurso ?></span>
                            </td>
                            <td class="alog-cell-ip"><?= $safe_ip ?></td>
                            <td>
                                <span class="alog-cell-detalles" title="<?= $safe_detalle ?>">
                                    <?= $safe_detalle ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alog-empty" id="alog-empty-state">
            <div class="alog-empty-icon">&#x1F4C2;</div>
            <p>La bitácora está vacía.<br>
               Los eventos del sistema se registrarán aquí automáticamente.</p>
        </div>
    <?php endif; ?>
</div>

<!-- ── PIE ───────────────────────────────────────────────────────────────── -->
<div class="alog-footer">
    <a href="/dashboard" class="alog-btn-back" id="btn-volver-dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="2.2" stroke="currentColor" width="14" height="14" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
        </svg>
        Volver al Dashboard
    </a>
</div>
