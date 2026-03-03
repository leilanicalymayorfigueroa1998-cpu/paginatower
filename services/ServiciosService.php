<?php

class ServiciosService
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // ✅ LISTAR
    public function obtenerTodos(): array
    {
        $sql = "SELECT s.id_servicio,
                       s.cfe,
                       s.agua,
                       s.contrato_cfe,
                       s.contrato_agua,
                       l.codigo
                FROM servicios s
                INNER JOIN locales l ON s.id_local = l.id_local
                ORDER BY s.id_servicio DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ OBTENER POR ID
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM servicios WHERE id_servicio = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    // ✅ CREAR
    public function crear(
        int $id_local,
        int $cfe,
        int $agua,
        ?string $contrato_cfe,
        ?string $contrato_agua
    ): bool {

        // 🔒 Evitar duplicado por local
        if ($this->existeServicioPorLocal($id_local)) {
            throw new Exception("Este local ya tiene servicios registrados.");
        }

        // 🧠 Validaciones
        if ($cfe === 1 && empty($contrato_cfe)) {
            throw new Exception("Debe ingresar contrato CFE.");
        }

        if ($agua === 1 && empty($contrato_agua)) {
            throw new Exception("Debe ingresar contrato Agua.");
        }

        $sql = "INSERT INTO servicios 
                (id_local, cfe, agua, contrato_cfe, contrato_agua)
                VALUES 
                (:id_local, :cfe, :agua, :contrato_cfe, :contrato_agua)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':cfe', $cfe, PDO::PARAM_INT);
        $stmt->bindValue(':agua', $agua, PDO::PARAM_INT);
        $stmt->bindValue(':contrato_cfe', $contrato_cfe);
        $stmt->bindValue(':contrato_agua', $contrato_agua);

        return $stmt->execute();
    }

    // ✅ ACTUALIZAR
    public function actualizar(
        int $id,
        int $id_local,
        int $cfe,
        int $agua,
        ?string $contrato_cfe,
        ?string $contrato_agua
    ): bool {

        if ($cfe === 1 && empty($contrato_cfe)) {
            throw new Exception("Debe ingresar contrato CFE.");
        }

        if ($agua === 1 && empty($contrato_agua)) {
            throw new Exception("Debe ingresar contrato Agua.");
        }

        $sql = "UPDATE servicios 
                SET id_local = :id_local,
                    cfe = :cfe,
                    agua = :agua,
                    contrato_cfe = :contrato_cfe,
                    contrato_agua = :contrato_agua
                WHERE id_servicio = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->bindValue(':cfe', $cfe, PDO::PARAM_INT);
        $stmt->bindValue(':agua', $agua, PDO::PARAM_INT);
        $stmt->bindValue(':contrato_cfe', $contrato_cfe);
        $stmt->bindValue(':contrato_agua', $contrato_agua);

        return $stmt->execute();
    }

    // ✅ ELIMINAR
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM servicios WHERE id_servicio = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 🔎 VALIDAR DUPLICADO POR LOCAL
    private function existeServicioPorLocal(int $id_local): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM servicios 
                WHERE id_local = :id_local";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_local', $id_local, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}