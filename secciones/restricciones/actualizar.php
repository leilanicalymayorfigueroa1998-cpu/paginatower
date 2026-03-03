<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/RestriccionService.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'restricciones', 'editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Acceso inválido (CSRF)");
}

unset($_SESSION['csrf_token']);

$id = filter_input(INPUT_POST, 'txtID', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location:index.php");
    exit();
}

$id_local = $_POST['id_local'] ?? null;
$restriccion = trim($_POST['restriccion'] ?? '');

if (empty($id_local) || empty($restriccion)) {
    header("Location: editar.php?txtID=$id&mensaje=error");
    exit();
}

$service = new RestriccionService($conexionBD);
$service->actualizarRestriccion($id, $id_local, $restriccion);

header("Location:index.php?mensaje=editado");
exit();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card">
        <div class="card-header">Editar Restricción</div>
        <div class="card-body">
            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= $registro['id_restriccion']; ?>">

                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <?php foreach ($listaLocales as $local): ?>
                            <option value="<?= $local['id_local']; ?>"
                                <?= ($local['id_local'] == $registro['id_local']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($local['codigo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Restricción</label>
                    <input type="text"
                        class="form-control"
                        name="restriccion"
                        value="<?= htmlspecialchars($registro['restriccion']); ?>"
                        required>
                </div>

                <button type="submit" class="btn btn-success">Actualizar</button>
                <a href="index.php" class="btn btn-primary">Cancelar</a>

            </form>
        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>