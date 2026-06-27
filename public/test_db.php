<?php
// public/test_db.php (Compilador de Rutas Axe)

// 1. Cargar la conexión a la base de datos
require __DIR__ . '/../config/database.php';

try {
    // 2. Extraer todas las rutas de la tabla
    $stmt = $pdo->query("SELECT uri, vista, plantilla, controlador, requiere_login, nivel_minimo, css, js FROM rutas");
    $rutas_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $rutas_procesadas = [];

    // 3. Formatear los datos para el enrutador
    foreach ($rutas_db as $ruta) {
        // Decodificar los JSON a arreglos nativos de PHP. Si está vacío, asignar arreglo en blanco.
        $ruta['css'] = !empty($ruta['css']) ? json_decode($ruta['css'], true) : [];
        $ruta['js'] = !empty($ruta['js']) ? json_decode($ruta['js'], true) : [];
        
        // Convertir el tinyint a booleano estricto
        $ruta['requiere_login'] = (bool) $ruta['requiere_login'];

        // Asignar la ruta limpia al arreglo usando la URI como llave
        $rutas_procesadas[$ruta['uri']] = $ruta;
    }

    // 4. Construir la cadena de texto con código PHP válido
    $contenido_cache = "<?php\n";
    $contenido_cache .= "// Archivo auto-generado por el Compilador de Axe Framework\n";
    $contenido_cache .= "// Fecha de última compilación: " . date('Y-m-d H:i:s') . "\n\n";
    $contenido_cache .= "return " . var_export($rutas_procesadas, true) . ";\n";

    // 5. Escribir el archivo físico en la carpeta config
    $ruta_archivo_cache = __DIR__ . '/../config/rutas_cache.php';
    file_put_contents($ruta_archivo_cache, $contenido_cache);

    // 6. Confirmación visual en pantalla
    echo "<div style='font-family: system-ui, sans-serif; max-width: 800px; margin: 40px auto; color: #333;'>";
    echo "<h2 style='color: #15803d; border-bottom: 2px solid #15803d; padding-bottom: 10px;'>✅ Compilación Exitosa</h2>";
    echo "<p>El mapa de rutas ha sido regenerado. El sistema ya reconoce las nuevas direcciones y la inyección de assets.</p>";
    echo "<h3 style='margin-top: 30px;'>Código generado en <code>config/rutas_cache.php</code>:</h3>";
    echo "<pre style='background: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 8px; overflow-x: auto; font-size: 14px;'>" . htmlspecialchars($contenido_cache) . "</pre>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='font-family: system-ui, sans-serif; max-width: 800px; margin: 40px auto; color: #333;'>";
    echo "<h2 style='color: #b91c1c; border-bottom: 2px solid #b91c1c; padding-bottom: 10px;'>❌ Error de Compilación</h2>";
    echo "<p>Ocurrió un problema al leer la base de datos:</p>";
    echo "<code>" . $e->getMessage() . "</code>";
    echo "</div>";
}