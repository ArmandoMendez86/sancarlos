<?php
// Archivo: app/controllers/ConfiguracionController.php

require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionController
{
    private $model;

    public function __construct()
    {
        $this->model = new ConfiguracionModel();
    }

    public function obtener()
    {
        header('Content-Type: application/json');
        $config = $this->model->obtenerConfiguracion();
        if ($config) {
            echo json_encode(['success' => true, 'data' => $config]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Configuración no encontrada.']);
        }
    }

    public function guardar()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no válido.']);
            return;
        }

        $data = $_POST;
        $data['lunes_abierto'] = isset($data['lunes_abierto']) ? 1 : 0;
        $data['martes_abierto'] = isset($data['martes_abierto']) ? 1 : 0;
        $data['miercoles_abierto'] = isset($data['miercoles_abierto']) ? 1 : 0;
        $data['jueves_abierto'] = isset($data['jueves_abierto']) ? 1 : 0;
        $data['viernes_abierto'] = isset($data['viernes_abierto']) ? 1 : 0;
        $data['sabado_abierto'] = isset($data['sabado_abierto']) ? 1 : 0;
        $data['domingo_abierto'] = isset($data['domingo_abierto']) ? 1 : 0;

        $result = $this->model->guardarConfiguracion($data);
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Configuración guardada con éxito.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }

    // Método auxiliar para la lógica de cobro
    public function obtenerHorarioDia($day)
    {
        $config = $this->model->obtenerConfiguracion();
        $dia = strtolower($day);

        return [
            'abierto' => $config[$dia . '_abierto'],
            'apertura' => $config[$dia . '_apertura'],
            'cierre' => $config[$dia . '_cierre']
        ];
    }
}