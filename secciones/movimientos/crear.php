<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos_financieros', 'crear');

// Obtener tipos de operación
$consultaTipos = $conexionBD->prepare("
    SELECT id, codigo, concepto 
    FROM tipo_operacion 
    ORDER BY codigo
");
$consultaTipos->execute();
$tiposOperacion = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

// Obtener propiedades
$consultaPropiedades = $conexionBD->prepare("
    SELECT id_propiedad, codigo 
    FROM propiedades
    ORDER BY codigo
");
$consultaPropiedades->execute();
$propiedades = $consultaPropiedades->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header">
            Movimientos Financieros
        </div>

        <div class="card-body">

            <form action="guardar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <!-- FECHA -->
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-control" name="fecha" required>
                </div>

                <!-- PROPIEDAD -->
                <div class="mb-3">
                    <label class="form-label">Propiedad</label>

                    <select name="id_propiedad" class="form-select" required>

                        <option value="">Seleccione una propiedad</option>

                        <?php foreach ($propiedades as $p) { ?>

                            <option value="<?= $p['id_propiedad']; ?>">

                                <?= $p['id_propiedad']; ?> - <?= htmlspecialchars($p['codigo']); ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- TIPO OPERACION -->
                <div class="mb-3">

                    <label class="form-label">Tipo de operación</label>

                    <select name="id_tipo_operacion" class="form-select" id="tipoOperacion" required>

                        <option value="">Seleccione un código</option>

                        <?php foreach ($tiposOperacion as $tipo) { ?>

                            <option
                                value="<?= $tipo['id']; ?>"
                                data-concepto="<?= htmlspecialchars($tipo['concepto']); ?>">

                                <?= htmlspecialchars($tipo['codigo']); ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <!-- CONCEPTO -->
                <div class="mb-3">

                    <label class="form-label">Concepto</label>

                    <input type="text" id="concepto" class="form-control" readonly>

                </div>

                <!-- NOTA -->
                <div class="mb-3">

                    <label class="form-label">Nota</label>

                    <input type="text" class="form-control" name="nota">

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
                               placeholder="0.00">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">Cargo</label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               class="form-control"
                               name="cargo"
                               placeholder="0.00">

                    </div>

                </div>

                <!-- ORIGEN -->
                <div class="mb-3">

                    <label class="form-label">Origen</label>

                    <select class="form-select" name="origen" required>

                        <option value="">Seleccione un origen</option>

                        <option value="CUENTA">Cuenta</option>

                        <option value="EFECTIVO">Efectivo</option>

                    </select>

                </div>

                <!-- BOTONES -->
                <button type="submit" class="btn btn-success">
                    Guardar
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