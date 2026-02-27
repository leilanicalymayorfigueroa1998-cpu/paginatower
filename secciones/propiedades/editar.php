<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'editar');

$id = $_GET['txtID'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location:index.php");
    exit();
}

$service = new PropiedadService($conexionBD);

$propiedad = $service->obtenerPorId($id);

if (!$propiedad) {
    header("Location:index.php");
    exit();
}

$tipos = $service->obtenerTipos();
$duenos = $service->obtenerDuenos();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Propiedades</div>
        <div class="card-body">

            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $propiedad['id_propiedad']; ?>">

                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text"
                        class="form-control"
                        name="codigo"
                        value="<?= htmlspecialchars($propiedad['codigo']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dueño</label>
                    <select name="id_dueno" class="form-select" required>
                        <option value="">Seleccione dueño</option>
                        <?php foreach ($duenos as $dueno): ?>
                            <option value="<?= $dueno['id_dueno']; ?>"
                                <?= ($dueno['id_dueno'] == $propiedad['id_dueno']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($dueno['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="id_tipo" class="form-select" required>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?= $tipo['id_tipo']; ?>"
                                <?= ($tipo['id_tipo'] == $propiedad['id_tipo']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($tipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text"
                        class="form-control"
                        name="direccion"
                        value="<?= htmlspecialchars($propiedad['direccion']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Latitud</label>
                    <input type="number"
                        step="0.0000001"
                        class="form-control"
                        name="latitud"
                        value="<?= htmlspecialchars($propiedad['latitud']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Longitud</label>
                    <input type="number"
                        step="0.0000001"
                        class="form-control"
                        name="longitud"
                        value="<?= htmlspecialchars($propiedad['longitud']); ?>">
                </div>

                <button type="submit" class="btn btn-success">Modificar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>