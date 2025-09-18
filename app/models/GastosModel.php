<?php
// Archivo: app/models/GastosModel.php

require_once __DIR__ . '/../config/Database.php';

class GastosModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registra un nuevo gasto en la base de datos.
     * @param array $data Los datos del formulario (concepto, monto).
     * @return bool|string Retorna true si es exitoso, o un mensaje de error.
     */
    public function registrarGasto($data)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO gastos (concepto, monto, fecha_gasto) VALUES (?, ?, NOW())");
            $stmt->execute([
                $data['concepto'],
                $data['monto']
            ]);

            return true;
        } catch (PDOException $e) {
            return "Error al registrar el gasto: " . $e->getMessage();
        }
    }
}