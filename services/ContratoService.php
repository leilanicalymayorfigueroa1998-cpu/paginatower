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

            // 1️⃣ Insertar contrato (incluye dia_pago personalizado)
            $stmtContrato = $this->conexion->prepare("
                INSERT INTO contratos
                (id_local, id_arrendatario, renta, deposito, adicional,
                 fecha_inicio, fecha_fin, estatus, duracion, dia_pago)
                VALUES
                (:id_local, :id_arrendatario, :renta, :deposito, :adicional,
                 :fecha_inicio, :fecha_fin, :estatus, :duracion, :dia_pago)
            ");

            $stmtContrato->execute([
                ':id_local'        => $data['id_local'],
                ':id_arrendatario' => $data['id_arrendatario'],
                ':renta'           => $data['renta'],
                ':deposito'        => $data['deposito'] ?? 0,
                ':adicional'       => $data['adicional'] ?? 0,
                ':fecha_inicio'    => $data['fecha_inicio'],
                ':fecha_fin'       => $data['fecha_fin'] ?? null,
                ':estatus'         => $data['estatus'],
                ':duracion'        => $data['duracion'],
                ':dia_pago'        => $data['dia_pago'] ?? 1,
            ]);

            $idContrato = $this->conexion->lastInsertId();

            // 2️⃣ Marcar local como ocupado si aplica
            if ($data['estatus'] === 'Activa') {
                $this->ocuparLocal($data['id_local']);
            }

            // 3️⃣ Generar pagos solo para contratos de plazo fijo
            if ($data['duracion'] !== 'Indefinido' && !empty($data['fecha_fin'])) {
                $this->generarPagos($idContrato, $data);
            }
            // Los contratos indefinidos generan pagos mes a mes vía cron

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
                duracion = :duracion,
                dia_pago = :dia_pago
            WHERE id_contrato = :id_contrato
        ");

            $consulta->execute([
                ':id_local'        => $data['id_local'],
                ':id_arrendatario' => $data['id_arrendatario'],
                ':renta'           => $data['renta'],
                ':deposito'        => $data['deposito'],
                ':adicional'       => $data['adicional'],
                ':fecha_inicio'    => $data['fecha_inicio'],
                ':fecha_fin'       => $data['fecha_fin'],
                ':estatus'         => $data['estatus'],
                ':duracion'        => $data['duracion'],
                ':dia_pago'        => $data['dia_pago'] ?? 1,
                ':id_contrato'     => $id_contrato
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
       Usa dia_pago para calcular la fecha exacta cada mes
    ===================================================== */
    private function generarPagos($id_contrato, $data)
    {
        if (empty($data['fecha_fin'])) {
            return;
        }

        $diaPago     = max(1, min(28, (int)($data['dia_pago'] ?? 1)));
        $fechaActual = strtotime($data['fecha_inicio']);
        $fechaFinal  = strtotime($data['fecha_fin']);

        while ($fechaActual <= $fechaFinal) {

            $anio   = (int) date('Y', $fechaActual);
            $mes    = (int) date('m', $fechaActual);

            // Ajustar día al máximo disponible del mes (ej. feb=28)
            $maxDia       = (int) date('t', mktime(0, 0, 0, $mes, 1, $anio));
            $diaReal      = min($diaPago, $maxDia);
            $fechaPago    = date('Y-m-d', mktime(0, 0, 0, $mes, $diaReal, $anio));
            $periodo      = date('Y-m-01', $fechaActual);

            $this->conexion->prepare("
                INSERT IGNORE INTO pagos
                (id_contrato, periodo, fecha_pago, monto, estatus)
                VALUES
                (:id_contrato, :periodo, :fecha_pago, :monto, 'Pendiente')
            ")->execute([
                ':id_contrato' => $id_contrato,
                ':periodo'     => $periodo,
                ':fecha_pago'  => $fechaPago,
                ':monto'       => $data['renta']
            ]);

            $fechaActual = strtotime("+1 month", $fechaActual);
        }
    }

    /* =====================================================
       GENERAR PAGOS INDEFINIDOS (ejecutado por cron)
       Crea el pago del mes actual con el dia_pago exacto
    ===================================================== */
    public function generarPagosIndefinidos()
    {
        $hoy    = date('Y-m-01');
        $anio   = (int) date('Y');
        $mes    = (int) date('m');

        // Traer contratos indefinidos activos con su dia_pago
        $consulta = $this->conexion->prepare("
            SELECT id_contrato, renta, dia_pago
            FROM contratos
            WHERE estatus = 'Activa'
            AND duracion = 'Indefinido'
        ");
        $consulta->execute();
        $contratos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $generados = 0;

        foreach ($contratos as $contrato) {

            // Verificar si ya existe pago para este periodo
            $verificar = $this->conexion->prepare("
                SELECT COUNT(*) FROM pagos
                WHERE id_contrato = :id
                AND periodo = :periodo
            ");
            $verificar->execute([
                ':id'      => $contrato['id_contrato'],
                ':periodo' => $hoy
            ]);

            if ($verificar->fetchColumn() == 0) {

                // Calcular fecha exacta de pago usando dia_pago del contrato
                $diaPago  = max(1, min(28, (int)($contrato['dia_pago'] ?? 1)));
                $maxDia   = (int) date('t', mktime(0, 0, 0, $mes, 1, $anio));
                $diaReal  = min($diaPago, $maxDia);
                $fechaPago = date('Y-m-d', mktime(0, 0, 0, $mes, $diaReal, $anio));

                $this->conexion->prepare("
                    INSERT INTO pagos
                    (id_contrato, periodo, fecha_pago, monto, estatus)
                    VALUES
                    (:id_contrato, :periodo, :fecha_pago, :monto, 'Pendiente')
                ")->execute([
                    ':id_contrato' => $contrato['id_contrato'],
                    ':periodo'     => $hoy,
                    ':fecha_pago'  => $fechaPago,
                    ':monto'       => $contrato['renta']
                ]);

                $generados++;
            }
        }

        return $generados;
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

?>