<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once('../../app/services/LocalService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'locales', 'editar');

$service = new LocalService($conexionBD);

$txtID = $_GET['txtID'] ?? null;

if (!$txtID || !is_numeric($txtID)) {
    header("Location: index.php");
    exit();
}

$local = $service->obtenerPorId($txtID);

if (!$local) {
    header("Location: index.php");
    exit();
}

$listaPropiedades = $service->obtenerPropiedades();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Locales</div>
        <div class="card-body">

            <form action="actualizar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $local['id_local']; ?>">

                <div class="mb-3">
                    <label class="form-label">Propiedad</label>
                    <select name="id_propiedad" class="form-control" required>
                        <?php foreach ($listaPropiedades as $prop) { ?>
                            <option value="<?= $prop['id_propiedad']; ?>"
                                <?= ($prop['id_propiedad'] == $local['id_propiedad']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($prop['codigo']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" name="codigo"
                        value="<?= htmlspecialchars($local['codigo']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Medidas</label>
                    <input type="text" class="form-control" name="medidas"
                        value="<?= htmlspecialchars($local['medidas']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <input type="text" class="form-control" name="descripcion"
                        value="<?= htmlspecialchars($local['descripcion']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Estacionamiento</label>
                    <input type="text" class="form-control" name="estacionamiento"
                        value="<?= htmlspecialchars($local['estacionamiento']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="Disponible" <?= ($local['estatus'] == 'Disponible') ? 'selected' : ''; ?>>
                            Disponible
                        </option>
                        <option value="Ocupado" <?= ($local['estatus'] == 'Ocupado') ? 'selected' : ''; ?>>
                            Ocupado
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Modificar</button>
                <a href="index.php" class="btn btn-primary">Cancelar</a>

            </form>

        </div>


    </div>

</div>

<?php include('../../templates/pie.php'); ?>