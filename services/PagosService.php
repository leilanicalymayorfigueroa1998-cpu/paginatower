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

            // Insertar pago
            $consulta = $this->conexion->prepare("
                INSERT INTO pagos 
                (id_contrato, fecha_pago, monto, metodo_pago, estatus) 
                VALUES (:id_contrato, :fecha_pago, :monto, :metodo_pago, :estatus)
            ");

            $consulta->execute([
                ':id_contrato' => $id_contrato,
                ':fecha_pago'  => $fecha_pago,
                ':monto'       => $monto,
                ':metodo_pago' => $metodo_pago,
                ':estatus'     => $estatus
            ]);

            // Si está pagado → movimiento financiero
            if ($estatus === "Pagado") {

                $mov = $this->conexion->prepare("
                    INSERT INTO movimientos_financieros
                    (fecha, id_tipo_operacion, abono, cargo, origen)
                    VALUES (:fecha, 1, :abono, 0, 'CUENTA')
                ");

                $mov->execute([
                    ':fecha' => $fecha_pago,
                    ':abono' => $monto
                ]);
            }

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function actualizarPago($id_pago, $id_contrato, $fecha_pago, $monto, $metodo_pago, $estatus)
    {
        $this->conexion->beginTransaction();

        try {

            // 1️⃣ Obtener estado anterior
            $stmtAnterior = $this->conexion->prepare("
            SELECT estatus FROM pagos WHERE id_pago = :id
        ");
            $stmtAnterior->execute([':id' => $id_pago]);
            $anterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

            $estatusAnterior = $anterior['estatus'] ?? '';

            // 2️⃣ Actualizar pago
            $update = $this->conexion->prepare("
            UPDATE pagos SET
                id_contrato = :id_contrato,
                fecha_pago = :fecha_pago,
                monto = :monto,
                metodo_pago = :metodo_pago,
                estatus = :estatus
            WHERE id_pago = :id_pago
        ");

            $update->execute([
                ':id_contrato' => $id_contrato,
                ':fecha_pago'  => $fecha_pago,
                ':monto'       => $monto,
                ':metodo_pago' => $metodo_pago,
                ':estatus'     => $estatus,
                ':id_pago'     => $id_pago
            ]);

            // 🔄 3️⃣ Lógica financiera

            // Caso 1: Se vuelve Pagado
            if ($estatus === "Pagado" && $estatusAnterior !== "Pagado") {

                $mov = $this->conexion->prepare("
                INSERT INTO movimientos_financieros
                (fecha, id_tipo_operacion, abono, cargo, origen, id_pago)
                VALUES (:fecha, 1, :abono, 0, 'CUENTA', :id_pago)
            ");

                $mov->execute([
                    ':fecha' => $fecha_pago,
                    ':abono' => $monto,
                    ':id_pago' => $id_pago
                ]);
            }

            // Caso 2: Ya estaba pagado y cambia el monto
            if ($estatus === "Pagado" && $estatusAnterior === "Pagado") {

                $movUpdate = $this->conexion->prepare("
                UPDATE movimientos_financieros
                SET abono = :abono, fecha = :fecha
                WHERE id_pago = :id_pago
            ");

                $movUpdate->execute([
                    ':abono' => $monto,
                    ':fecha' => $fecha_pago,
                    ':id_pago' => $id_pago
                ]);
            }

            // Caso 3: Deja de estar pagado
            if ($estatus !== "Pagado" && $estatusAnterior === "Pagado") {

                $movDelete = $this->conexion->prepare("
                DELETE FROM movimientos_financieros
                WHERE id_pago = :id_pago
            ");

                $movDelete->execute([
                    ':id_pago' => $id_pago
                ]);
            }

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function eliminarPago($id_pago)
    {
        $this->conexion->beginTransaction();

        try {

            // 🔎 Verificar si estaba pagado
            $stmt = $this->conexion->prepare("
            SELECT estatus FROM pagos WHERE id_pago = :id
        ");
            $stmt->execute([':id' => $id_pago]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pago) {
                throw new Exception("Pago no encontrado.");
            }

            // Si estaba pagado → eliminar movimiento financiero
            if ($pago['estatus'] === 'Pagado') {

                $deleteMov = $this->conexion->prepare("
                DELETE FROM movimientos_financieros
                WHERE id_pago = :id_pago
            ");
                $deleteMov->execute([':id_pago' => $id_pago]);
            }

            // Eliminar pago
            $deletePago = $this->conexion->prepare("
            DELETE FROM pagos WHERE id_pago = :id
        ");
            $deletePago->execute([':id' => $id_pago]);

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function marcarComoPagado($id_pago)
    {
        if (!is_numeric($id_pago)) {
            throw new Exception("ID inválido");
        }

        // 🔎 Obtener datos actuales del pago
        $stmt = $this->conexion->prepare("
        SELECT * FROM pagos WHERE id_pago = :id
    ");
        $stmt->execute([':id' => $id_pago]);
        $pago = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pago) {
            throw new Exception("Pago no encontrado");
        }

        // 🔄 Usar actualizarPago para que se ejecute toda la lógica financiera
        $this->actualizarPago(
            $id_pago,
            $pago['id_contrato'],
            date('Y-m-d'), // fecha actual
            $pago['monto'],
            $pago['metodo_pago'],
            'Pagado'
        );
    }
}
