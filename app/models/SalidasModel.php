<?php
// app/models/SalidasModel.php
require_once __DIR__ . '/../config/Database.php';

class SalidasModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getVehiculoPorEntradaId($entradaId) {
        $sql = "SELECT 
                    e.id,
                    e.fecha_entrada,
                    e.marca,
                    e.color,
                    e.placa,
                    e.folio,
                    e.vehiculos_id,
                    v.tipo,
                    v.tarifa
                FROM entradas e
                INNER JOIN vehiculos v ON v.id = e.vehiculos_id
                WHERE e.id = ?
                LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$entradaId]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarSalida(array $data) {
        try {
            $this->db->beginTransaction();

            if (!empty($data['entrada_override'])) {
                $st0 = $this->db->prepare("UPDATE entradas SET fecha_entrada = ? WHERE id = ?");
                $st0->execute([$data['entrada_override'], $data['entrada_id']]);
            }

            // Evitar duplicado
            $chk = $this->db->prepare("SELECT id FROM salidas WHERE entrada_id = ? LIMIT 1");
            $chk->execute([$data['entrada_id']]);
            if ($chk->fetchColumn()) {
                $this->db->rollBack();
                return "La entrada ya tiene una salida registrada.";
            }

            $st = $this->db->prepare("
                INSERT INTO salidas (tipo_cobro, fecha_salida, cobro, boleto_perdido, es_pension, horas_extras, cobro_extra, entrada_id)
                VALUES (?, ?, ?, ?, ?, NULL, NULL, ?)
            ");
            $st->execute([
                $data['tipo_cobro'],
                $data['fecha_salida'],
                $data['cobro'],
                $data['boleto_perdido'],
                $data['es_pension'],
                $data['entrada_id']
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Error al registrar la salida: ' . $e->getMessage();
        }
    }
}
