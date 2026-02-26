<?php

class DuenoService
{

    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear(array $data): bool
    {

        if (empty($data['nombre']) || empty($data['telefono'])) {
            throw new Exception("Nombre y teléfono son obligatorios.");
        }

        if (
            !empty($data['correo']) &&
            !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception("Correo inválido.");
        }

        try {

            $consulta = $this->conexion->prepare("
                INSERT INTO duenos (nombre, telefono, correo)
                VALUES (:nombre, :telefono, :correo)
            ");

            return $consulta->execute([
                ':nombre' => trim($data['nombre']),
                ':telefono' => trim($data['telefono']),
                ':correo' => $data['correo'] ?: null
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al crear dueño.");
        }
    }

    public function listar(): array
    {

        $consulta = $this->conexion->prepare("
            SELECT id_dueno, nombre, telefono, correo
            FROM duenos
            ORDER BY id_dueno DESC
        ");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {

        $consulta = $this->conexion->prepare("
            SELECT id_dueno, nombre, telefono, correo
            FROM duenos
            WHERE id_dueno = :id
        ");

        $consulta->execute([':id' => $id]);

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function actualizar(int $id, array $data): bool
    {
        if (empty($data['nombre']) || empty($data['telefono'])) {
            throw new Exception("Nombre y teléfono son obligatorios.");
        }

        if (
            !empty($data['correo']) &&
            !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception("Correo inválido.");
        }

        try {

            $consulta = $this->conexion->prepare("
            UPDATE duenos SET
                nombre = :nombre,
                telefono = :telefono,
                correo = :correo
            WHERE id_dueno = :id
        ");

            return $consulta->execute([
                ':nombre'   => trim($data['nombre']),
                ':telefono' => trim($data['telefono']),
                ':correo'   => $data['correo'] ?: null,
                ':id'       => $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar dueño.");
        }
    }

    public function eliminar(int $id): bool
    {

        if ($id <= 0) {
            throw new Exception("ID inválido.");
        }

        $consulta = $this->conexion->prepare("
            DELETE FROM duenos
            WHERE id_dueno = :id
        ");

        return $consulta->execute([':id' => $id]);
    }
}
