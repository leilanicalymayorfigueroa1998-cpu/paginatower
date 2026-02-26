<?php

class ContratoService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /* =====================================================
       CREAR CONTRATO
    ===================================================== */
    public function crearContrato($data)
{
    $this->conexion->beginTransaction();

    try {

        // 1️⃣ Insertar contrato
        $stmtContrato = $this->conexion->prepare("
            INSERT INTO contratos 
            (id_arrendatario, id_propiedad, fecha_inicio, duracion_meses, monto_renta)
            VALUES (:id_arrendatario, :id_propiedad, :fecha_inicio, :duracion_meses, :monto_renta)
        ");

        $stmtContrato->execute([
            ':id_arrendatario' => $data['id_arrendatario'],
            ':id_propiedad'    => $data['id_propiedad'],
            ':fecha_inicio'    => $data['fecha_inicio'],
            ':duracion_meses'  => $data['duracion_meses'], // 6 o 12
            ':monto_renta'     => $data['monto_renta']
        ]);

        $idContrato = $this->conexion->lastInsertId();

        // 2️⃣ Generar pagos automáticamente
        $fechaInicio = new DateTime($data['fecha_inicio']);

        for ($i = 0; $i < $data['duracion_meses']; $i++) {

            $fechaPago = clone $fechaInicio;
            $fechaPago->modify("+$i month");

            $stmtPago = $this->conexion->prepare("
                INSERT INTO pagos 
                (id_contrato, fecha_programada, monto, estatus)
                VALUES (:id_contrato, :fecha_programada, :monto, 'Pendiente')
            ");

            $stmtPago->execute([
                ':id_contrato'    => $idContrato,
                ':fecha_programada' => $fechaPago->format('Y-m-d'),
                ':monto'          => $data['monto_renta']
            ]);
        }

        $this->conexion->commit();
        return true;

    } catch (Exception $e) {
        $this->conexion->rollBack();
        throw $e;
    }
}

    /* =====================================================
       ACTUALIZAR CONTRATO
    ===================================================== */
    public function actualizarContrato($id_contrato, $data)
    {
        $this->conexion->beginTransaction();

        try {

            $this->validarFechas($data);

            $actual = $this->conexion->prepare("
            SELECT id_local, estatus, fecha_fin
            FROM contratos
            WHERE id_contrato = :id
        ");
            $actual->execute([':id' => $id_contrato]);
            $contratoActual = $actual->fetch(PDO::FETCH_ASSOC);

            if (!$contratoActual) {
                throw new Exception("Contrato no encontrado.");
            }

            $localAnterior = $contratoActual['id_local'];
            $estatusAnterior = $contratoActual['estatus'];
            $fechaFinAnterior = $contratoActual['fecha_fin'];

            // 🔄 ACTUALIZAR CONTRATO
            $consulta = $this->conexion->prepare("
            UPDATE contratos SET
                id_local = :id_local,
                id_arrendatario = :id_arrendatario,
                renta = :renta,
                deposito = :deposito,
                adicional = :adicional,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                estatus = :estatus,
                duracion = :duracion
            WHERE id_contrato = :id_contrato
        ");

            $consulta->execute([
                ':id_local' => $data['id_local'],
                ':id_arrendatario' => $data['id_arrendatario'],
                ':renta' => $data['renta'],
                ':deposito' => $data['deposito'],
                ':adicional' => $data['adicional'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':estatus' => $data['estatus'],
                ':duracion' => $data['duracion'],
                ':id_contrato' => $id_contrato
            ]);

            /* ==============================
           CAMBIO DE LOCAL
        ============================== */
            if ($localAnterior != $data['id_local']) {

                $this->liberarLocal($localAnterior);

                if ($data['estatus'] === 'Activa') {
                    $this->ocuparLocal($data['id_local']);
                }
            }

            /* ==============================
           CAMBIO DE ESTATUS
        ============================== */

            if ($estatusAnterior !== 'Activa' && $data['estatus'] === 'Activa') {
                $this->ocuparLocal($data['id_local']);
            }

            if (in_array($data['estatus'], ['Cancelada', 'Finalizada'])) {

                $this->liberarLocal($data['id_local']);

                $this->conexion->prepare("
                UPDATE pagos
                SET estatus = 'Cancelado'
                WHERE id_contrato = :id
                AND estatus = 'Pendiente'
            ")->execute([':id' => $id_contrato]);
            }

            /* ==============================
           SI CAMBIÓ LA FECHA FIN
           REGENERAR PAGOS
        ============================== */

            if ($fechaFinAnterior != $data['fecha_fin']) {

                // Eliminar pagos pendientes anteriores
                $this->conexion->prepare("
                DELETE FROM pagos
                WHERE id_contrato = :id
                AND estatus = 'Pendiente'
            ")->execute([':id' => $id_contrato]);

                // Solo generar si tiene fecha_fin (plazo fijo)
                if (!empty($data['fecha_fin']) && $data['estatus'] === 'Activa') {
                    $this->generarPagos($id_contrato, $data);
                }
            }

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
    /* =====================================================
       VALIDAR FECHAS
    ===================================================== */
    private function validarFechas($data)
    {
        if (empty($data['fecha_inicio'])) {
            throw new Exception("Debe seleccionar fecha inicio.");
        }

        // 👇 Solo validar fecha_fin si existe
        if (!empty($data['fecha_fin'])) {

            if (strtotime($data['fecha_fin']) < strtotime($data['fecha_inicio'])) {
                throw new Exception("La fecha fin no puede ser menor que la fecha inicio.");
            }
        }
    }

    /* =====================================================
       OCUPAR LOCAL
    ===================================================== */
    private function ocuparLocal($id_local)
    {
        $this->conexion->prepare("
            UPDATE locales
            SET estatus = 'Ocupado'
            WHERE id_local = :id
        ")->execute([':id' => $id_local]);
    }

    /* =====================================================
       LIBERAR LOCAL
    ===================================================== */
    private function liberarLocal($id_local)
    {
        $this->conexion->prepare("
            UPDATE locales
            SET estatus = 'Disponible'
            WHERE id_local = :id
        ")->execute([':id' => $id_local]);
    }

    /* =====================================================
       GENERAR PAGOS (SOLO PARA PLAZO FIJO)
    ===================================================== */
    private function generarPagos($id_contrato, $data)
    {
        if (empty($data['fecha_fin'])) {
            return; // 👈 NO generar pagos para indefinido
        }

        $fechaActual = strtotime($data['fecha_inicio']);
        $fechaFinal  = strtotime($data['fecha_fin']);

        while ($fechaActual <= $fechaFinal) {

            $periodo = date('Y-m-01', $fechaActual);

            $this->conexion->prepare("
                INSERT INTO pagos
                (id_contrato, periodo, fecha_pago, monto, estatus)
                VALUES
                (:id_contrato, :periodo, NULL, :monto, 'Pendiente')
            ")->execute([
                ':id_contrato' => $id_contrato,
                ':periodo' => $periodo,
                ':monto' => $data['renta']
            ]);

            $fechaActual = strtotime("+1 month", $fechaActual);
        }
    }

    public function generarPagosIndefinidos()
    {
        $hoy = date('Y-m-01');

        // Buscar contratos activos e indefinidos
        $consulta = $this->conexion->prepare("
        SELECT id_contrato, renta
        FROM contratos
        WHERE estatus = 'Activa'
        AND duracion = 'indefinido'
    ");

        $consulta->execute();
        $contratos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($contratos as $contrato) {

            // Verificar si ya existe pago este mes
            $verificar = $this->conexion->prepare("
            SELECT COUNT(*) FROM pagos
            WHERE id_contrato = :id
            AND periodo = :periodo
        ");

            $verificar->execute([
                ':id' => $contrato['id_contrato'],
                ':periodo' => $hoy
            ]);

            if ($verificar->fetchColumn() == 0) {

                // Crear pago del mes
                $this->conexion->prepare("
                INSERT INTO pagos
                (id_contrato, periodo, fecha_pago, monto, estatus)
                VALUES
                (:id_contrato, :periodo, NULL, :monto, 'Pendiente')
            ")->execute([
                    ':id_contrato' => $contrato['id_contrato'],
                    ':periodo' => $hoy,
                    ':monto' => $contrato['renta']
                ]);
            }
        }
    }

    public function eliminarContrato($id)
    {
        $this->conexion->beginTransaction();

        try {

            // 1️⃣ Eliminar pagos asociados
            $stmtPagos = $this->conexion->prepare("
            DELETE FROM pagos 
            WHERE id_contrato = :id
        ");
            $stmtPagos->bindParam(':id', $id);
            $stmtPagos->execute();

            // 2️⃣ Eliminar contrato
            $stmtContrato = $this->conexion->prepare("
            DELETE FROM contratos 
            WHERE id_contrato = :id
        ");
            $stmtContrato->bindParam(':id', $id);
            $stmtContrato->execute();

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}
