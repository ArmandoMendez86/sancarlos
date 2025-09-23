<?php
// Archivo: app/controllers/VentasController.php

require_once __DIR__ . '/../models/VentasModel.php';

class VentasController
{
    private $ventasModel;

    public function __construct()
    {
        $this->ventasModel = new VentasModel();
    }

    /**
     * Registra una venta de baños.
     */
    public function registrar()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
            return;
        }

        $result = $this->ventasModel->registrarVentaBaños();

        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Venta de baños registrada con éxito.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }
}