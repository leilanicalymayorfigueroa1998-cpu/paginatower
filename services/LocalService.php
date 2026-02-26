<?php

class LocalService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear($data)
    {
        if (empty($data['codigo'])) {
            throw new Exception("El código es obligatorio");
        }

        $sql = "INSERT INTO locales
            (id_propiedad, codigo, medidas, descripcion, estacionamiento, estatus)
            VALUES
            (:id_propiedad, :codigo, :medidas, :descripcion, :estacionamiento, :estatus)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    public function obtenerPropiedades()
    {
        $stmt = $this->conexion->prepare("SELECT id_propiedad, codigo FROM propiedades");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM locales WHERE id_local = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data)
    {
        $sql = "UPDATE locales SET
            id_propiedad = :id_propiedad,
            codigo = :codigo,
            medidas = :medidas,
            descripcion = :descripcion,
            estacionamiento = :estacionamiento,
            estatus = :estatus
            WHERE id_local = :id";

        $data['id'] = $id;

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    public function obtenerTodos()
    {
        $sql = "SELECT
                    l.id_local,
                    p.codigo AS propiedad,
                    l.codigo,
                    l.medidas,
                    l.descripcion,
                    l.estacionamiento,
                    l.estatus
                FROM locales l
                INNER JOIN propiedades p
                    ON p.id_propiedad = l.id_propiedad
                ORDER BY l.id_local DESC";

        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM locales WHERE id_local = :id");
        return $stmt->execute([':id' => $id]);
    }
}
