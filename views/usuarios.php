<?php
/**
 * views/usuarios.php - Vista parcial del modulo IAM.
 * Variables: $lista_usuarios (array), $mensaje_usuarios (string|null)
 */
?>
<style>
    .iam-page-header { display:flex; align-items:flex-start; gap:1rem; margin-bottom:1.75rem; }
    .iam-header-icon { width:52px; height:52px; background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(139,92,246,.2)); border:1px solid rgba(99,102,241,.3); border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; box-shadow:0 0 18px rgba(99,102,241,.15); }
    .iam-header-text h2 { font-size:1.55rem; font-weight:800; color:#f1f5f9; letter-spacing:-.02em; margin-bottom:.25rem; }
    .iam-header-text h2 span { background:linear-gradient(90deg,#818cf8,#c4b5fd); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .iam-header-text p { font-size:.87rem; color:#64748b; line-height:1.6; }

    .iam-alert { display:flex; align-items:flex-start; gap:.85rem; padding:.95rem 1.3rem; border-radius:11px; font-size:.87rem; font-weight:500; line-height:1.5; margin-bottom:1.5rem; position:relative; overflow:hidden; animation:iamAlertIn .3s cubic-bezier(.34,1.56,.64,1); }
    .iam-alert::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; }
    @keyframes iamAlertIn { from{opacity:0;transform:translateY(-10px) scale(.98)} to{opacity:1;transform:translateY(0) scale(1)} }
    .iam-alert-icon { font-size:1.1rem; flex-shrink:0; margin-top:.05rem; }
    .iam-alert-success { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); color:#6ee7b7; }
    .iam-alert-success::before { background:#10b981; }
    .iam-alert-error { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:#fca5a5; }
    .iam-alert-error::before { background:#ef4444; }

    .iam-card { background:linear-gradient(145deg,#13162a,#10131f); border:1px solid rgba(99,102,241,.14); border-radius:17px; padding:1.6rem 1.75rem; box-shadow:0 10px 38px rgba(0,0,0,.4); margin-bottom:1.5rem; }
    .iam-card-header { display:flex; align-items:center; gap:.85rem; margin-bottom:1.5rem; padding-bottom:1.1rem; border-bottom:1px solid rgba(99,102,241,.1); }
    .iam-card-header-icon { width:42px; height:42px; background:linear-gradient(135deg,rgba(99,102,241,.22),rgba(139,92,246,.22)); border:1px solid rgba(99,102,241,.28); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
    .iam-card-header-text h3 { font-size:1rem; font-weight:700; color:#e2e8f0; }
    .iam-card-header-text p { font-size:.78rem; color:#475569; margin-top:.1rem; }

    .iam-form-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    .iam-form-group { display:flex; flex-direction:column; gap:.4rem; }
    .iam-label { font-size:.79rem; font-weight:600; color:#94a3b8; display:flex; align-items:center; gap:.3rem; }
    .iam-required { color:#f87171; font-size:.68rem; }
    .iam-input, .iam-select { background:rgba(10,12,20,.75); border:1px solid rgba(99,102,241,.22); border-radius:9px; padding:.65rem .9rem; color:#e2e8f0; font-size:.86rem; font-family:inherit; transition:border-color .2s,box-shadow .2s,background .2s; outline:none; width:100%; appearance:none; -webkit-appearance:none; }
    .iam-input::placeholder { color:#2d3748; }
    .iam-input:focus, .iam-select:focus { border-color:rgba(99,102,241,.65); box-shadow:0 0 0 3px rgba(99,102,241,.1); background:rgba(10,12,20,.95); }
    .iam-select-wrap { position:relative; }
    .iam-select-wrap::after { content:''; position:absolute; right:.85rem; top:50%; transform:translateY(-50%); width:0; height:0; border-left:4px solid transparent; border-right:4px solid transparent; border-top:5px solid #64748b; pointer-events:none; }
    .iam-select { cursor:pointer; padding-right:2.2rem; }
    .iam-select option { background:#1a1d2e; color:#e2e8f0; }
    .iam-form-footer { display:flex; justify-content:flex-end; padding-top:.1rem; }
    .iam-btn-submit { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.6rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border:none; border-radius:9px; font-size:.88rem; font-weight:700; font-family:inherit; cursor:pointer; transition:all .25s; box-shadow:0 4px 16px rgba(99,102,241,.35); letter-spacing:.02em; white-space:nowrap; }
    .iam-btn-submit:hover { background:linear-gradient(135deg,#4f46e5,#7c3aed); box-shadow:0 6px 22px rgba(99,102,241,.5); transform:translateY(-2px); }
    .iam-btn-submit:active { transform:translateY(0); }

    .iam-table-meta { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; padding-bottom:1.1rem; border-bottom:1px solid rgba(99,102,241,.1); }
    .iam-table-meta-left { display:flex; align-items:center; gap:.85rem; }
    .iam-table-meta-icon { width:42px; height:42px; background:linear-gradient(135deg,rgba(16,185,129,.18),rgba(52,211,153,.1)); border:1px solid rgba(16,185,129,.25); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
    .iam-table-meta-text h3 { font-size:1rem; font-weight:700; color:#e2e8f0; }
    .iam-table-meta-text p { font-size:.78rem; color:#475569; margin-top:.1rem; }
    .iam-count-badge { display:inline-flex; align-items:center; padding:.3rem .8rem; background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2); border-radius:20px; font-size:.76rem; color:#818cf8; font-weight:600; }
    .iam-table-scroll { overflow-x:auto; border-radius:11px; border:1px solid rgba(99,102,241,.1); }
    .iam-table { width:100%; border-collapse:collapse; min-width:680px; }
    .iam-table thead { background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.08)); }
    .iam-table thead th { padding:.85rem 1rem; text-align:left; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6366f1; border-bottom:1px solid rgba(99,102,241,.15); white-space:nowrap; }
    .iam-table thead th:last-child { text-align:right; }
    .iam-table tbody tr { border-bottom:1px solid rgba(255,255,255,.04); transition:background .18s; }
    .iam-table tbody tr:last-child { border-bottom:none; }
    .iam-table tbody tr:hover { background:rgba(99,102,241,.05); }
    .iam-table tbody td { padding:.9rem 1rem; font-size:.85rem; vertical-align:middle; color:#cbd5e1; }
    .iam-cell-id { font-family:'Courier New',monospace; font-size:.77rem; color:#64748b; font-weight:600; }
    .iam-cell-email { color:#e2e8f0; font-weight:500; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }

    .iam-nivel { display:inline-flex; align-items:center; padding:.22rem .65rem; border-radius:6px; font-size:.72rem; font-weight:700; white-space:nowrap; }
    .n-0   { background:rgba(100,116,139,.15); color:#94a3b8; border:1px solid rgba(100,116,139,.2); }
    .n-10  { background:rgba(59,130,246,.12);  color:#60a5fa; border:1px solid rgba(59,130,246,.2); }
    .n-20  { background:rgba(16,185,129,.12);  color:#34d399; border:1px solid rgba(16,185,129,.2); }
    .n-30  { background:rgba(245,158,11,.12);  color:#fbbf24; border:1px solid rgba(245,158,11,.2); }
    .n-40  { background:rgba(249,115,22,.12);  color:#fb923c; border:1px solid rgba(249,115,22,.2); }
    .n-50  { background:rgba(236,72,153,.12);  color:#f472b6; border:1px solid rgba(236,72,153,.2); }
    .n-60  { background:rgba(139,92,246,.15);  color:#a78bfa; border:1px solid rgba(139,92,246,.25); }
    .n-70  { background:rgba(99,102,241,.18);  color:#818cf8; border:1px solid rgba(99,102,241,.3); }
    .n-100 { background:rgba(239,68,68,.15);   color:#f87171; border:1px solid rgba(239,68,68,.25); }

    .iam-estatus { display:inline-flex; align-items:center; gap:.38rem; padding:.25rem .68rem; border-radius:20px; font-size:.74rem; font-weight:600; white-space:nowrap; }
    .iam-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
    .iam-activo { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); color:#34d399; }
    .iam-activo .iam-dot { background:#10b981; animation:iamPulse 2s infinite; }
    .iam-suspendido { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); color:#f87171; }
    .iam-suspendido .iam-dot { background:#ef4444; }
    @keyframes iamPulse { 0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(16,185,129,.4)} 50%{opacity:.6;box-shadow:0 0 0 4px rgba(16,185,129,0)} }

    .iam-actions-cell { text-align:right; }
    .iam-actions-wrap { display:flex; align-items:center; justify-content:flex-end; gap:.45rem; flex-wrap:wrap; }
    .iam-mini-form { display:inline-flex; align-items:center; gap:.38rem; }
    .iam-mini-select { background:rgba(10,12,20,.8); border:1px solid rgba(99,102,241,.22); border-radius:7px; padding:.34rem 1.5rem .34rem .55rem; color:#94a3b8; font-size:.73rem; font-family:inherit; cursor:pointer; outline:none; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748b' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .45rem center; transition:border-color .2s; max-width:128px; }
    .iam-mini-select:focus { border-color:rgba(99,102,241,.5); }
    .iam-mini-select option { background:#1a1d2e; }
    .iam-btn-mini { display:inline-flex; align-items:center; gap:.28rem; padding:.34rem .7rem; border:none; border-radius:7px; font-size:.73rem; font-weight:700; font-family:inherit; cursor:pointer; transition:all .2s; white-space:nowrap; }
    .iam-btn-update { background:rgba(99,102,241,.15); color:#818cf8; border:1px solid rgba(99,102,241,.25); }
    .iam-btn-update:hover { background:rgba(99,102,241,.28); border-color:rgba(99,102,241,.45); color:#a5b4fc; transform:translateY(-1px); }
    .iam-btn-suspend { background:rgba(239,68,68,.1); color:#f87171; border:1px solid rgba(239,68,68,.22); }
    .iam-btn-suspend:hover { background:rgba(239,68,68,.22); border-color:rgba(239,68,68,.4); color:#fca5a5; transform:translateY(-1px); }
    .iam-btn-reactivar { background:rgba(16,185,129,.1); color:#34d399; border:1px solid rgba(16,185,129,.22); }
    .iam-btn-reactivar:hover { background:rgba(16,185,129,.22); border-color:rgba(16,185,129,.4); color:#6ee7b7; transform:translateY(-1px); }
    .iam-sep { width:1px; height:18px; background:rgba(99,102,241,.15); flex-shrink:0; }

    .iam-empty { text-align:center; padding:3rem 1.5rem; color:#475569; }
    .iam-empty-icon { font-size:2.4rem; margin-bottom:.9rem; opacity:.5; }
    .iam-empty p { font-size:.88rem; line-height:1.6; }

    @media(max-width:860px){ .iam-form-grid{grid-template-columns:1fr 1fr;} }
    @media(max-width:560px){ .iam-form-grid{grid-template-columns:1fr;} .iam-card{padding:1.2rem;} }
</style>

<!-- CABECERA -->
<div class="iam-page-header">
    <div class="iam-header-icon">&#x1F6E1;&#xFE0F;</div>
    <div class="iam-header-text">
        <h2>Gestion de <span>Identidad y Acceso</span></h2>
        <p>Administra usuarios del sistema, niveles de privilegio y estatus de cuentas.</p>
    </div>
</div>

<!-- ALERTA -->
<?php if (!empty($mensaje_usuarios)) : ?>
    <?php
        $es_err    = stripos($mensaje_usuarios, 'error') !== false
                  || stripos($mensaje_usuarios, 'ya esta') !== false
                  || stripos($mensaje_usuarios, 'ya está') !== false;
        $cls_alert = $es_err ? 'iam-alert-error' : 'iam-alert-success';
        $ico_alert = $es_err ? '&#x2717;'        : '&#x2713;';
    ?>
    <div class="iam-alert <?= $cls_alert ?>" role="alert" aria-live="polite" id="iam-system-alert">
        <span class="iam-alert-icon"><?= $ico_alert ?></span>
        <span><?= htmlspecialchars($mensaje_usuarios, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
<?php endif; ?>

<!-- PANEL ALTA -->
<div class="iam-card" id="panel-alta-usuario">
    <div class="iam-card-header">
        <div class="iam-card-header-icon">&#x2795;</div>
        <div class="iam-card-header-text">
            <h3>Registrar nuevo usuario</h3>
            <p>Crea una cuenta con credenciales cifradas y nivel de acceso inicial</p>
        </div>
    </div>

    <form method="POST" action="/usuarios" novalidate id="form-crear-usuario">
        <input type="hidden" name="accion" value="crear">

        <div class="iam-form-grid">
            <div class="iam-form-group">
                <label class="iam-label" for="iam-email-nuevo">
                    Correo electronico <span class="iam-required">*</span>
                </label>
                <input type="email" class="iam-input" id="iam-email-nuevo" name="email"
                       placeholder="usuario@dominio.com" required autocomplete="off" spellcheck="false">
            </div>

            <div class="iam-form-group">
                <label class="iam-label" for="iam-password-nuevo">
                    Contrasena <span class="iam-required">*</span>
                </label>
                <input type="password" class="iam-input" id="iam-password-nuevo" name="password"
                       placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required autocomplete="new-password">
            </div>

            <div class="iam-form-group">
                <label class="iam-label" for="iam-nivel-nuevo">
                    Nivel de acceso <span class="iam-required">*</span>
                </label>
                <div class="iam-select-wrap">
                    <select class="iam-select" id="iam-nivel-nuevo" name="nivel_acceso" required>
                        <option value="0">0 &mdash; Publico</option>
                        <option value="10">10 &mdash; Estudiante</option>
                        <option value="20">20 &mdash; Docente</option>
                        <option value="30">30 &mdash; Cobranza</option>
                        <option value="40">40 &mdash; Coord. Nivel</option>
                        <option value="50">50 &mdash; Coord. General</option>
                        <option value="60">60 &mdash; Subdireccion</option>
                        <option value="70">70 &mdash; Direccion</option>
                        <option value="100">100 &mdash; SuperAdmin</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="iam-form-footer">
            <button type="submit" class="iam-btn-submit" id="btn-registrar-usuario">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Registrar Usuario
            </button>
        </div>
    </form>
</div>

<!-- PANEL TABLA -->
<div class="iam-card" id="panel-tabla-usuarios">
    <?php $total_usuarios = count($lista_usuarios ?? []); ?>
    <div class="iam-table-meta">
        <div class="iam-table-meta-left">
            <div class="iam-table-meta-icon">&#x1F4CB;</div>
            <div class="iam-table-meta-text">
                <h3>Directorio de Usuarios</h3>
                <p>Monitoreo de cuentas, privilegios y estatus en tiempo real</p>
            </div>
        </div>
        <span class="iam-count-badge">
            <?= $total_usuarios ?> registro<?= $total_usuarios !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (!empty($lista_usuarios)) : ?>
        <div class="iam-table-scroll">
            <table class="iam-table" id="tabla-usuarios">
                <thead>
                    <tr>
                        <th scope="col"># ID</th>
                        <th scope="col">Email</th>
                        <th scope="col">Nivel</th>
                        <th scope="col">Estatus</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $fila) : ?>
                        <?php
                            $nivel_num = (int) $fila['nivel_acceso'];
                            switch ($nivel_num) {
                                case 0:   $nivel_label = 'Publico';        break;
                                case 10:  $nivel_label = 'Estudiante';     break;
                                case 20:  $nivel_label = 'Docente';        break;
                                case 30:  $nivel_label = 'Cobranza';       break;
                                case 40:  $nivel_label = 'Coord. Nivel';   break;
                                case 50:  $nivel_label = 'Coord. General'; break;
                                case 60:  $nivel_label = 'Subdireccion';   break;
                                case 70:  $nivel_label = 'Direccion';      break;
                                case 100: $nivel_label = 'SuperAdmin';     break;
                                default:  $nivel_label = 'Nivel ' . $nivel_num; break;
                            }
                            $nivel_css_map = [0=>'n-0',10=>'n-10',20=>'n-20',30=>'n-30',
                                              40=>'n-40',50=>'n-50',60=>'n-60',
                                              70=>'n-70',100=>'n-100'];
                            $nivel_clase = $nivel_css_map[$nivel_num] ?? 'n-0';
                            $activo      = (int) $fila['activo'];
                            $es_activo   = ($activo === 1);
                            $safe_id     = htmlspecialchars((string)$fila['id'],    ENT_QUOTES, 'UTF-8');
                            $safe_email  = htmlspecialchars((string)$fila['email'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr id="fila-usuario-<?= $safe_id ?>">
                            <td class="iam-cell-id">#<?= $safe_id ?></td>
                            <td><span class="iam-cell-email" title="<?= $safe_email ?>"><?= $safe_email ?></span></td>
                            <td>
                                <span class="iam-nivel <?= $nivel_clase ?>">
                                    <?= $nivel_num ?> &mdash; <?= htmlspecialchars($nivel_label, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($es_activo) : ?>
                                    <span class="iam-estatus iam-activo"><span class="iam-dot"></span> Activo</span>
                                <?php else : ?>
                                    <span class="iam-estatus iam-suspendido"><span class="iam-dot"></span> Suspendido</span>
                                <?php endif; ?>
                            </td>
                            <td class="iam-actions-cell">
                                <div class="iam-actions-wrap">

                                    <form method="POST" action="/usuarios" class="iam-mini-form" id="form-nivel-<?= $safe_id ?>">
                                        <input type="hidden" name="accion"     value="cambiar_nivel">
                                        <input type="hidden" name="usuario_id" value="<?= $safe_id ?>">
                                        <select name="nuevo_nivel" class="iam-mini-select" id="select-nivel-<?= $safe_id ?>" aria-label="Nuevo nivel usuario #<?= $safe_id ?>">
                                            <option value="0"   <?= $nivel_num===0   ?'selected':'' ?>>0 &mdash; Publico</option>
                                            <option value="10"  <?= $nivel_num===10  ?'selected':'' ?>>10 &mdash; Estudiante</option>
                                            <option value="20"  <?= $nivel_num===20  ?'selected':'' ?>>20 &mdash; Docente</option>
                                            <option value="30"  <?= $nivel_num===30  ?'selected':'' ?>>30 &mdash; Cobranza</option>
                                            <option value="40"  <?= $nivel_num===40  ?'selected':'' ?>>40 &mdash; Coord. Nivel</option>
                                            <option value="50"  <?= $nivel_num===50  ?'selected':'' ?>>50 &mdash; Coord. Gral.</option>
                                            <option value="60"  <?= $nivel_num===60  ?'selected':'' ?>>60 &mdash; Subdireccion</option>
                                            <option value="70"  <?= $nivel_num===70  ?'selected':'' ?>>70 &mdash; Direccion</option>
                                            <option value="100" <?= $nivel_num===100 ?'selected':'' ?>>100 &mdash; SuperAdmin</option>
                                        </select>
                                        <button type="submit" class="iam-btn-mini iam-btn-update" id="btn-nivel-<?= $safe_id ?>" title="Actualizar nivel">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="10" height="10">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            </svg>
                                            Actualizar
                                        </button>
                                    </form>

                                    <div class="iam-sep"></div>

                                    <form method="POST" action="/usuarios" class="iam-mini-form" id="form-estatus-<?= $safe_id ?>">
                                        <input type="hidden" name="accion"         value="toggle_estatus">
                                        <input type="hidden" name="usuario_id"     value="<?= $safe_id ?>">
                                        <input type="hidden" name="estatus_actual" value="<?= $activo ?>">
                                        <?php if ($es_activo) : ?>
                                            <button type="submit" class="iam-btn-mini iam-btn-suspend" id="btn-estatus-<?= $safe_id ?>" title="Suspender">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="10" height="10">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Suspender
                                            </button>
                                        <?php else : ?>
                                            <button type="submit" class="iam-btn-mini iam-btn-reactivar" id="btn-estatus-<?= $safe_id ?>" title="Reactivar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="10" height="10">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Reactivar
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else : ?>
        <div class="iam-empty" id="iam-empty-state">
            <div class="iam-empty-icon">&#x1F464;</div>
            <p>No hay usuarios registrados todavia.<br>
               Usa el formulario superior para crear el primer registro.</p>
        </div>
    <?php endif; ?>

</div>
