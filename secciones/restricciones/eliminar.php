<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// 🔐 Permiso para eliminar
verificarPermiso($conexionBD, $idRol, 'restricciones', 'eliminar');

if (!isset($_GET['txtID'])) {
    header("Location:index.php");
    exit();
}

$txtID = $_GET['txtID'];

// 🔎 Verificar que exista
$consulta = $conexionBD->prepare("
    SELECT id_restriccion 
    FROM restricciones 
    WHERE id_restriccion = :id
");
$consulta->bindParam(':id', $txtID);
$consulta->execute();

$existe = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$existe) {
    header("Location:index.php");
    exit();
}

// 🗑 Eliminar
$consultaEliminar = $conexionBD->prepare("
    DELETE FROM restricciones 
    WHERE id_restriccion = :id
");
$consultaEliminar->bindParam(':id', $txtID);
$consultaEliminar->execute();

header("Location:index.php?mensaje=eliminado");
exit();