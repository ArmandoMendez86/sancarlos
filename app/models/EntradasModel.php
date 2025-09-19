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
        $sql = "SELECT 
                    e.id,
                    e.fecha_entrada,
                    e.marca,
                    e.color,
                    e.placa,
                    e.folio,
                    e.vehiculos_id,
                    v.tipo AS tipo,
                    v.tarifa
                FROM entradas e
                INNER JOIN vehiculos v ON v.id = e.vehiculos_id
                LEFT JOIN salidas s ON s.entrada_id = e.id
                WHERE s.id IS NULL
                ORDER BY e.fecha_entrada DESC";
        $st = $this->db->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }



    private function horarioParaFecha(DateTime $fecha)
    {
        // Lee única fila de configuracion
        $row = $this->db->query("SELECT * FROM configuracion LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if (!$row) return ['apertura' => null, 'cierre' => 'Cerrado'];

        // 0 dom, 1 lun, ... 6 sab (PHP: 0=domingo)
        $dow = (int)$fecha->format('w');
        $map = [
            0 => ['abierto' => 'domingo_abierto', 'apertura' => 'domingo_apertura', 'cierre' => 'domingo_cierre'],
            1 => ['abierto' => 'lunes_abierto', 'apertura' => 'lunes_apertura', 'cierre' => 'lunes_cierre'],
            2 => ['abierto' => 'martes_abierto', 'apertura' => 'martes_apertura', 'cierre' => 'martes_cierre'],
            3 => ['abierto' => 'miercoles_abierto', 'apertura' => 'miercoles_apertura', 'cierre' => 'miercoles_cierre'],
            4 => ['abierto' => 'jueves_abierto', 'apertura' => 'jueves_apertura', 'cierre' => 'jueves_cierre'],
            5 => ['abierto' => 'viernes_abierto', 'apertura' => 'viernes_apertura', 'cierre' => 'viernes_cierre'],
            6 => ['abierto' => 'sabado_abierto', 'apertura' => 'sabado_apertura', 'cierre' => 'sabado_cierre'],
        ];
        $k = $map[$dow];
        if ((int)$row[$k['abierto']] !== 1) {
            return ['apertura' => null, 'cierre' => 'Cerrado'];
        }
        return ['apertura' => $row[$k['apertura']], 'cierre' => $row[$k['cierre']]];
    }

    public function obtenerDetallesParaSalida($id)
    {
        $st = $this->db->prepare("SELECT e.*, v.nombre AS tipo FROM entradas e JOIN vehiculos v ON v.id=e.vehiculos_id WHERE e.id=? LIMIT 1");
        $st->execute([$id]);
        $e = $st->fetch(PDO::FETCH_ASSOC);
        if (!$e) return null;

        $fechaEntrada = new DateTime($e['fecha_entrada']);
        $fechaSalida  = !empty($e['fecha_salida']) ? new DateTime($e['fecha_salida']) : new DateTime();

        $diff = $fechaEntrada->diff($fechaSalida);
        $tiempoTotal = sprintf('%d h %02d min', ($diff->days * 24 + $diff->h), $diff->i);

        $hor = $this->horarioParaFecha($fechaEntrada);

        return [
            'id' => (int)$e['id'],
            'placa' => $e['placa'],
            'marca' => $e['marca'],
            'color' => $e['color'],
            'tipo'  => $e['tipo'],
            'fecha_entrada' => $e['fecha_entrada'],
            'fecha_salida'  => $e['fecha_salida'],
            'tiempo_total'  => $tiempoTotal,
            'hora_apertura' => $hor['apertura'],
            'hora_cierre'   => $hor['cierre'],
        ];
    }

    public function registrarSalida($entradaId, $cobro, $boletoPerdido, $esPension, $entradaOverride = null, $salidaOverride = null)
    {
        try {
            $this->db->beginTransaction();

            // Aplica overrides de fechas si vienen
            if ($entradaOverride !== null || $salidaOverride !== null) {
                $st = $this->db->prepare("UPDATE entradas SET
            fecha_entrada = COALESCE(?, fecha_entrada),
            fecha_salida  = COALESCE(?, NOW())
          WHERE id = ?");
                $st->execute([$entradaOverride, $salidaOverride, $entradaId]);
            } else {
                // si no hay fecha_salida, la marcamos ahora
                $st = $this->db->prepare("UPDATE entradas SET fecha_salida = NOW() WHERE id = ? AND (fecha_salida IS NULL OR fecha_salida = '0000-00-00 00:00:00')");
                $st->execute([$entradaId]);
            }

            // Registra venta simple (ajusta a tu modelo real)
            $st2 = $this->db->prepare("INSERT INTO ventas (concepto, monto, fecha_venta) VALUES (?, ?, NOW())");
            $concepto = $esPension ? 'Pensión nocturna' : 'Estacionamiento';
            $st2->execute([$concepto, $cobro]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Error al registrar salida: ' . $e->getMessage();
        }
    }

    // Ejemplo de registro de entrada
    public function registrarEntrada($data)
    {
        try {
            $folio = $this->generarFolio();
            $st = $this->db->prepare("INSERT INTO entradas (fecha_entrada, marca, color, placa, folio, vehiculos_id) VALUES (NOW(), ?, ?, ?, ?, ?)");
            $st->execute([$data['marca'] ?? null, $data['color'] ?? null, $data['placa'], $folio, $data['vehiculos_id']]);
            return true;
        } catch (PDOException $e) {
            return 'Error al registrar la entrada: ' . $e->getMessage();
        }
    }

    private function generarFolio()
    {
        // Folio simple SEP-0001, ajusta a tu preferencia
        $n = (int)$this->db->query("SELECT COALESCE(MAX(id),0)+1 FROM entradas")->fetchColumn();
        return sprintf('SEP-%04d', $n);
    }
}
