<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acerca de Axe - System Info</title>
    <style>
        :root {
            --fondo: #0b101a; 
            --tarjeta: #121a2f;
            --texto: #e2e8f0;
            --texto-secundario: #94a3b8;
            --borde: #1e2c4a;
            --axe-rojo: #d92534;
            --axe-azul: #2b5b94;
            --axe-dorado: #f5b700;
            --exito: #10b981; /* Verde terminal */
        }

        body {
            font-family: 'Courier New', Courier, monospace; /* Tipografía monoespaciada para el reporte técnico */
            background-color: var(--fondo);
            color: var(--texto);
            line-height: 1.6;
            margin: 0;
            padding: 40px 20px;
            background-image: 
                linear-gradient(rgba(30, 44, 74, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(30, 44, 74, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .contenedor {
            max-width: 800px;
            margin: 0 auto;
        }

        .panel-diagnostico {
            background: rgba(18, 26, 47, 0.85);
            border: 1px solid var(--borde);
            border-left: 4px solid var(--axe-azul);
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border-radius: 4px;
        }

        .cabecera-panel {
            border-bottom: 1px dashed var(--borde);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        h1 {
            font-family: system-ui, -apple-system, sans-serif;
            color: var(--axe-dorado);
            margin: 0 0 5px 0;
            text-transform: uppercase;
            font-size: 2rem;
            letter-spacing: 2px;
        }

        .subtitulo {
            color: var(--texto-secundario);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Tabla de especificaciones */
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .spec-item {
            background: rgba(0,0,0,0.3);
            padding: 12px 15px;
            border: 1px solid var(--borde);
            border-radius: 3px;
        }

        .spec-label {
            color: var(--texto-secundario);
            font-size: 0.85rem;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }

        .spec-value {
            color: var(--texto);
            font-size: 1.1rem;
            font-weight: bold;
        }

        .estado-verde { color: var(--exito); text-shadow: 0 0 5px rgba(16, 185, 129, 0.4); }
        .estado-rojo { color: var(--axe-rojo); }

        /* Lista de Módulos */
        .modulos-lista {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
            border-top: 1px dashed var(--borde);
            padding-top: 20px;
        }

        .modulos-lista li {
            padding: 10px 0;
            border-bottom: 1px solid rgba(30, 44, 74, 0.4);
            display: flex;
            justify-content: space-between;
        }

        .legal {
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 0.8rem;
            color: var(--texto-secundario);
            text-align: justify;
            border-top: 1px solid var(--borde);
            padding-top: 20px;
        }

        .btn-retorno {
            display: inline-block;
            margin-top: 20px;
            color: var(--axe-dorado);
            text-decoration: none;
            border: 1px solid var(--axe-dorado);
            padding: 8px 20px;
            border-radius: 3px;
            font-family: system-ui, -apple-system, sans-serif;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-retorno:hover {
            background: var(--axe-dorado);
            color: var(--fondo);
        }

        @media (max-width: 600px) {
            .specs-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="contenedor">
        <div class="panel-diagnostico">
            
            <div class="cabecera-panel">
                <h1>Axe Core Info</h1>
                <p class="subtitulo">REPORTE DE DIAGNÓSTICO DEL SISTEMA EN TIEMPO REAL</p>
            </div>

            <div class="specs-grid">
                <div class="spec-item">
                    <span class="spec-label">Versión del Núcleo</span>
                    <span class="spec-value">v1.2.0 (Build: Caucel)</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Entorno de Ejecución</span>
                    <span class="spec-value">PHP <?= phpversion() ?></span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Software del Servidor</span>
                    <span class="spec-value"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?></span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Huella de Memoria (RAM)</span>
                    <span class="spec-value"><?= round(memory_get_peak_usage(true) / 1024 / 1024, 2) ?> MB</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Arquitectura Arquitecto</span>
                    <span class="spec-value">Javier</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Estampa de Tiempo (Local)</span>
                    <span class="spec-value"><?= date('Y-m-d H:i:s') ?></span>
                </div>
            </div>

            <h3 style="color: var(--axe-azul); margin-bottom: 10px; font-family: system-ui;">ESTADO DE MÓDULOS</h3>
            <ul class="modulos-lista">
                <li>
                    <span>Enrutador Nativo (Caché Compilada)</span>
                    <span class="estado-verde">[ EN LÍNEA ]</span>
                </li>
                <li>
                    <span>Núcleo de Seguridad (Split Token)</span>
                    <span class="estado-verde">[ ACTIVO ]</span>
                </li>
                <li>
                    <span>Motor IAM (Control de Niveles)</span>
                    <span class="estado-verde">[ EN LÍNEA ]</span>
                </li>
                <li>
                    <span>Bitácora Forense Base de Datos</span>
                    <span class="estado-verde">[ REGISTRANDO ]</span>
                </li>
            </ul>

            <div class="legal">
                <strong>Aviso de Privacidad y Licencia:</strong> Axe Framework es una arquitectura propietaria desarrollada bajo el protocolo estricto "adorno-cero" para entornos de alta seguridad y control de identidades. La copia, distribución o manipulación del código fuente sin autorización del arquitecto del sistema está estrictamente prohibida. 
            </div>

        </div>

        <a href="/" class="btn-retorno">&laquo; Retornar</a>
    </div>

</body>
</html>