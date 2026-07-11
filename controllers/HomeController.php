<?php

/**
 * controllers/HomeController.php
 *
 * CONTROLADOR DE LA RUTA RAÍZ  "/"
 * ─────────────────────────────────────────────────────────────────────────────
 * Responsabilidad única: preparar los datos que necesita views/home.php.
 *
 * Convención del framework:
 *   - Este archivo es requerido (require) directamente por el enrutador
 *     (public/index.php) en modo procedimental, ANTES de abrir el búfer
 *     de salida (ob_start).
 *   - Los datos se declaran en el arreglo asociativo $datos_vista.
 *   - El enrutador extrae sus claves con extract($datos_vista), dejando
 *     cada clave disponible como variable en el ámbito de la vista.
 *
 * Restricciones:
 *   - Sin clases ni métodos: flujo 100 % procedimental.
 *   - Sin salida directa (echo / print): solo preparación de datos.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// Arreglo de datos que será inyectado en la vista mediante extract().
$datos_vista = [
    'titulo' => 'Bienvenido a Axe Framework',
];
