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
   Verificar permiso
========================= */
verificarPermiso($conexionBD, $idRol, 'pagos', 'crear');

/* =========================
   Obtener contratos activos
========================= */
try {

    $consultaContrato = $conexionBD->prepare("
        SELECT 
            r.id_contrato,
            l.codigo AS codigo_local,
            a.nombre AS nombre_arrendatario
        FROM contratos r
        INNER JOIN locales l 
            ON r.id_local = l.id_local
        INNER JOIN arrendatarios a 
            ON r.id_arrendatario = a.id_arrendatario
        WHERE r.estatus = 'Activa'
        ORDER BY l.codigo ASC
    ");

    $consultaContrato->execute();
    $listaContratos = $consultaContrato->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {

    echo "<h3>Error al cargar contratos</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

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
            <h5 class="mb-0">Nuevo Pago</h5>
        </div>

        <div class="card-body">

            <?php if (empty($listaContratos)): ?>

                <div class="alert alert-warning">
                    No hay contratos activos para registrar pagos.
                </div>

            <?php else: ?>

                <form action="guardar.php" method="post">

                    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                    <div class="mb-3">
                        <label class="form-label">Contrato</label>

                        <select name="id_contrato" class="form-control" required>

                            <option value="">-- Selecciona un contrato --</option>

                            <?php foreach ($listaContratos as $contrato): ?>

                                <option value="<?= $contrato['id_contrato']; ?>">

                                    <?= htmlspecialchars($contrato['codigo_local']) ?>
                                    -
                                    <?= htmlspecialchars($contrato['nombre_arrendatario']); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" name="fecha_pago" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Método de Pago</label>

                        <select name="metodo_pago" class="form-control">

                            <option value="">-- Selecciona método --</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Deposito">Depósito</option>
                            <option value="SPEI">SPEI</option>

                        </select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estatus</label>

                        <select name="estatus" class="form-control" required>

                            <option value="">-- Selecciona estatus --</option>
                            <option value="Pagado">Pagado</option>
                            <option value="Pendiente">Pendiente</option>

                        </select>

                    </div>

                    <button type="submit" class="btn btn-success">
                        Guardar Pago
                    </button>

                    <a href="index.php" class="btn btn-secondary">
                        Cancelar
                    </a>

                </form>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>