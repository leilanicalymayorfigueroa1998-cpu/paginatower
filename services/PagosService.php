<?php

class PagoService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /* =========================================
       CREAR PAGO
    ========================================= */
    public function crearPago($id_contrato, $fecha_pago, $monto, $metodo_pago, $estatus)
    {

        if (empty($fecha_pago)) {
            throw new Exception("Fecha de pago inválida.");
        }

        $periodo = date('Y-m-01', strtotime($fecha_pago));

        /* evitar pagos duplicados */
        $check = $this->conexion->prepare("
        SELECT id_pago
        FROM pagos
        WHERE id_contrato = :contrato
        AND periodo = :periodo
        ");

        $check->execute([
            ':contrato' => $id_contrato,
            ':periodo' => $periodo
        ]);

        if ($check->fetch()) {
            throw new Exception("Ya existe un pago registrado para este mes.");
        }

        $this->conexion->beginTransaction();

        try {

            $stmt = $this->conexion->prepare("
            INSERT INTO pagos
            (id_contrato, periodo, fecha_pago, monto, metodo_pago, estatus)
            VALUES (:contrato, :periodo, :fecha, :monto, :metodo, :estatus)
            ");

            $stmt->execute([
                ':contrato' => $id_contrato,
                ':periodo' => $periodo,
                ':fecha' => $fecha_pago,
                ':monto' => $monto,
                ':metodo' => $metodo_pago,
                ':estatus' => $estatus
            ]);

            $id_pago = $this->conexion->lastInsertId();

            /* obtener local */
            $stmtContrato = $this->conexion->prepare("
            SELECT id_local
            FROM contratos
            WHERE id_contrato = :id
            ");

            $stmtContrato->execute([':id' => $id_contrato]);

            $id_local = $stmtContrato->fetchColumn();

            if (!$id_local) {
                throw new Exception("Contrato no encontrado.");
            }

            /* crear movimiento financiero */
            if ($estatus === "Pagado") {

                $nota = "Ingreso renta contrato #" . $id_contrato;

                $stmtMov = $this->conexion->prepare("
                INSERT INTO movimientos_financieros
                (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen, id_pago)
                VALUES (:fecha, :propiedad, 1, :nota, :abono, 0, 'CUENTA', :id_pago)
                ");

                $stmtMov->execute([
                    ':fecha' => $fecha_pago,
                    ':propiedad' => $id_local,
                    ':nota' => $nota,
                    ':abono' => $monto,
                    ':id_pago' => $id_pago
                ]);
            }

            $this->conexion->commit();

        } catch (Exception $e) {

            $this->conexion->rollBack();
            throw $e;
        }
    }


    /* =========================================
       ACTUALIZAR PAGO
    ========================================= */
    public function actualizarPago($id_pago, $id_contrato, $fecha_pago, $monto, $metodo_pago, $estatus)
    {

        if (empty($fecha_pago)) {
            throw new Exception("Fecha inválida.");
        }

        $periodo = date('Y-m-01', strtotime($fecha_pago));

        /* evitar duplicados */
        $check = $this->conexion->prepare("
        SELECT id_pago
        FROM pagos
        WHERE id_contrato = :contrato
        AND periodo = :periodo
        AND id_pago != :id
        ");

        $check->execute([
            ':contrato' => $id_contrato,
            ':periodo' => $periodo,
            ':id' => $id_pago
        ]);

        if ($check->fetch()) {
            throw new Exception("Ya existe un pago para este mes.");
        }

        $this->conexion->beginTransaction();

        try {

            $stmt = $this->conexion->prepare("
            UPDATE pagos
            SET
            id_contrato = :contrato,
            periodo = :periodo,
            fecha_pago = :fecha,
            monto = :monto,
            metodo_pago = :metodo,
            estatus = :estatus
            WHERE id_pago = :id
            ");

            $stmt->execute([
                ':contrato' => $id_contrato,
                ':periodo' => $periodo,
                ':fecha' => $fecha_pago,
                ':monto' => $monto,
                ':metodo' => $metodo_pago,
                ':estatus' => $estatus,
                ':id' => $id_pago
            ]);

            /* obtener local */
            $stmtContrato = $this->conexion->prepare("
            SELECT id_local
            FROM contratos
            WHERE id_contrato = :id
            ");

            $stmtContrato->execute([':id' => $id_contrato]);

            $id_local = $stmtContrato->fetchColumn();

            /* verificar movimiento */
            $checkMov = $this->conexion->prepare("
            SELECT id_movimiento
            FROM movimientos_financieros
            WHERE id_pago = :id
            ");

            $checkMov->execute([':id' => $id_pago]);

            $movimiento = $checkMov->fetch();

            if ($estatus === "Pagado") {

                if (!$movimiento) {

                    $nota = "Ingreso renta contrato #" . $id_contrato;

                    $insertMov = $this->conexion->prepare("
                    INSERT INTO movimientos_financieros
                    (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen, id_pago)
                    VALUES (:fecha, :propiedad, 1, :nota, :abono, 0, 'CUENTA', :id_pago)
                    ");

                    $insertMov->execute([
                        ':fecha' => $fecha_pago,
                        ':propiedad' => $id_local,
                        ':nota' => $nota,
                        ':abono' => $monto,
                        ':id_pago' => $id_pago
                    ]);

                } else {

                    /* actualizar monto si cambió */

                    $updateMov = $this->conexion->prepare("
                    UPDATE movimientos_financieros
                    SET fecha = :fecha,
                        abono = :abono
                    WHERE id_pago = :id
                    ");

                    $updateMov->execute([
                        ':fecha' => $fecha_pago,
                        ':abono' => $monto,
                        ':id' => $id_pago
                    ]);
                }

            } else {

                if ($movimiento) {

                    $deleteMov = $this->conexion->prepare("
                    DELETE FROM movimientos_financieros
                    WHERE id_pago = :id
                    ");

                    $deleteMov->execute([':id' => $id_pago]);
                }
            }

            $this->conexion->commit();

        } catch (Exception $e) {

            $this->conexion->rollBack();
            throw $e;
        }
    }


    /* =========================================
       ELIMINAR PAGO
    ========================================= */
    public function eliminarPago($id_pago)
    {

        $this->conexion->beginTransaction();

        try {

            $this->conexion->prepare("
            DELETE FROM movimientos_financieros
            WHERE id_pago = :id
            ")->execute([':id' => $id_pago]);

            $this->conexion->prepare("
            DELETE FROM pagos
            WHERE id_pago = :id
            ")->execute([':id' => $id_pago]);

            $this->conexion->commit();

        } catch (Exception $e) {

            $this->conexion->rollBack();
            throw $e;
        }
    }


    /* =========================================
       GENERAR PAGOS MENSUALES
    ========================================= */
    public function generarPagosMensuales($idContrato)
    {

        $stmt = $this->conexion->prepare("
        SELECT fecha_inicio, renta, duracion
        FROM contratos
        WHERE id_contrato = :id
        AND estatus = 'Activa'
        ");

        $stmt->execute([':id' => $idContrato]);

        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contrato) {
            return;
        }

        $fechaInicio = new DateTime($contrato['fecha_inicio']);

        $meses = min((int)$contrato['duracion'], 120);

        for ($i = 0; $i < $meses; $i++) {

            $fechaPago = clone $fechaInicio;
            $fechaPago->modify("+$i month");

            $periodo = $fechaPago->format('Y-m-01');

            $insert = $this->conexion->prepare("
            INSERT IGNORE INTO pagos
            (id_contrato, periodo, fecha_pago, monto, metodo_pago, estatus)
            VALUES (:contrato, :periodo, :fecha, :monto, NULL, 'Pendiente')
            ");

            $insert->execute([
                ':contrato' => $idContrato,
                ':periodo' => $periodo,
                ':fecha' => $fechaPago->format('Y-m-d'),
                ':monto' => $contrato['renta']
            ]);
        }
    }
}