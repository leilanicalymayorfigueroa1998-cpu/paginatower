<?php

class MovimientoService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Crear movimiento
    public function crear($data)
    {
        if ($data['abono'] > 0 && $data['cargo'] > 0) {
            throw new Exception("No puede registrar abono y cargo al mismo tiempo.");
        }

        if ($data['abono'] <= 0 && $data['cargo'] <= 0) {
            throw new Exception("Debe registrar un abono o un cargo.");
        }

        $this->conexion->beginTransaction();

        try {
            // 1. Insertar movimiento
            $sql = "INSERT INTO movimientos_financieros
                    (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen, id_pago)
                    VALUES
                    (:fecha, :id_propiedad, :id_tipo_operacion, :nota, :abono, :cargo, :origen, :id_pago)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':fecha'             => $data['fecha'],
                ':id_propiedad'      => $data['id_propiedad'],
                ':id_tipo_operacion' => $data['id_tipo_operacion'],
                ':nota'              => $data['nota'],
                ':abono'             => $data['abono'],
                ':cargo'             => $data['cargo'],
                ':origen'            => $data['origen'],
                ':id_pago'           => $data['id_pago'] ?? null,
            ]);

            $id_movimiento = $this->conexion->lastInsertId();

            // 2. Si es código IR (Ingreso por Rentas) y hay abono → afecta pagos y contratos
            if ($data['abono'] > 0) {
                $codigoIR = $this->esCodigoIR($data['id_tipo_operacion']);

                if ($codigoIR) {
                    // Buscar el contrato activo del local/propiedad
                    $stmtContrato = $this->conexion->prepare("
                        SELECT id_contrato, renta
                        FROM contratos
                        WHERE id_local = :id_propiedad
                        AND estatus = 'Activa'
                        ORDER BY id_contrato DESC
                        LIMIT 1
                    ");
                    $stmtContrato->execute([':id_propiedad' => $data['id_propiedad']]);
                    $contrato = $stmtContrato->fetch(PDO::FETCH_ASSOC);

                    if ($contrato) {
                        $id_contrato = $contrato['id_contrato'];
                        $periodo = date('Y-m-01', strtotime($data['fecha']));

                        // Verificar si ya existe pago para este periodo
                        $checkPago = $this->conexion->prepare("
                            SELECT id_pago FROM pagos
                            WHERE id_contrato = :id_contrato
                            AND periodo = :periodo
                        ");
                        $checkPago->execute([
                            ':id_contrato' => $id_contrato,
                            ':periodo'     => $periodo,
                        ]);
                        $pagoExistente = $checkPago->fetch();

                        if ($pagoExistente) {
                            // Actualizar pago existente a Pagado
                            $this->conexion->prepare("
                                UPDATE pagos
                                SET estatus     = 'Pagado',
                                    fecha_pago  = :fecha,
                                    monto       = :monto,
                                    metodo_pago = :metodo
                                WHERE id_pago = :id_pago
                            ")->execute([
                                ':fecha'    => $data['fecha'],
                                ':monto'    => $data['abono'],
                                ':metodo'   => strtolower($data['origen'] ?? 'Efectivo'),
                                ':id_pago'  => $pagoExistente['id_pago'],
                            ]);

                            // Enlazar movimiento con el pago
                            $this->conexion->prepare("
                                UPDATE movimientos_financieros
                                SET id_pago = :id_pago
                                WHERE id_movimiento = :id_mov
                            ")->execute([
                                ':id_pago' => $pagoExistente['id_pago'],
                                ':id_mov'  => $id_movimiento,
                            ]);
                        } else {
                            // Crear nuevo pago
                            $this->conexion->prepare("
                                INSERT INTO pagos
                                (id_contrato, periodo, fecha_pago, monto, metodo_pago, estatus)
                                VALUES (:id_contrato, :periodo, :fecha, :monto, :metodo, 'Pagado')
                            ")->execute([
                                ':id_contrato' => $id_contrato,
                                ':periodo'     => $periodo,
                                ':fecha'       => $data['fecha'],
                                ':monto'       => $data['abono'],
                                ':metodo'      => strtolower($data['origen'] ?? 'Efectivo'),
                            ]);

                            $id_pago_nuevo = $this->conexion->lastInsertId();

                            // Enlazar movimiento con el pago nuevo
                            $this->conexion->prepare("
                                UPDATE movimientos_financieros
                                SET id_pago = :id_pago
                                WHERE id_movimiento = :id_mov
                            ")->execute([
                                ':id_pago' => $id_pago_nuevo,
                                ':id_mov'  => $id_movimiento,
                            ]);
                        }

                        // Descontar deuda del contrato
                        $this->conexion->prepare("
                            UPDATE contratos
                            SET deuda = GREATEST(deuda - :abono, 0)
                            WHERE id_contrato = :id_contrato
                        ")->execute([
                            ':abono'       => $data['abono'],
                            ':id_contrato' => $id_contrato,
                        ]);
                    }
                } else {
                    // No es IR pero igual descuenta deuda si hay abono
                    $this->conexion->prepare("
                        UPDATE contratos
                        SET deuda = GREATEST(deuda - :abono, 0)
                        WHERE id_local = :id_propiedad
                        AND estatus = 'Activa'
                    ")->execute([
                        ':abono'       => $data['abono'],
                        ':id_propiedad' => $data['id_propiedad'],
                    ]);
                }
            }

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    // Verificar si el tipo de operación es un código IR (Ingreso por Rentas)
    private function esCodigoIR($id_tipo_operacion)
    {
        $stmt = $this->conexion->prepare("
            SELECT codigo FROM tipo_operacion WHERE id = :id
        ");
        $stmt->execute([':id' => $id_tipo_operacion]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
        // C_IR = Ingreso por rentas cobradas (cuenta), E_IR = Ingreso por rentas cobradas (efectivo)
        return $tipo && in_array($tipo['codigo'], ['C_IR', 'E_IR']);
    }

    // Obtener todos
    public function obtenerTodos()
    {
        $sql = "SELECT m.*, p.codigo AS propiedad, t.codigo AS tipo_codigo
                FROM movimientos_financieros m
                INNER JOIN propiedades p ON p.id_propiedad = m.id_propiedad
                INNER JOIN tipo_operacion t ON t.id = m.id_tipo_operacion
                ORDER BY m.fecha DESC";

        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Balance ingresos y egresos separado por cuenta/efectivo
    public function obtenerBalance()
    {
        $sql = "SELECT
                    origen,
                    SUM(abono) AS total_abonos,
                    SUM(cargo) AS total_cargos,
                    SUM(abono) - SUM(cargo) AS balance
                FROM movimientos_financieros
                GROUP BY origen";

        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Balance del mes actual
    public function obtenerBalanceMes()
    {
        $sql = "SELECT
                    origen,
                    SUM(abono) AS total_abonos,
                    SUM(cargo) AS total_cargos,
                    SUM(abono) - SUM(cargo) AS balance
                FROM movimientos_financieros
                WHERE YEAR(fecha) = YEAR(CURDATE())
                  AND MONTH(fecha) = MONTH(CURDATE())
                GROUP BY origen";

        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por ID
    public function obtenerPorId($id)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM movimientos_financieros WHERE id_movimiento = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar
    public function actualizar($id, $data)
    {
        $data['id'] = $id;

        $sql = "UPDATE movimientos_financieros SET
                fecha = :fecha,
                id_propiedad = :id_propiedad,
                id_tipo_operacion = :id_tipo_operacion,
                nota = :nota,
                abono = :abono,
                cargo = :cargo,
                origen = :origen
                WHERE id_movimiento = :id";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    // Eliminar
    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM movimientos_financieros WHERE id_movimiento = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Catálogos
    public function obtenerTiposOperacion()
    {
        return $this->conexion->query("
            SELECT id, codigo, concepto 
            FROM tipo_operacion 
            ORDER BY codigo
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPropiedades()
    {
        return $this->conexion->query("
            SELECT id_propiedad, codigo 
            FROM propiedades
            ORDER BY codigo
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
