<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Axe Framework</title>
    <style>
        /* Paleta y variables base de Axe */
        :root {
            --fondo: #0b101a; 
            --tarjeta: #121a2f;
            --texto: #e2e8f0;
            --texto-secundario: #94a3b8;
            --borde: #1e2c4a;
            
            --axe-rojo: #d92534;
            --axe-azul: #2b5b94;
            --axe-dorado: #f5b700;
            
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
            background-image: 
                linear-gradient(rgba(30, 44, 74, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(30, 44, 74, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Barra de navegación superior */
        nav {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--borde);
            background: rgba(11, 16, 26, 0.9);
            backdrop-filter: blur(5px);
        }

        .nav-logo {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--axe-dorado);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .nav-links a {
            color: var(--texto-secundario);
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
            transition: color 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-links a:hover {
            color: var(--axe-rojo);
        }

        /* Contenedor principal Hero */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
        }

        .hero-logo img {
            max-width: 220px;
            height: auto;
            margin-bottom: 30px;
            filter: drop-shadow(0 0 20px var(--glow-azul));
            animation: flotar 4s ease-in-out infinite;
        }

        @keyframes flotar {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        h1 { 
            font-size: 3.5rem; 
            margin: 0 0 10px 0; 
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(90deg, var(--axe-azul), var(--axe-rojo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .slogan {
            font-size: 1.4rem;
            color: var(--texto-secundario);
            margin-bottom: 40px;
            max-width: 600px;
        }

        /* Simulación de Terminal */
        .terminal {
            background: #05080f;
            border: 1px solid var(--borde);
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            max-width: 600px;
            text-align: left;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            margin-bottom: 40px;
            position: relative;
        }

        .terminal-header {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--borde);
            padding-bottom: 10px;
        }

        .punto {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .punto.rojo { background: var(--axe-rojo); }
        .punto.dorado { background: var(--axe-dorado); }
        .punto.azul { background: var(--axe-azul); }

        .codigo {
            font-family: 'Courier New', Courier, monospace;
            color: #10b981; /* Verde terminal */
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .codigo span.comentario { color: #64748b; }
        .codigo span.variable { color: var(--axe-dorado); }
        .codigo span.funcion { color: var(--axe-azul); }
        .codigo span.string { color: var(--axe-rojo); }

        /* Botones */
        .btn-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-primario {
            background: transparent;
            color: var(--axe-rojo);
            border: 1px solid var(--axe-rojo);
        }

        .btn-primario:hover {
            background: var(--axe-rojo);
            color: #fff;
            box-shadow: 0 0 15px var(--glow-rojo);
        }

        .btn-secundario {
            background: transparent;
            color: var(--axe-dorado);
            border: 1px solid var(--axe-dorado);
        }

        .btn-secundario:hover {
            background: var(--axe-dorado);
            color: var(--fondo);
            box-shadow: 0 0 15px rgba(245, 183, 0, 0.4);
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid var(--borde);
            color: var(--texto-secundario);
            font-size: 0.85rem;
            background: rgba(11, 16, 26, 0.9);
        }
        
        @media (max-width: 600px) {
            h1 { font-size: 2.5rem; }
            .terminal { font-size: 0.85rem; }
            .nav-links { display: none; } /* Ocultar links en móvil para simplificar */
        }
    </style>
</head>
<body>



    <div class="hero">
        <div class="hero-logo">
            <!-- Sustituye la ruta por la ubicación real de tu logo -->
            <img src="/ruta/a/tu/image_995dc1.png" alt="Axe Escudo">
        </div>
        
        <h1>Axe Framework</h1>
        <p class="slogan">Secure Routing System. Arquitectura de adorno-cero construida para la velocidad, control absoluto y blindaje forense.</p>

        <div class="terminal">
            <div class="terminal-header">
                <div class="punto rojo"></div>
                <div class="punto dorado"></div>
                <div class="punto azul"></div>
            </div>
            <div class="codigo">
                <span class="comentario">// Inicializando motor de enrutamiento estricto</span><br>
                <span class="variable">$router</span>-><span class="funcion">get</span>(<span class="string">'/panel'</span>, <span class="string">'Dashboard@index'</span>, [<span class="string">'nivel_minimo'</span> => <span class="variable">1</span>]);<br>
                <span class="variable">$router</span>-><span class="funcion">get</span>(<span class="string">'/auditoria'</span>, <span class="string">'Forense@ver'</span>, [<span class="string">'nivel_minimo'</span> => <span class="variable">10</span>]);<br>
                <br>
                <span class="comentario">// Ejecución sin dependencias</span><br>
                <span class="variable">$router</span>-><span class="funcion">compilar</span>();<br>
                <span class="comentario">>> Carga completada en 0.012s. Sistema asegurado.</span>
            </div>
        </div>

        <div class="btn-container">
            <a href="/dashboard" class="btn btn-primario">Ingresar al Sistema</a>
            <a href="/nosotros" class="btn btn-secundario">Conocer la Arquitectura</a>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Axe Framework. Desarrollado bajo protocolo estricto.
    </footer>

</body>
</html>
