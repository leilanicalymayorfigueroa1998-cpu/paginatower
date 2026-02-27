<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos', 'crear');

// Tabla Tipo Operación
$consultaTipos = $conexionBD->prepare("
    SELECT id, codigo, concepto 
    FROM tipo_operacion 
    ORDER BY codigo
");
$consultaTipos->execute();
$tiposOperacion = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

// Tabla Propiedades
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
        <div class="card-header">Movimientos Financieros</div>
        <div class="card-body">

            <form action="guardar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-control" name="fecha" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Propiedad</label>
                    <select name="id_propiedad" class="form-control" required>
                        <option value="">Seleccione una propiedad</option>
                        <?php foreach ($propiedades as $p) { ?>
                            <option value="<?= $p['id_propiedad']; ?>">
                                <?= htmlspecialchars($p['codigo']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de operación</label>
                    <select name="id_tipo_operacion" class="form-select" required>
                        <option value="">Seleccione un código</option>
                        <?php foreach ($tiposOperacion as $tipo) { ?>
                            <option value="<?= $tipo['id']; ?>">
                                <?= htmlspecialchars($tipo['codigo'] . " - " . $tipo['concepto']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nota</label>
                    <input type="text" class="form-control" name="nota">
                </div>

                <div class="mb-3">
                    <label class="form-label">Abono</label>
                    <input type="number" step="0.01" class="form-control" name="abono">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo</label>
                    <input type="number" step="0.01" class="form-control" name="cargo">
                </div>

                <div class="mb-3">
                    <label class="form-label">Origen</label>
                    <select class="form-select" name="origen" required>
                        <option value="">Seleccione un origen</option>
                        <option value="CUENTA">Cuenta</option>
                        <option value="EFECTIVO">Efectivo</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>

                </form>
        </div>


    </div>

</div>

<?php include('../../templates/pie.php'); ?>