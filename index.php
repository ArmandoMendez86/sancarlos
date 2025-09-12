<?php

// 1. Obtener la URL solicitada
$url = isset($_GET['url']) ? $_GET['url'] : 'panel';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);


// 2. Definir las rutas y sus vistas asociadas
$routes = [
    'panel' => 'public/vistas/panel.php', // Ejemplo: si la URL es "home"
    'registro_entrada' => 'public/vistas/registro_entrada.php',
    'cobro' => 'public/vistas/cobro.php',
    'configuracion' => 'public/vistas/configuracion.php',
    'vehiculos_activos' => 'public/vistas/activos.php',
    'caja' => 'public/vistas/corte.php',
    
    // Agrega aquí todas tus rutas y sus archivos de vista
];


// 3. Cargar la vista correspondiente
$requested_path = $url[0];

if (array_key_exists($requested_path, $routes)) {
    // Si la ruta existe en nuestro arreglo, carga la vista
    require_once $routes[$requested_path];
} else {
    // Si la ruta no existe, muestra una página de error 404
    http_response_code(404);
    echo "<h1>Error 404: Página no encontrada</h1>";
    // O puedes cargar una vista de error 404 específica
    // require_once 'vistas/404.php';
}