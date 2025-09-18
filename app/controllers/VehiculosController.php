<?php
// Archivo: app/controllers/VehiculosController.php

require_once __DIR__ . '/../models/VehiculosModel.php';

class VehiculosController
{
    private $vehiculosModel;

    public function __construct()
    {
        $this->vehiculosModel = new VehiculosModel();
    }

    public function tipos()
    {
        header('Content-Type: application/json');
        $tiposVehiculos = $this->vehiculosModel->getTiposVehiculos();
        if ($tiposVehiculos !== null) {
            echo json_encode(['success' => true, 'data' => $tiposVehiculos]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener los tipos de vehículos.']);
        }
    }
}