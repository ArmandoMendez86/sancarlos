<?php
// Archivo: app/models/VentasModel.php

require_once __DIR__ . '/../config/Database.php';

class VentasModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registra una venta de baños en la base de datos.
     * @return bool|string Retorna true si es exitoso, o un mensaje de error.
     */
    public function registrarVentaBaños()
    {
        try {
            $concepto = "Venta de Baños";
            $monto = 5.00; // Puedes cambiar este valor por la tarifa de tu negocio

            $stmt = $this->db->prepare("INSERT INTO ventas (concepto, monto, fecha_venta) VALUES (?, ?, NOW())");
            $stmt->execute([$concepto, $monto]);

            return true;
        } catch (PDOException $e) {
            return "Error al registrar la venta: " . $e->getMessage();
        }
    }
}