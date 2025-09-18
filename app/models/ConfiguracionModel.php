<?php
// Archivo: app/models/ConfiguracionModel.php

require_once __DIR__ . '/../config/Database.php';

class ConfiguracionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerConfiguracion()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM configuracion WHERE id = 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function guardarConfiguracion($data)
    {
        try {
            $sql = "UPDATE configuracion SET
                        nombre_negocio = ?, direccion = ?,
                        lunes_abierto = ?, lunes_apertura = ?, lunes_cierre = ?,
                        martes_abierto = ?, martes_apertura = ?, martes_cierre = ?,
                        miercoles_abierto = ?, miercoles_apertura = ?, miercoles_cierre = ?,
                        jueves_abierto = ?, jueves_apertura = ?, jueves_cierre = ?,
                        viernes_abierto = ?, viernes_apertura = ?, viernes_cierre = ?,
                        sabado_abierto = ?, sabado_apertura = ?, sabado_cierre = ?,
                        domingo_abierto = ?, domingo_apertura = ?, domingo_cierre = ?
                    WHERE id = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['nombre_negocio'], $data['direccion'],
                $data['lunes_abierto'], $data['lunes_apertura'], $data['lunes_cierre'],
                $data['martes_abierto'], $data['martes_apertura'], $data['martes_cierre'],
                $data['miercoles_abierto'], $data['miercoles_apertura'], $data['miercoles_cierre'],
                $data['jueves_abierto'], $data['jueves_apertura'], $data['jueves_cierre'],
                $data['viernes_abierto'], $data['viernes_apertura'], $data['viernes_cierre'],
                $data['sabado_abierto'], $data['sabado_apertura'], $data['sabado_cierre'],
                $data['domingo_abierto'], $data['domingo_apertura'], $data['domingo_cierre']
            ]);

            return true;
        } catch (PDOException $e) {
            return "Error al guardar la configuración: " . $e->getMessage();
        }
    }
}