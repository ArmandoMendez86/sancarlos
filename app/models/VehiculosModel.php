<?php
// Archivo: app/models/VehiculosModel.php

require_once __DIR__ . '/../config/Database.php';

class VehiculosModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTiposVehiculos()
    {
        try {
            $stmt = $this->db->prepare("SELECT id, tipo FROM vehiculos");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}