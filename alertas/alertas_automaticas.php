<?php
require_once(__DIR__ . '/../bd.php');

$hoy       = date('Y-m-d');
$tres_dias = date('Y-m-d', strtotime('+3 days'));

$consulta = $conexionBD->prepare("
    SELECT id_contrato, id_arrendatario, id_local, fecha_fin
    FROM contratos
    WHERE estatus = 'Activa'
    AND fecha_fin BETWEEN :hoy AND :tres_dias
");
$consulta->bindParam(':hoy',       $hoy);
$consulta->bindParam(':tres_dias', $tres_dias);
$consulta->execute();

$contratos = $consulta->fetchAll(PDO::FETCH_ASSOC);

foreach ($contratos as $contrato) {

    $mensaje = "La renta del local ID " . $contrato['id_local'] .
               " vence el " . $contrato['fecha_fin'];

    // Verificar si ya existe alerta para este contrato y usuario
    $existe = $conexionBD->prepare("
        SELECT COUNT(*)
        FROM alertas
        WHERE id_referencia = :id_referencia
        AND tipo            = 'renta'
        AND id_usuario      = :usuario
    ");
    // CORREGIDO: el placeholder :id_referencia coincide con el SQL
    $existe->bindParam(':id_referencia', $contrato['id_contrato']);
    $existe->bindParam(':usuario',       $contrato['id_arrendatario']);
    $existe->execute();

    if ($existe->fetchColumn() == 0) {

        $insertar = $conexionBD->prepare("
            INSERT INTO alertas (id_usuario, titulo, mensaje, tipo, id_referencia)
            VALUES (:usuario, :titulo, :mensaje, :tipo, :ref)
        ");

        $titulo = "Renta próxima a vencer";
        $tipo   = "renta";

        $insertar->bindParam(':usuario',  $contrato['id_arrendatario']);
        $insertar->bindParam(':titulo',   $titulo);
        $insertar->bindParam(':mensaje',  $mensaje);
        $insertar->bindParam(':tipo',     $tipo);
        $insertar->bindParam(':ref',      $contrato['id_contrato']);
        $insertar->execute();
    }
}
