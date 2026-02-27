<?php

class PropiedadService
{
    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /* ==============================
       CREAR
    ============================== */
    public function crear($data)
    {
        if (empty($data['codigo']) || empty($data['direccion'])) {
            throw new Exception("Código y dirección son obligatorios.");
        }

        if (!empty($data['latitud']) && !is_numeric($data['latitud'])) {
            throw new Exception("Latitud inválida.");
        }

        if (!empty($data['longitud']) && !is_numeric($data['longitud'])) {
            throw new Exception("Longitud inválida.");
        }

        $sql = "INSERT INTO propiedades 
                (codigo, direccion, latitud, longitud, id_tipo, id_dueno)
                VALUES 
                (:codigo, :direccion, :latitud, :longitud, :id_tipo, :id_dueno)";

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    /* ==============================
       OBTENER TODOS (CON JOIN)
    ============================== */
    public function obtenerTodos()
    {
        $sql = "
        SELECT p.*, 
               t.nombre AS tipo,
               d.nombre AS dueno
        FROM propiedades p
        LEFT JOIN tipo_propiedad t 
            ON t.id_tipo = p.id_tipo
        LEFT JOIN duenos d
            ON d.id_dueno = p.id_dueno
        ORDER BY p.codigo
    ";

        return $this->conexion->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==============================
       OBTENER POR ID
    ============================== */
    public function obtenerPorId($id)
    {
        $stmt = $this->conexion->prepare("
        SELECT * 
        FROM propiedades 
        WHERE id_propiedad = :id
        LIMIT 1
    ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /* ==============================
       ACTUALIZAR
    ============================== */
    public function actualizar($id, $data)
    {
        $sql = "UPDATE propiedades SET
                    codigo = :codigo,
                    direccion = :direccion,
                    latitud = :latitud,
                    longitud = :longitud,
                    id_tipo = :id_tipo,
                    id_dueno = :id_dueno
                WHERE id_propiedad = :id";

        $stmt = $this->conexion->prepare($sql);

        $data['id'] = $id;

        return $stmt->execute($data);
    }

    /* ==============================
       ELIMINAR
    ============================== */
    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("
            DELETE FROM propiedades 
            WHERE id_propiedad = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    /* ==============================
       CATÁLOGOS
    ============================== */
    public function obtenerTipos()
    {
        return $this->conexion->query("
            SELECT id_tipo, nombre
            FROM tipo_propiedad
            WHERE estatus = 'Activo'
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDuenos()
    {
        return $this->conexion->query("
            SELECT id_dueno, nombre 
            FROM duenos
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
