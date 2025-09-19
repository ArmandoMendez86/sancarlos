<?php
// app/controllers/SalidasController.php
require_once __DIR__ . '/../models/SalidasModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class SalidasController {
    private $salidasModel;
    private $configModel;

    public function __construct() {
        $this->salidasModel = new SalidasModel();
        $this->configModel  = new ConfiguracionModel();
    }

    // POST(JSON/form): { id }
    public function obtenerDetalles() {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;

        $entradaId = isset($input['id']) ? (int)$input['id'] : 0;
        if ($entradaId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de entrada inválido']); return;
        }

        $vehiculo = $this->salidasModel->getVehiculoPorEntradaId($entradaId);
        if (!$vehiculo) {
            echo json_encode(['success' => false, 'message' => 'Entrada no encontrada']); return;
        }

        // Horario dinámico según día de fecha ENTRADA
        $fechaEntrada = new DateTime($vehiculo['fecha_entrada']);
        $diaNum = (int)$fechaEntrada->format('w'); // 0 dom ... 6 sab

        $cfg = $this->configModel->obtenerConfiguracion();
        $map = [
          0 => ['abierto'=>'domingo_abierto','apertura'=>'domingo_apertura','cierre'=>'domingo_cierre'],
          1 => ['abierto'=>'lunes_abierto','apertura'=>'lunes_apertura','cierre'=>'lunes_cierre'],
          2 => ['abierto'=>'martes_abierto','apertura'=>'martes_apertura','cierre'=>'martes_cierre'],
          3 => ['abierto'=>'miercoles_abierto','apertura'=>'miercoles_apertura','cierre'=>'miercoles_cierre'],
          4 => ['abierto'=>'jueves_abierto','apertura'=>'jueves_apertura','cierre'=>'jueves_cierre'],
          5 => ['abierto'=>'viernes_abierto','apertura'=>'viernes_apertura','cierre'=>'viernes_cierre'],
          6 => ['abierto'=>'sabado_abierto','apertura'=>'sabado_apertura','cierre'=>'sabado_cierre']
        ];
        $k = $map[$diaNum];
        $abierto = isset($cfg[$k['abierto']]) ? (int)$cfg[$k['abierto']] === 1 : false;
        $horaApertura = $abierto ? $cfg[$k['apertura']] : null;
        $horaCierre   = $abierto ? $cfg[$k['cierre']]   : null;

        $vehiculo['hora_apertura'] = $abierto && $horaApertura ? date('H:i', strtotime($horaApertura)) : 'Cerrado';
        $vehiculo['hora_cierre']   = $abierto && $horaCierre   ? date('H:i', strtotime($horaCierre))   : 'Cerrado';

        // Horario completo de la semana para cálculo multi-día
        $vehiculo['horario_semana'] = [
          '0' => ['abierto'=>(int)($cfg['domingo_abierto']??0)===1, 'apertura'=>$cfg['domingo_apertura']??null, 'cierre'=>$cfg['domingo_cierre']??null],
          '1' => ['abierto'=>(int)($cfg['lunes_abierto']??0)===1, 'apertura'=>$cfg['lunes_apertura']??null, 'cierre'=>$cfg['lunes_cierre']??null],
          '2' => ['abierto'=>(int)($cfg['martes_abierto']??0)===1, 'apertura'=>$cfg['martes_apertura']??null, 'cierre'=>$cfg['martes_cierre']??null],
          '3' => ['abierto'=>(int)($cfg['miercoles_abierto']??0)===1, 'apertura'=>$cfg['miercoles_apertura']??null, 'cierre'=>$cfg['miercoles_cierre']??null],
          '4' => ['abierto'=>(int)($cfg['jueves_abierto']??0)===1, 'apertura'=>$cfg['jueves_apertura']??null, 'cierre'=>$cfg['jueves_cierre']??null],
          '5' => ['abierto'=>(int)($cfg['viernes_abierto']??0)===1, 'apertura'=>$cfg['viernes_apertura']??null, 'cierre'=>$cfg['viernes_cierre']??null],
          '6' => ['abierto'=>(int)($cfg['sabado_abierto']??0)===1, 'apertura'=>$cfg['sabado_apertura']??null, 'cierre'=>$cfg['sabado_cierre']??null],
        ];

        // Precálculo simple del tiempo vs ahora (solo informativo)
        $fechaSalida = new DateTime();
        $diff = $fechaEntrada->diff($fechaSalida);
        $vehiculo['tiempo_total'] = sprintf('%d h %02d min', ($diff->days*24 + $diff->h), $diff->i);
        $vehiculo['fecha_salida'] = $fechaSalida->format('Y-m-d H:i:s');

        echo json_encode(['success' => true, 'data' => $vehiculo]); return;
    }

    // POST(JSON/form)
    public function registrar() {
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        $entradaId     = isset($data['entrada_id']) ? (int)$data['entrada_id'] : 0;
        $cobro         = isset($data['cobro']) ? (float)$data['cobro'] : 0;
        $boletoPerdido = isset($data['boleto_perdido']) ? (float)$data['boleto_perdido'] : 0;
        $esPension     = isset($data['es_pension']) ? (int)$data['es_pension'] : 0;

        if ($entradaId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Entrada no especificada.']); return;
        }

        $fmt = function($s) {
            if (!$s) return null;
            $s = str_replace('T', ' ', $s);
            if (strlen($s) === 16) $s .= ':00';
            return $s;
        };
        $entradaOverride = isset($data['fecha_entrada_override']) ? $fmt($data['fecha_entrada_override']) : null;
        $salidaOverride  = isset($data['fecha_salida_override'])  ? $fmt($data['fecha_salida_override'])  : null;

        // Seguridad: si marcan pensión manual, validar entrada <= cierre
        if ($esPension === 1) {
            $vehiculo = $this->salidasModel->getVehiculoPorEntradaId($entradaId);
            if ($vehiculo) {
                $fechaEntrada = new DateTime($entradaOverride ?: $vehiculo['fecha_entrada']);
                $diaNum = (int)$fechaEntrada->format('w');
                $map = [
                  0 => ['abierto'=>'domingo_abierto','cierre'=>'domingo_cierre'],
                  1 => ['abierto'=>'lunes_abierto','cierre'=>'lunes_cierre'],
                  2 => ['abierto'=>'martes_abierto','cierre'=>'martes_cierre'],
                  3 => ['abierto'=>'miercoles_abierto','cierre'=>'miercoles_cierre'],
                  4 => ['abierto'=>'jueves_abierto','cierre'=>'jueves_cierre'],
                  5 => ['abierto'=>'viernes_abierto','cierre'=>'viernes_cierre'],
                  6 => ['abierto'=>'sabado_abierto','cierre'=>'sabado_cierre']
                ];
                $k = $map[$diaNum];
                $abierto = isset($this->configModel->obtenerConfiguracion()[$k['abierto']]) ? (int)$this->configModel->obtenerConfiguracion()[$k['abierto']] === 1 : false;
                if (!$abierto) { echo json_encode(['success'=>false,'message'=>'El negocio está cerrado ese día.']); return; }
                $horaCierre = $this->configModel->obtenerConfiguracion()[$k['cierre']] ?? null;
                if (!$horaCierre) { echo json_encode(['success'=>false,'message'=>'No se pudo determinar la hora de cierre.']); return; }
                $cierre = new DateTime($fechaEntrada->format('Y-m-d').' '.$horaCierre);
                if ($fechaEntrada > $cierre) { echo json_encode(['success'=>false,'message'=>'Entrada mayor al cierre para ese día.']); return; }
            }
        }

        $tipoCobro = $esPension ? 'pension' : 'normal';

        $result = $this->salidasModel->registrarSalida([
            'tipo_cobro'       => $tipoCobro,
            'fecha_salida'     => $salidaOverride ?: date('Y-m-d H:i:s'),
            'cobro'            => $cobro,
            'boleto_perdido'   => $boletoPerdido,
            'es_pension'       => $esPension,
            'entrada_id'       => $entradaId,
            'entrada_override' => $entradaOverride,
        ]);

        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Salida registrada con éxito.']); return;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $result]); return;
        }
    }
}
