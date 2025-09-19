<?php
// Archivo: app/controllers/EntradasController.php
require_once __DIR__ . '/../models/EntradasModel.php';

class EntradasController {
  private $entradasModel;

  public function __construct() {
    $this->entradasModel = new EntradasModel();
  }

  // GET: entradas/obtenerActivas
  public function obtenerActivas() {
    $data = $this->entradasModel->obtenerEntradasActivas();
    echo json_encode(['success' => true, 'data' => $data]);
  }

  // POST(JSON): salidas/obtenerDetalles  { id }
  public function obtenerDetallesSalida() {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : 0;
    if (!$id) {
      echo json_encode(['success' => false, 'message' => 'ID inválido']);
      return;
    }
    $det = $this->entradasModel->obtenerDetallesParaSalida($id);
    if (!$det) {
      echo json_encode(['success' => false, 'message' => 'No encontrado']);
      return;
    }
    echo json_encode(['success' => true, 'data' => $det]);
  }

  // POST(JSON): salidas/registrar
  public function registrarSalida() {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
      echo json_encode(['success' => false, 'message' => 'Entrada inválida']);
      return;
    }
    $entradaId     = (int)($input['entrada_id'] ?? 0);
    $cobro         = (float)($input['cobro'] ?? 0);
    $boletoPerdido = (float)($input['boleto_perdido'] ?? 0);
    $esPension     = (int)($input['es_pension'] ?? 0);
    $entradaOverride = $input['fecha_entrada_override'] ?? null;
    $salidaOverride  = $input['fecha_salida_override'] ?? null;

    // Normaliza "YYYY-MM-DDTHH:MM" -> "YYYY-MM-DD HH:MM:SS"
    $fmt = function($s) {
      if (!$s) return null;
      $s = str_replace('T', ' ', $s);
      if (strlen($s) === 16) $s .= ':00';
      return $s;
    };
    $entradaOverride = $fmt($entradaOverride);
    $salidaOverride  = $fmt($salidaOverride);

    $ok = $this->entradasModel->registrarSalida($entradaId, $cobro, $boletoPerdido, $esPension, $entradaOverride, $salidaOverride);
    if ($ok === true) {
      echo json_encode(['success' => true, 'message' => 'Salida registrada.']);
    } else {
      echo json_encode(['success' => false, 'message' => $ok ?: 'Error al registrar salida']);
    }
  }

  // POST: entradas/registrar (no esencial para este flujo)
  public function registrarEntrada() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Método inválido']);
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
