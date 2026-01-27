<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'dueno') {
    header("Location: ../../index.php");
    exit();
}

include("../../bd.php");


include('../../templates/cabecera.php');

$id_dueno = $_SESSION['id_usuario'];

?>

<div class="container mt-4">
    <h2>Dashboard del Dueño</h2>

    <div class="row">

        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h6>Mis propiedades</h6>
                    <h3><?= $total_propiedades ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h6>Rentas activas</h6>
                    <h3><?= $total_rentas ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h6>Ingresos totales</h6>
                    <h3>$<?= number_format($total_pagos, 2) ?></h3>
                </div>
            </div>
        </div>

    </div>
</div>