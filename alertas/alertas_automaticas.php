<?php
require_once(__DIR__ . '/../bd.php');

$hoy = date('Y-m-d');
$tres_dias = date('Y-m-d', strtotime('+3 days'));

$consulta = $conexionBD->prepare("SELECT id_contrato, id_cliente, id_local, fecha_fin
    FROM contratos
    WHERE estatus = 'Activa'
    AND fecha_fin BETWEEN :hoy AND :tres_dias");

$consulta->bindParam(':hoy', $hoy);
$consulta->bindParam(':tres_dias', $tres_dias);
$consulta->execute();

$rentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

foreach ($rentas as $renta) {

    $mensaje = "La renta del local ID " . $renta['id_local'] .
        " vence el " . $renta['fecha_fin'];

    // Verificar si ya existe alerta
    $existe = $conexionBD->prepare("SELECT COUNT(*)
        FROM alertas
        WHERE id_referencia = :id_renta
        AND tipo = 'renta'
        AND id_usuario = :usuario");

    $existe->bindParam(':id_contrato', $renta['id_contrato']);
    $existe->bindParam(':usuario', $renta['id_cliente']);
    $existe->execute();

    if ($existe->fetchColumn() == 0) {

        $insertar = $conexionBD->prepare("INSERT INTO alertas 
            (id_usuario, titulo, mensaje, tipo, id_referencia)
            VALUES 
            (:usuario, :titulo, :mensaje, :tipo, :ref) ");

        $titulo = "Renta próxima a vencer";
        $tipo = "renta";

        $insertar->bindParam(':usuario', $renta['id_cliente']);
        $insertar->bindParam(':titulo', $titulo);
        $insertar->bindParam(':mensaje', $mensaje);
        $insertar->bindParam(':tipo', $tipo);
        $insertar->bindParam(':ref', $renta['id_contrato']);

        $insertar->execute();
    }
}
