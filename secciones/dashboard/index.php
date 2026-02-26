<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'dashboard', 'ver');

// TOTAL
$totalContratos = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
")->fetchColumn();

// ACTIVOS
$activos = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
    WHERE estatus = 'Activa'
")->fetchColumn();

// VENCIDOS
$vencidos = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
    WHERE estatus = 'Activa'
    AND fecha_fin IS NOT NULL
    AND fecha_fin < CURDATE()
")->fetchColumn();

// POR VENCER (5 días)
$porVencer = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
    WHERE estatus = 'Activa'
    AND fecha_fin IS NOT NULL
    AND fecha_fin BETWEEN CURDATE() 
    AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
")->fetchColumn();

// INDEFINIDOS
$indefinidos = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
    WHERE estatus = 'Activa'
    AND duracion = 'Indefinido'
")->fetchColumn();

// FINALIZADOS
$finalizados = $conexionBD->query("
    SELECT COUNT(*) FROM contratos
    WHERE estatus IN ('Finalizada','Cancelada')
")->fetchColumn();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="content">

    <h4 class="mb-4">Resumen de Contratos</h4>

    <div class="row mb-4">

        <div class="col-md-2 mb-3">
            <div class="card border-start border-dark border-4 shadow-sm">
                <div class="card-body">
                    <small>Total</small>
                    <h4><?= $totalContratos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <small>Activos</small>
                    <h4><?= $activos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <small>Vencidos</small>
                    <h4><?= $vencidos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <small>Por vencer</small>
                    <h4><?= $porVencer ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <small>Indefinidos</small>
                    <h4><?= $indefinidos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-start border-secondary border-4 shadow-sm">
                <div class="card-body">
                    <small>Finalizados</small>
                    <h4><?= $finalizados ?></h4>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="mb-3">Contratos por Estado</h5>
                <canvas id="graficaContratos"></canvas>
            </div>
        </div>

    </div>
</div>

<script>
            document.addEventListener("DOMContentLoaded", function() {

                const duracion = document.getElementById("duracion");
                const fechaInicio = document.querySelector("input[name='fecha_inicio']");
                const fechaFin = document.getElementById("fecha_fin");

                function actualizarFechaFin() {

                    if (!fechaInicio.value) return;

                    let inicio = new Date(fechaInicio.value);
                    let nuevaFecha = new Date(inicio);

                    if (duracion.value === "6") {
                        nuevaFecha.setMonth(nuevaFecha.getMonth() + 6);
                        fechaFin.value = nuevaFecha.toISOString().split('T')[0];
                    } else if (duracion.value === "12") {
                        nuevaFecha.setMonth(nuevaFecha.getMonth() + 12);
                        fechaFin.value = nuevaFecha.toISOString().split('T')[0];
                    } else if (duracion.value === "indefinido") {
                        fechaFin.value = "";
                    }
                }

                duracion.addEventListener("change", actualizarFechaFin);
                fechaInicio.addEventListener("change", actualizarFechaFin);

            });
        </script>

        <script>
            const ctx = document.getElementById('graficaContratos');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Activos', 'Vencidos', 'Por vencer', 'Indefinidos', 'Finalizados'],
                    datasets: [{
                        label: 'Cantidad',
                        data: [
                            <?= $activos ?>,
                            <?= $vencidos ?>,
                            <?= $porVencer ?>,
                            <?= $indefinidos ?>,
                            <?= $finalizados ?>
                        ],
                        backgroundColor: [
                            '#198754', // verde
                            '#dc3545', // rojo
                            '#ffc107', // amarillo
                            '#0d6efd', // azul
                            '#6c757d' // gris
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

<?php include('../../templates/pie.php'); ?>