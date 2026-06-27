<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Axe Framework</title>
    <style>
        /* Paleta extraída del escudo Axe: Fondo oscuro, neón rojo, azul escudo y dorado circuito */
        :root {
            --fondo: #0b101a; 
            --tarjeta: #121a2f;
            --texto: #e2e8f0;
            --texto-secundario: #94a3b8;
            --borde: #1e2c4a;
            
            /* Colores de Marca */
            --axe-rojo: #d92534;
            --axe-azul: #2b5b94;
            --axe-dorado: #f5b700;
            
            /* Brillos Neón */
            --glow-rojo: rgba(217, 37, 52, 0.4);
            --glow-azul: rgba(43, 91, 148, 0.4);
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--fondo);
            color: var(--texto);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            /* Patrón de malla sutil de fondo hecho con CSS puro */
            background-image: 
                linear-gradient(rgba(30, 44, 74, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(30, 44, 74, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .contenedor {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        header {
            text-align: center;
            margin-bottom: 60px;
            border-bottom: 1px solid var(--borde);
            padding-bottom: 40px;
        }

        /* Espacio para tu Logo */
        .logo-container img {
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 15px var(--glow-azul));
        }

        h1 { 
            font-size: 2.8rem; 
            margin-bottom: 10px; 
            text-transform: uppercase;
            letter-spacing: 2px;
            /* Gradiente de texto inspirado en la dualidad del escudo */
            background: linear-gradient(90deg, var(--axe-azul), var(--axe-rojo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 { 
            color: var(--axe-dorado); 
            margin-top: 50px; 
            border-left: 4px solid var(--axe-rojo);
            padding-left: 15px;
        }

        .subtitulo { 
            font-size: 1.2rem; 
            color: var(--texto-secundario); 
            letter-spacing: 1px;
        }
        
        /* Grid de Pilares con efecto Neón */
        .grid-pilares {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .pilar {
            background: var(--tarjeta);
            padding: 25px;
            border-radius: 6px;
            border: 1px solid var(--borde);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        /* Línea superior de adorno técnico en la tarjeta */
        .pilar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--axe-azul);
            transition: background 0.3s ease;
        }

        .pilar:hover {
            transform: translateY(-5px);
            border-color: var(--axe-rojo);
            box-shadow: 0 8px 25px var(--glow-rojo);
        }

        .pilar:hover::before {
            background: var(--axe-rojo);
        }

        .pilar h3 { margin-top: 0; color: #fff; }
        .pilar p { font-size: 0.95rem; color: var(--texto-secundario); }
        
        /* Sección del Arquitecto */
        .perfil-arquitecto {
            display: flex;
            align-items: center;
            gap: 40px;
            background: var(--tarjeta);
            padding: 40px;
            border-radius: 8px;
            margin-top: 60px;
            border: 1px solid var(--borde);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }

        .foto-placeholder {
            flex-shrink: 0;
            width: 250px;
            height: 250px;
            border-radius: 4px;
            overflow: hidden;
            border: 2px solid var(--axe-azul);
            box-shadow: 0 0 20px var(--glow-azul);
        }

        .foto-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(20%) contrast(120%);
        }

        .info-perfil h3 { 
            margin-top: 0; 
            font-size: 1.8rem; 
            color: #fff;
        }
        
        /* Botones estilo Terminal/Cyber */
        .btn {
            display: inline-block;
            background: transparent;
            color: var(--axe-dorado);
            padding: 12px 30px;
            text-decoration: none;
            border: 1px solid var(--axe-dorado);
            border-radius: 4px;
            margin-top: 40px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn:hover {
            background: var(--axe-dorado);
            color: var(--fondo);
            box-shadow: 0 0 15px rgba(245, 183, 0, 0.4);
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .perfil-arquitecto { flex-direction: column; text-align: center; }
            .foto-placeholder { width: 200px; height: 200px; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <div class="contenedor">
        <header>
            <div class="logo-container">
                <!-- Coloca la ruta de tu logo aquí -->
                <img src="/ruta/a/tu/image_995dc1.png" alt="Axe Framework Logo">
            </div>
            <h1>Axe Framework</h1>
            <p class="subtitulo">Arquitectura PHP de Alto Rendimiento. Control Absoluto.</p>
        </header>

        <section>
            <h2>La Filosofía del Sistema</h2>
            <p>Axe Framework nace de la necesidad de escapar del código inflado y las dependencias innecesarias de los entornos comerciales masivos. Nuestra arquitectura está diseñada bajo el protocolo <strong>"adorno-cero"</strong>: cada línea de código tiene un propósito operativo estricto. El objetivo es recuperar el control absoluto sobre la seguridad, la gestión de bases de datos y los ciclos de ejecución, garantizando un rendimiento óptimo sin sacrificar la flexibilidad estructural.</p>
        </section>

        <section>
            <h2>Motores de la Arquitectura</h2>
            <div class="grid-pilares">
                <div class="pilar">
                    <h3>Enrutador Nativo</h3>
                    <p>Gestión de URIs limpia y optimizada mediante caché estricta, eliminando la sobrecarga de procesamiento en peticiones.</p>
                </div>
                <div class="pilar">
                    <h3>Núcleo de Seguridad</h3>
                    <p>Implementación de criptografía y patrón Split Token para un blindaje total contra suplantación y secuestro de sesiones.</p>
                </div>
                <div class="pilar">
                    <h3>Control IAM</h3>
                    <p>Gestión de identidades y accesos estructurada por niveles jerárquicos y aislamiento vertical de privilegios.</p>
                </div>
                <div class="pilar">
                    <h3>Auditoría Forense</h3>
                    <p>Registro inmutable y silencioso de eventos críticos del sistema, operando en tiempo real directamente en la base de datos.</p>
                </div>
            </div>
        </section>

        <section class="perfil-arquitecto">
            <div class="foto-placeholder">
                <!-- Sustituye con tu foto cuando la tengas -->
                <img src="https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80" alt="Terminal de servidor">
            </div>
            <div class="info-perfil">
                <h3>El Arquitecto del Sistema</h3>
                <p>Desarrollado por <strong>Javier</strong>, profesional en Marketing Internacional y programador backend autodidacta. Axe Framework surge de la aplicación cruzada entre la gestión de operaciones y la ingeniería de software.</p>
                <p>Con experiencia en la estandarización de procesos de alto volumen, el enfoque arquitectónico consiste en construir ecosistemas digitales resilientes. Axe está diseñado para sostener la lógica dura detrás de plataformas de gestión administrativa e infraestructuras de validación, priorizando la precisión de los datos y la inquebrantabilidad operativa.</p>
            </div>
        </section>

        <div style="text-align: center;">
            <a href="/dashboard" class="btn">Retornar al Dashboard</a>
        </div>
    </div>

</body>
</html>