<?php
// Archivo: public/api/index.php


// Mostrar errores para la depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluimos todos los controladores que podamos necesitar
// Esto es más eficiente que incluirlos dinámicamente en un proyecto pequeño
require_once __DIR__ . '/../../app/controllers/EntradasController.php';
require_once __DIR__ . '/../../app/controllers/VehiculosController.php';
require_once __DIR__ . '/../../app/controllers/SalidasController.php';
//require_once __DIR__ . '/../../app/controllers/GastosController.php';
//require_once __DIR__ . '/../../app/controllers/VentasController.php';
//require_once __DIR__ . '/../../app/controllers/ConfiguracionController.php';

// Obtenemos la acción solicitada desde la URL (ej. ?action=vehiculos/tipos)
// Usamos $_GET para este ejemplo simple, pero en un router real se usa la URL amigable
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Dividimos la acción para obtener el controlador y el método
// Por ejemplo, 'vehiculos/tipos' se convierte en ['vehiculos', 'tipos']
$parts = explode('/', $action);
$controllerName = ucfirst($parts[0] ?? '') . 'Controller';
$methodName = $parts[1] ?? 'index'; // Método por defecto

if (class_exists($controllerName)) {
    $controller = new $controllerName();

    if (method_exists($controller, $methodName)) {
        // Ejecutamos el método del controlador
        $controller->$methodName();
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Método de la API no encontrado.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Controlador de la API no encontrado.']);
}