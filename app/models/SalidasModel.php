<?php
// Archivo: app/models/SalidasModel.php

require_once __DIR__ . '/../config/Database.php';

class SalidasModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene los detalles de un vehículo por su placa.
     * @param string $placa La placa del vehículo.
     * @return array|null
     */
    public function obtenerDetallesPorPlaca($placa)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, v.tipo, v.tarifa 
                FROM entradas e 
                JOIN vehiculos v ON e.vehiculos_id = v.id 
                WHERE e.placa = ? ORDER BY e.fecha_entrada DESC LIMIT 1");
            $stmt->execute([$placa]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Registra una nueva salida de vehículo.
     * @param array $data
     * @return bool|string
     */
    public function registrarSalida($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO salidas (tipo_cobro, fecha_salida, cobro, boleto_perdido, es_pension, entrada_id)
                VALUES (?, NOW(), ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['tipo_cobro'],
                $data['cobro'],
                $data['boleto_perdido'],
                $data['es_pension'],
                $data['entrada_id']
            ]);
            return true;
        } catch (PDOException $e) {
            return "Error al registrar la salida: " . $e->getMessage();
        }
    }
}