<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/MovimientoService.php';

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos_financieros', 'editar');

$id = $_GET['txtID'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location:index.php");
    exit();
}

$service = new MovimientoService($conexionBD);

$movimiento = $service->obtenerPorId($id);

if (!$movimiento) {
    header("Location:index.php");
    exit();
}

$tiposOperacion = $service->obtenerTiposOperacion();
$propiedades = $service->obtenerPropiedades();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Editar Movimiento Financiero</div>

        <div class="card-body">

            <form action="actualizar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $movimiento['id_movimiento']; ?>">

                <!-- FECHA -->
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date"
                        class="form-control"
                        name="fecha"
                        value="<?= htmlspecialchars($movimiento['fecha']); ?>"
                        required>
                </div>

                <!-- PROPIEDAD -->
                <div class="mb-3">
                    <label class="form-label">Propiedad</label>

                    <select name="id_propiedad" class="form-select" required>

                        <option value="">Seleccione una propiedad</option>

                        <?php foreach ($propiedades as $p): ?>

                            <option value="<?= $p['id_propiedad']; ?>"
                                <?= ($p['id_propiedad'] == $movimiento['id_propiedad']) ? 'selected' : ''; ?>>

                                <?= $p['id_propiedad']; ?> - <?= htmlspecialchars($p['codigo']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- TIPO OPERACION -->
                <div class="mb-3">

                    <label class="form-label">Tipo de operación</label>

                    <select name="id_tipo_operacion" class="form-select" id="tipoOperacion" required>

                        <option value="">Seleccione un código</option>

                        <?php foreach ($tiposOperacion as $tipo): ?>

                            <option
                                value="<?= $tipo['id']; ?>"
                                data-concepto="<?= htmlspecialchars($tipo['concepto']); ?>"
                                <?= ($tipo['id'] == $movimiento['id_tipo_operacion']) ? 'selected' : ''; ?>>

                                <?= htmlspecialchars($tipo['codigo']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- CONCEPTO -->
                <div class="mb-3">

                    <label class="form-label">Concepto</label>

                    <input type="text"
                        id="concepto"
                        class="form-control"
                        value=""
                        readonly>

                </div>

                <!-- NOTA -->
                <div class="mb-3">
                    <label class="form-label">Nota</label>

                    <input type="text"
                        class="form-control"
                        name="nota"
                        value="<?= htmlspecialchars($movimiento['nota']); ?>">
                </div>

                <!-- ABONO / CARGO -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Abono</label>

                        <input type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="abono"
                            value="<?= htmlspecialchars($movimiento['abono']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cargo</label>

                        <input type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="cargo"
                            value="<?= htmlspecialchars($movimiento['cargo']); ?>">
                    </div>

                </div>

                <!-- ORIGEN -->
                <div class="mb-3">

                    <label class="form-label">Origen</label>

                    <select name="origen" class="form-select" required>

                        <option value="CUENTA"
                            <?= ($movimiento['origen'] == 'CUENTA') ? 'selected' : ''; ?>>

                            Cuenta

                        </option>

                        <option value="EFECTIVO"
                            <?= ($movimiento['origen'] == 'EFECTIVO') ? 'selected' : ''; ?>>

                            Efectivo

                        </option>

                    </select>

                </div>

                <button type="submit" class="btn btn-success">
                    Modificar
                </button>

                <a class="btn btn-secondary" href="index.php">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

<script src="../../assets/js/movimientos_financieros.js"></script>

<?php include('../../templates/pie.php'); ?>