<?php
// Archivo: app/controllers/EntradasController.php

require_once __DIR__ . '/../models/EntradasModel.php';

class EntradasController
{
    private $entradasModel;

    public function __construct()
    {
        $this->entradasModel = new EntradasModel();
    }

    public function obtenerActivas()
    {
        header('Content-Type: application/json');
        $entradas = $this->entradasModel->obtenerEntradasActivas();
        if ($entradas !== null) {
            echo json_encode(['success' => true, 'data' => $entradas]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener los registros.']);
        }
    }

    public function registrar()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
            return;
        }
        if (empty($_POST['placa']) || empty($_POST['vehiculos_id'])) {
            echo json_encode(['success' => false, 'message' => 'Placa y tipo de vehículo son obligatorios.']);
            return;
        }
        $result = $this->entradasModel->registrarEntrada($_POST);
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Entrada registrada con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }
}