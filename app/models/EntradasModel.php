<?php
// Archivo: app/models/EntradasModel.php

require_once __DIR__ . '/../config/Database.php';

class EntradasModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerEntradasActivas()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT e.id, e.placa, e.marca, e.color, e.fecha_entrada, v.tipo
                FROM entradas e
                LEFT JOIN salidas s ON e.id = s.entrada_id
                JOIN vehiculos v ON e.vehiculos_id = v.id
                WHERE s.id IS NULL
                ORDER BY e.fecha_entrada DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function registrarEntrada($data)
    {
        try {
            $monthPrefix = strtoupper(date('M'));
            $stmt = $this->db->prepare("SELECT folio FROM entradas WHERE folio LIKE ? ORDER BY folio DESC LIMIT 1");
            $stmt->execute(["$monthPrefix-%"]);
            $lastFolio = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextNumber = 1;
            if ($lastFolio) {
                $lastNumber = intval(substr($lastFolio['folio'], 4));
                $nextNumber = $lastNumber + 1;
            }
            $folio = $monthPrefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $stmt = $this->db->prepare("INSERT INTO entradas (fecha_entrada, marca, color, placa, folio, vehiculos_id) VALUES (NOW(), ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['marca'],
                $data['color'],
                $data['placa'],
                $folio,
                $data['vehiculos_id']
            ]);
            return true;
        } catch (PDOException $e) {
            return "Error al registrar la entrada: " . $e->getMessage();
        }
    }
}