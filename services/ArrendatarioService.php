<?php

class ArrendatarioService
{

    private $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear(array $data): bool
    {

        // 🔹 Validaciones básicas
        if (empty($data['nombre']) || empty($data['telefono'])) {
            throw new Exception("Nombre y teléfono son obligatorios.");
        }

        // 🔹 Validar correos si vienen llenos
        if (
            !empty($data['correo']) &&
            !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception("Correo inválido.");
        }

        if (
            !empty($data['correoaval']) &&
            !filter_var($data['correoaval'], FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception("Correo del aval inválido.");
        }

        try {

            $consulta = $this->conexion->prepare("
                INSERT INTO arrendatarios
                (nombre, telefono, correo, aval, correoaval, direccion, ciudad)
                VALUES
                (:nombre, :telefono, :correo, :aval, :correoaval, :direccion, :ciudad)
            ");

            return $consulta->execute([
                ':nombre' => trim($data['nombre']),
                ':telefono' => trim($data['telefono']),
                ':correo' => $data['correo'] ?: null,
                ':aval' => $data['aval'] ?: null,
                ':correoaval' => $data['correoaval'] ?: null,
                ':direccion' => $data['direccion'] ?: null,
                ':ciudad' => $data['ciudad'] ?: null
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al crear arrendatario.");
        }
    }

    public function actualizar(int $id, array $data): bool
    {

        if ($id <= 0) {
            throw new Exception("ID inválido.");
        }

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
            UPDATE arrendatarios SET
                nombre = :nombre,
                telefono = :telefono,
                correo = :correo,
                aval = :aval,
                correoaval = :correoaval,
                direccion = :direccion,
                ciudad = :ciudad
            WHERE id_arrendatario = :id
        ");

            return $consulta->execute([
                ':nombre' => trim($data['nombre']),
                ':telefono' => trim($data['telefono']),
                ':correo' => $data['correo'] ?: null,
                ':aval' => $data['aval'] ?: null,
                ':correoaval' => $data['correoaval'] ?: null,
                ':direccion' => $data['direccion'] ?: null,
                ':ciudad' => $data['ciudad'] ?: null,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar arrendatario.");
        }
    }


    public function listar(): array
    {

        $consulta = $this->conexion->prepare("
        SELECT * FROM arrendatarios
        ORDER BY id_arrendatario DESC
    ");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar(int $id): bool
    {

        $consulta = $this->conexion->prepare("
        DELETE FROM arrendatarios
        WHERE id_arrendatario = :id
    ");

        return $consulta->execute([':id' => $id]);
    }

    public function obtenerPorId(int $id): ?array
    {

        $consulta = $this->conexion->prepare("
        SELECT * FROM arrendatarios
        WHERE id_arrendatario = :id
    ");

        $consulta->execute([':id' => $id]);

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }
}
