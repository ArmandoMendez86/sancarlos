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


  public function registrar()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Método inválido']);
      return;
    }
    if (empty($_POST['placa'])) {
      echo json_encode(['success' => false, 'message' => 'La Placa es obligatoria.']);
      return;
    }
    $result = $this->entradasModel->registrarEntrada($_POST);
    if ($result === true) {
      echo json_encode(['success' => true, 'message' => 'Entrada registrada con éxito.']);
    } else {
      echo json_encode(['success' => false, 'message' => $result]);
    }
  }

  // === NUEVO: API para buscar por folio (lectura con escáner) === PENDIENTE NO SE USA AUN
  public function buscarPorFolio()
  {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
      $input = $_POST ?? [];
    }

    $folio = isset($input['folio']) ? trim($input['folio']) : '';
    if ($folio === '') {
      echo json_encode(['success' => false, 'message' => 'Folio requerido']);
      return;
    }

    $id = $this->entradasModel->obtenerEntradaIdPorFolio($folio);
    if (!$id) {
      echo json_encode(['success' => false, 'message' => 'Folio no encontrado']);
      return;
    }

    echo json_encode(['success' => true, 'data' => $id]);
  }
}
