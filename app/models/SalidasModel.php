<?php
// app/models/SalidasModel.php
require_once __DIR__ . '/../config/Database.php';

class SalidasModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function registrarSalida(array $data)
    {
        $sql = "INSERT INTO salidas (fecha_salida, monto,  entrada_id, pension)
                VALUES (:fecha_salida, :monto, :entrada_id, :pension)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha_salida', $data['fecha_salida']);
        $stmt->bindValue(':monto',        $data['monto']);
        $stmt->bindValue(':entrada_id',   $data['entrada_id']);
        $stmt->bindValue(':pension',      (int)$data['pension'], PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
}
