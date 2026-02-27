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

        $sql = "INSERT INTO movimientos_financieros
                (fecha, id_propiedad, id_tipo_operacion, nota, abono, cargo, origen)
                VALUES
                (:fecha, :id_propiedad, :id_tipo_operacion, :nota, :abono, :cargo, :origen)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute($data);
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