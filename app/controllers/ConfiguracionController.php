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

    public function obtenerHorarioDia($day)
    {
        $config = $this->model->obtenerConfiguracion();
        $map = [
            'lunes'     => ['abierto' => 'lunes_abierto',     'apertura' => 'lunes_apertura',     'cierre' => 'lunes_cierre'],
            'martes'    => ['abierto' => 'martes_abierto',    'apertura' => 'martes_apertura',    'cierre' => 'martes_cierre'],
            'miercoles' => ['abierto' => 'miercoles_abierto', 'apertura' => 'miercoles_apertura', 'cierre' => 'miercoles_cierre'],
            'jueves'    => ['abierto' => 'jueves_abierto',    'apertura' => 'jueves_apertura',    'cierre' => 'jueves_cierre'],
            'viernes'   => ['abierto' => 'viernes_abierto',   'apertura' => 'viernes_apertura',   'cierre' => 'viernes_cierre'],
            'sabado'    => ['abierto' => 'sabado_abierto',    'apertura' => 'sabado_apertura',    'cierre' => 'sabado_cierre'],
            'domingo'   => ['abierto' => 'domingo_abierto',   'apertura' => 'domingo_apertura',   'cierre' => 'domingo_cierre'],
        ];
        $k = $map[$day] ?? null;
        if (!$k || !$config) return ['abierto' => 0, 'apertura' => null, 'cierre' => null];

        return [
            'abierto'  => (int)$config[$k['abierto']] === 1,
            'apertura' => $config[$k['apertura']],
            'cierre'   => $config[$k['cierre']],
        ];
    }


    public function obtenerCierreHoy()
    {
        header('Content-Type: application/json');
        $dias = [
            'Sunday'    => 'domingo',
            'Monday'    => 'lunes',
            'Tuesday'   => 'martes',
            'Wednesday' => 'miercoles',
            'Thursday'  => 'jueves',
            'Friday'    => 'viernes',
            'Saturday'  => 'sabado',
        ];
        $diaActual = $dias[date('l')];
        $horario = $this->obtenerHorarioDia($diaActual);
        echo json_encode(['success' => true, 'data' => $horario]);
    }


    public function obtenerHorarioDiaAPI()
    {
        header('Content-Type: application/json');
        $dia = $_GET['dia'] ?? null;
        if (!$dia) {
            echo json_encode(['success' => false, 'message' => 'Día no especificado']);
            return;
        }
        $horario = $this->obtenerHorarioDia($dia);
        echo json_encode(['success' => true, 'data' => $horario]);
    }
}
