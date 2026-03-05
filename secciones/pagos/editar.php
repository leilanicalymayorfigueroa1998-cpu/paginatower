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
   Permiso editar pagos
========================= */
verificarPermiso($conexionBD, $idRol, 'pagos', 'editar');

/* =========================
   Obtener ID
========================= */
$id_pago = intval($_GET['txtID'] ?? 0);

if ($id_pago <= 0) {
    header("Location: index.php");
    exit();
}

/* =========================
   Obtener pago
========================= */
$consulta = $conexionBD->prepare("
SELECT *
FROM pagos
WHERE id_pago = :id
");

$consulta->execute([':id' => $id_pago]);

$pago = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$pago) {

    echo "<div class='alert alert-danger'>Pago no encontrado.</div>";
    exit();
}

$id_contrato = $pago['id_contrato'];
$fecha_pago  = $pago['fecha_pago'];
$monto       = $pago['monto'];
$metodo_pago = $pago['metodo_pago'];
$estatus     = $pago['estatus'];

/* =========================
   Obtener contratos activos
========================= */
$consultaContrato = $conexionBD->prepare("
SELECT 
    r.id_contrato,
    l.codigo,
    a.nombre
FROM contratos r
INNER JOIN locales l ON r.id_local = l.id_local
INNER JOIN arrendatarios a ON r.id_arrendatario = a.id_arrendatario
WHERE r.estatus = 'Activa'
ORDER BY l.codigo ASC
");

$consultaContrato->execute();
$listaContratos = $consultaContrato->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   Plantillas
========================= */
include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Editar Pago</h5>
        </div>

        <div class="card-body">

            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= htmlspecialchars($id_pago) ?>">

                <div class="mb-3">

                    <label class="form-label">Contrato</label>

                    <select name="id_contrato" class="form-control" required>

                        <?php foreach ($listaContratos as $contrato): ?>

                            <option value="<?= $contrato['id_contrato']; ?>"
                                <?= ($contrato['id_contrato'] == $id_contrato) ? 'selected' : ''; ?>>

                                <?= htmlspecialchars($contrato['codigo']) ?>
                                -
                                <?= htmlspecialchars($contrato['nombre']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">Fecha Pago</label>

                    <input
                        type="date"
                        class="form-control"
                        name="fecha_pago"
                        value="<?= htmlspecialchars($fecha_pago) ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Monto</label>

                    <input
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="form-control"
                        name="monto"
                        value="<?= htmlspecialchars($monto) ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Método de pago</label>

                    <select name="metodo_pago" class="form-control">

                        <option value="">-- Seleccionar --</option>

                        <option value="Efectivo" <?= ($metodo_pago == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>

                        <option value="Transferencia" <?= ($metodo_pago == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>

                        <option value="Depósito" <?= ($metodo_pago == 'Depósito') ? 'selected' : ''; ?>>Depósito</option>

                        <option value="SPEI" <?= ($metodo_pago == 'SPEI') ? 'selected' : ''; ?>>SPEI</option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">Estatus</label>

                    <select name="estatus" class="form-control" required>

                        <option value="Pagado" <?= ($estatus == 'Pagado') ? 'selected' : ''; ?>>Pagado</option>

                        <option value="Pendiente" <?= ($estatus == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>

                        <option value="Vencido" <?= ($estatus == 'Vencido') ? 'selected' : ''; ?>>Vencido</option>

                    </select>

                </div>

                <button type="submit" class="btn btn-warning">
                    Actualizar Pago
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>