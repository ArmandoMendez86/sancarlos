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


    public function registrarEntrada($data)
    {
        try {
            $folio = $this->generarFolio();
            $st = $this->db->prepare("INSERT INTO entradas (fecha_entrada, marca, color, placa, folio) VALUES (NOW(), ?, ?, ?, ?)");
            $st->execute([$data['marca'] ?? null, $data['color'] ?? null, $data['placa'], $folio]);
            return true;
        } catch (PDOException $e) {
            return 'Error al registrar la entrada: ' . $e->getMessage();
        }
    }


    private function generarFolio()
    {
        // Obtiene el mes actual en letras (ej: SEP, OCT)
        $mes = strtoupper(date('M'));
        $anio = date('Y');

        // Busca el último folio del mes y año actual
        $st = $this->db->prepare("SELECT folio FROM entradas WHERE DATE_FORMAT(fecha_entrada, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') ORDER BY id DESC LIMIT 1");
        $st->execute();
        $ultimoFolio = $st->fetchColumn();

        if ($ultimoFolio) {
            // Extrae el número del folio anterior y suma 1
            preg_match('/\d+$/', $ultimoFolio, $matches);
            $n = isset($matches[0]) ? ((int)$matches[0]) + 1 : 1;
        } else {
            $n = 1;
        }

        return sprintf('%s-%04d', $mes, $n);
    }



    public function obtenerEntradaIdPorFolio(string $folio): ?array
    {
        $st = $this->db->prepare("SELECT * FROM entradas WHERE folio = ? ORDER BY id DESC LIMIT 1");
        $st->execute([$folio]);
        $registro = $st->fetch(PDO::FETCH_ASSOC);
        return $registro ?: null;
    }
}
