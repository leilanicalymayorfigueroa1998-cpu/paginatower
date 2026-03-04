<?php

class PagoService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function crearPago($id_contrato, $fecha_pago, $monto, $metodo_pago, $estatus)
    {
        $this->conexion->beginTransaction();

        try {

            $periodo = date('Y-m-01', strtotime($fecha_pago));

            // Insertar pago
            $stmt = $this->conexion->prepare("
                INSERT INTO pagos
                (id_contrato, periodo, fecha_pago, monto, metodo_pago, estatus)
                VALUES (:id_contrato, :periodo, :fecha_pago, :monto, :metodo_pago, :estatus)
            ");

            $stmt->execute([
                ':id_contrato' => $id_contrato,
                ':periodo'     => $periodo,
                ':fecha_pago'  => $fecha_pago,
                ':monto'       => $monto,
                ':metodo_pago' => $metodo_pago,
                ':estatus'     => $estatus
            ]);

            $id_pago = $this->conexion->lastInsertId();

            // Obtener id_local del contrato
            $stmtContrato = $this->conexion->prepare("
                SELECT id_local FROM contratos WHERE id_contrato = :id
            ");

            $stmtContrato->execute([':id' => $id_contrato]);
            $contrato = $stmtContrato->fetch(PDO::FETCH_ASSOC);

            if (!$contrato) {
                throw new Exception("Contrato no encontrado.");
            }

            $id_local = $contrato['id_local'];

            // Si el pago está Pagado → crear movimiento financiero
            if ($estatus === "Pagado") {

                $nota = "Ingreso renta contrato #" . $id_contrato;

                $stmtMov = $this->conexion->prepare("
                    INSERT INTO movimientos_financieros
                    (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen, id_pago)
                    VALUES (:fecha, :id_propiedad, 1, :nota, :abono, 0, 'CUENTA', :id_pago)
                ");

                $stmtMov->execute([
                    ':fecha'        => $fecha_pago,
                    ':id_propiedad' => $id_local,
                    ':nota'         => $nota,
                    ':abono'        => $monto,
                    ':id_pago'      => $id_pago
                ]);
            }

            $this->conexion->commit();

        } catch (Exception $e) {

            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function generarPagosMensuales()
    {
        $stmt = $this->conexion->query("
            SELECT id_contrato, fecha_inicio, renta, duracion
            FROM contratos
            WHERE estatus = 'Activa'
        ");

        $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($contratos as $contrato) {

            $fechaInicio = new DateTime($contrato['fecha_inicio']);
            $meses = $contrato['duracion'];

            for ($i = 0; $i < $meses; $i++) {

                $fechaPago = clone $fechaInicio;
                $fechaPago->modify("+$i month");

                $periodo = $fechaPago->format('Y-m-01');

                // verificar si ya existe pago para ese mes
                $check = $this->conexion->prepare("
                    SELECT id_pago 
                    FROM pagos 
                    WHERE id_contrato = :contrato
                    AND periodo = :periodo
                ");

                $check->execute([
                    ':contrato' => $contrato['id_contrato'],
                    ':periodo'  => $periodo
                ]);

                if (!$check->fetch()) {

                    $insert = $this->conexion->prepare("
                        INSERT INTO pagos
                        (id_contrato, periodo, fecha_pago, monto, metodo_pago, estatus)
                        VALUES (:contrato, :periodo, :fecha, :monto, NULL, 'Pendiente')
                    ");

                    $insert->execute([
                        ':contrato' => $contrato['id_contrato'],
                        ':periodo'  => $periodo,
                        ':fecha'    => $fechaPago->format('Y-m-d'),
                        ':monto'    => $contrato['renta']
                    ]);
                }
            }
        }
    }
}