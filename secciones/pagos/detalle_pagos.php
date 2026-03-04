<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

$contrato = $_GET['contrato'] ?? '';

// Generar pagos mensuales automáticamente
$pagoService = new PagoService($conexionBD);
$pagoService->generarPagosMensuales();

// Marcar pagos vencidos automáticamente
$conexionBD->exec("
UPDATE pagos
SET estatus = 'Vencido'
WHERE estatus = 'Pendiente'
AND fecha_pago < CURDATE()
");

$consulta = $conexionBD->prepare("
SELECT 
    p.id_pago,
    l.codigo AS contrato,
    p.fecha_pago,
    p.monto,
    p.metodo_pago,
    p.estatus
FROM pagos p
INNER JOIN contratos r ON p.id_contrato = r.id_contrato
INNER JOIN locales l ON r.id_local = l.id_local
WHERE l.codigo = :contrato
ORDER BY p.periodo DESC
");

$consulta->execute([
    ':contrato' => $contrato
]);

$pagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Pagos del contrato: <?= htmlspecialchars($contrato) ?></h5>

            <a class="btn btn-secondary" href="index.php">
                Volver
            </a>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">

                        <tr>
                            <th>Fecha pago</th>
                            <th>Monto</th>
                            <th>Método Pago</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($pagos as $value): ?>

                            <tr>

                                <td><?= date('d/m/Y', strtotime($value['fecha_pago'])) ?></td>

                                <td>$<?= number_format($value['monto'], 2) ?></td>

                                <td><?= $value['metodo_pago'] ? htmlspecialchars($value['metodo_pago']) : 'Sin registrar' ?></td>

                                <td>

                                    <?php if ($value['estatus'] == 'Pagado'): ?>

                                        <span class="badge bg-success">Pagado</span>

                                    <?php elseif ($value['estatus'] == 'Pendiente'): ?>

                                        <span class="badge bg-warning text-dark">Pendiente</span>

                                    <?php elseif ($value['estatus'] == 'Vencido'): ?>

                                        <span class="badge bg-danger">Vencido</span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">Cancelado</span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <a class="btn btn-primary btn-sm"
                                        href="editar.php?txtID=<?= $value['id_pago'] ?>">
                                        Editar
                                    </a>

                                    <form action="eliminar.php" method="post" style="display:inline;">

                                        <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                                        <input type="hidden" name="txtID" value="<?= $value['id_pago']; ?>">

                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Seguro que deseas eliminar este pago?');">

                                            Borrar

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>