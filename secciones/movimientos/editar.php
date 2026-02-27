<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/MovimientoService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'movimientos', 'editar');

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

//Tabla Tipo de Operaciones 
$consultaTipos = $conexionBD->prepare("SELECT id, codigo, concepto 
FROM tipo_operacion 
ORDER BY codigo");
$consultaTipos->execute();
$tiposOperacion = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

//Tabla Propiedades
$consultaPropiedades = $conexionBD->prepare("SELECT id_propiedad, codigo 
FROM propiedades
ORDER BY codigo");
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

            <form action="actualizar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $movimiento['id_movimiento']; ?>">

                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date"
                        class="form-control"
                        name="fecha"
                        value="<?= htmlspecialchars($movimiento['fecha']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Propiedad</label>
                    <select name="id_propiedad" class="form-control" required>
                        <?php foreach ($propiedades as $p): ?>
                            <option value="<?= $p['id_propiedad']; ?>"
                                <?= ($p['id_propiedad'] == $movimiento['id_propiedad']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['codigo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de operación</label>
                    <select name="id_tipo_operacion" class="form-select" required>
                        <?php foreach ($tiposOperacion as $tipo): ?>
                            <option value="<?= $tipo['id']; ?>"
                                <?= ($tipo['id'] == $movimiento['id_tipo_operacion']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($tipo['codigo'] . " - " . $tipo['concepto']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nota</label>
                    <input type="text"
                        class="form-control"
                        name="nota"
                        value="<?= htmlspecialchars($movimiento['nota']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Abono</label>
                    <input type="number"
                        step="0.01"
                        class="form-control"
                        name="abono"
                        value="<?= htmlspecialchars($movimiento['abono']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo</label>
                    <input type="number"
                        step="0.01"
                        class="form-control"
                        name="cargo"
                        value="<?= htmlspecialchars($movimiento['cargo']); ?>">
                </div>

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

                <button type="submit" class="btn btn-success">Modificar</button>
                <a class="btn btn-primary" href="index.php">Cancelar</a>

            </form>

        </div>


    </div>

</div>

<?php include('../../templates/pie.php'); ?>