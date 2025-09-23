<?php
// app/controllers/SalidasController.php
require_once __DIR__ . '/../models/SalidasModel.php';


class SalidasController
{
    private $salidasModel;

    public function __construct()
    {
        $this->salidasModel = new SalidasModel();
    }

    public function registrar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $entrada_id      = $_POST['entrada_id']      ?? '';
        $monto           = $_POST['monto']           ?? '';
        $pension         = isset($_POST['pension']) ? (int)$_POST['pension'] : 0;
        $fecha_salida_in = $_POST['fecha_salida_txt'] ?? ''; // viene como texto visible

        if ($entrada_id === '' || $monto === '') {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
            return;
        }

        // Normalizar fecha: si viene en blanco, usa ahora; si viene en texto local, intenta parsear
        $fecha_salida = date('Y-m-d H:i:s'); // default now
        if ($fecha_salida_in !== '') {
            $ts = strtotime($fecha_salida_in);
            if ($ts !== false) {
                $fecha_salida = date('Y-m-d H:i:s', $ts);
            }
        }

        try {
            $id = $this->salidasModel->registrarSalida([
                'fecha_salida' => $fecha_salida,
                'monto'        => (float)$monto,
                'entrada_id'   => $entrada_id,
                'pension'      => $pension
            ]);

            echo json_encode(['success' => true, 'id' => $id]);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar la salida.']);
        }
    }
}
