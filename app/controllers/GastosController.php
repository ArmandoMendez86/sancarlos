<?php
// Archivo: app/controllers/GastosController.php

require_once __DIR__ . '/../models/GastosModel.php';

class GastosController
{
    private $gastosModel;

    public function __construct()
    {
        $this->gastosModel = new GastosModel();
    }

    /**
     * Registra un nuevo gasto.
     * Espera una solicitud POST con los datos del formulario.
     */
    public function registrar()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
            return;
        }

        if (empty($_POST['concepto']) || !isset($_POST['monto'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Concepto y monto son obligatorios.']);
            return;
        }

        $result = $this->gastosModel->registrarGasto($_POST);

        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Gasto registrado con éxito.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }
}