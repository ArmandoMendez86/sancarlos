<?php
// Archivo: app/controllers/SalidasController.php

require_once __DIR__ . '/../models/SalidasModel.php';
require_once __DIR__ . '/ConfiguracionController.php';

class SalidasController
{
    private $salidasModel;
    private $configuracionController;

    public function __construct()
    {
        $this->salidasModel = new SalidasModel();
        $this->configuracionController = new ConfiguracionController();
    }

    public function obtenerDetalles()
    {
        header('Content-Type: application/json');
        date_default_timezone_set('America/Mexico_City');

        $placa = $_GET['placa'] ?? null;

        if (empty($placa)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Placa no especificada.']);
            return;
        }

        $vehiculo = $this->salidasModel->obtenerDetallesPorPlaca($placa);

        if (!$vehiculo) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Vehículo no encontrado.']);
            return;
        }

        // --- INICIO DE MODIFICACIÓN ---

        // Obtener el horario de apertura y cierre del día de entrada
        $fechaEntrada = new DateTime($vehiculo['fecha_entrada']);
        $dias = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];
        $diaSemana = strtolower($fechaEntrada->format('l'));
        $nombreDiaES = $dias[$diaSemana];

        $horarioDia = $this->configuracionController->obtenerHorarioDia($nombreDiaES);

        $vehiculo['hora_apertura'] = $horarioDia['abierto'] ? date('H:i', strtotime($horarioDia['apertura'])) : 'Cerrado';
        $vehiculo['hora_cierre'] = $horarioDia['abierto'] ? date('H:i', strtotime($horarioDia['cierre'])) : 'Cerrado';

        // --- FIN DE MODIFICACIÓN ---

        // Lógica de cobro base
        $tarifa = $vehiculo['tarifa'];
        $fechaSalida = new DateTime();
        $intervalo = $fechaEntrada->diff($fechaSalida);
        $minutosTotales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;
        $costoNormal = 0;

        if ($minutosTotales > 3) {
            $minutosCobrados = max(0, $minutosTotales - 3);
            $costoNormal += $tarifa; // Primera hora o fracción
            $minutosCobrados -= 60;

            while ($minutosCobrados > 0) {
                if ($minutosCobrados > 10) { // Tolerancia de 10 min por hora
                    $costoNormal += $tarifa;
                }
                $minutosCobrados -= 60;
            }
        }

        $vehiculo['fecha_salida'] = $fechaSalida->format('Y-m-d H:i:s');
        $vehiculo['tiempo_total'] = $intervalo->format('%a días, %h horas y %i minutos');
        $vehiculo['costo_base'] = $costoNormal;

        echo json_encode(['success' => true, 'data' => $vehiculo]);
    }

    public function registrar()
    {
        header('Content-Type: application/json');
        date_default_timezone_set('America/Mexico_City');

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['entrada_id']) || !isset($data['cobro'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos incompletos para el registro de salida.']);
            return;
        }

        // Determinar tipo de cobro para el registro
        $tipoCobro = 'normal';
        if ($data['es_pension'] == 1) {
            $tipoCobro = 'pension';
        }
        if ($data['boleto_perdido'] > 0) {
            $tipoCobro = 'boleto_perdido';
        }

        $result = $this->salidasModel->registrarSalida([
            'tipo_cobro' => $tipoCobro,
            'cobro' => $data['cobro'],
            'boleto_perdido' => $data['boleto_perdido'],
            'es_pension' => $data['es_pension'],
            'entrada_id' => $data['entrada_id']
        ]);

        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Salida registrada con éxito.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $result]);
        }
    }
}