<?php

class RestriccionService
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // ✅ LISTAR
    public function obtenerTodas(): array
    {
        $sql = "SELECT r.id_restriccion, r.restriccion, l.codigo
                FROM restricciones r
                INNER JOIN locales l ON r.id_local = l.id_local
                ORDER BY r.id_restriccion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ OBTENER POR ID
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM restricciones WHERE id_restriccion = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    // ✅ CREAR
    public function crearRestriccion(int $id_local, string $restriccion): bool
    {
        $restriccion = trim($restriccion);

        if (empty($restriccion)) {
            throw new Exception("La restricción no puede estar vacía.");
        }

        // 🔒 Evitar duplicado por mismo local
        if ($this->existeRestriccion($id_local, $restriccion)) {
            throw new Exception("Esta restricción ya existe para este local.");
        }

        $sql = "INSERT INTO restricciones (id_local, restriccion)
                VALUES (:id_local, :restriccion)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':restriccion', $restriccion);

        return $stmt->execute();
    }

    private function existeRestriccionParaOtro(int $id, int $id_local, string $restriccion): bool
    {
        $sql = "SELECT COUNT(*) 
            FROM restricciones 
            WHERE id_local = :id_local 
            AND restriccion = :restriccion
            AND id_restriccion != :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':restriccion', $restriccion);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // ✅ ACTUALIZAR
    public function actualizarRestriccion(int $id, int $id_local, string $restriccion): bool
    {
        $restriccion = trim($restriccion);

        if (empty($restriccion)) {
            throw new Exception("La restricción no puede estar vacía.");
        }

        // 🔒 Evitar duplicado (excepto el mismo registro)
        if ($this->existeRestriccionParaOtro($id, $id_local, $restriccion)) {
            throw new Exception("Esta restricción ya existe para este local.");
        }

        $sql = "UPDATE restricciones 
            SET id_local = :id_local,
                restriccion = :restriccion
            WHERE id_restriccion = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':restriccion', $restriccion);

        return $stmt->execute();
    }

    // ✅ ELIMINAR
    public function eliminarRestriccion(int $id): bool
    {
        $sql = "DELETE FROM restricciones WHERE id_restriccion = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 🔎 MÉTODO PRIVADO PARA VALIDAR DUPLICADOS
    private function existeRestriccion(int $id_local, string $restriccion): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM restricciones 
                WHERE id_local = :id_local 
                AND restriccion = :restriccion";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':restriccion', $restriccion);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}
