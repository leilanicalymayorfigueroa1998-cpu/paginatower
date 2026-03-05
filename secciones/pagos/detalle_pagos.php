<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PagosService.php');

/* =========================
   Verificar sesión
========================= */
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

/* =========================
   Permisos
========================= */
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'pagos', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'pagos', 'eliminar');

/* =========================
   Limpiar parámetro
========================= */
$contrato = trim($_GET['contrato'] ?? '');

if ($contrato == '') {
    echo "<div class='alert alert-danger'>Contrato inválido</div>";
    exit();
}

$pagoService = new PagoService($conexionBD);

/* =========================
   Obtener contrato
========================= */
$stmtContrato = $conexionBD->prepare("
SELECT r.id_contrato
FROM contratos r
INNER JOIN locales l ON r.id_local = l.id_local
WHERE l.codigo = :codigo
LIMIT 1
");

$stmtContrato->execute([
    ':codigo' => $contrato
]);

$contratoData = $stmtContrato->fetch(PDO::FETCH_ASSOC);

if (!$contratoData) {
    echo "<div class='alert alert-danger'>Contrato no encontrado</div>";
    exit();
}

$idContrato = $contratoData['id_contrato'];

/* =========================
   Marcar pagos vencidos
========================= */
$stmtVencidos = $conexionBD->prepare("
UPDATE pagos
SET estatus = 'Vencido'
WHERE estatus = 'Pendiente'
AND fecha_pago < CURDATE()
AND id_contrato = :id
");

$stmtVencidos->execute([
    ':id' => $idContrato
]);

/* =========================
   Consultar pagos
========================= */
$consulta = $conexionBD->prepare("
SELECT 
    id_pago,
    fecha_pago,
    monto,
    metodo_pago,
    estatus
FROM pagos
WHERE id_contrato = :id
ORDER BY periodo DESC
");

$consulta->execute([
    ':id' => $idContrato
]);

$pagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">
Pagos del contrato: <?= htmlspecialchars($contrato) ?>
</h5>

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

<?php if (empty($pagos)): ?>

<tr>
<td colspan="5" class="text-center">
No hay pagos registrados
</td>
</tr>

<?php else: ?>

<?php foreach ($pagos as $value): ?>

<tr>

<td>
<?= $value['fecha_pago'] 
? date('d/m/Y', strtotime($value['fecha_pago'])) 
: 'Sin fecha'; ?>
</td>

<td>
$<?= number_format($value['monto'], 2) ?>
</td>

<td>
<?= $value['metodo_pago']
? htmlspecialchars($value['metodo_pago'])
: 'Sin registrar' ?>
</td>

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

<?php if ($puedeEditar): ?>

<a class="btn btn-primary btn-sm"
href="editar.php?txtID=<?= $value['id_pago'] ?>">
Editar
</a>

<?php endif; ?>

<?php if ($puedeEliminar): ?>

<form action="eliminar.php" method="post" style="display:inline;">

<input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

<input type="hidden" name="txtID" value="<?= $value['id_pago']; ?>">

<button type="submit"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Seguro que deseas eliminar este pago?');">

Borrar

</button>

</form>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php include('../../templates/pie.php'); ?>