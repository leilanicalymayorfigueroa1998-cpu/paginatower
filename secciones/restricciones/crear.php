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

// 🔐 Permiso para crear
verificarPermiso($conexionBD, $idRol, 'restricciones', 'crear');

$service = new RestriccionService($conexionBD);

// 🔹 Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $id_local     = $_POST['id_local'] ?? null;
    $restriccion  = trim($_POST['restriccion'] ?? '');

    if (empty($id_local) || empty($restriccion)) {
        header("Location: crear.php?mensaje=error");
        exit();
    }

    $service->crearRestriccion($id_local, $restriccion);

    header("Location: index.php?mensaje=creado");
    exit();
}

// 🔹 Obtener locales
$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card">
        <div class="card-header">Nueva Restricción</div>
        <div class="card-body">

            <div class="alert alert-warning alert-dismissible fade show">
                Todos los campos son obligatorios ⚠️
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <form method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <option value="">-- Selecciona un local --</option>
                        <?php foreach ($listaLocales as $local) { ?>
                            <option value="<?php echo $local['id_local']; ?>">
                                <?php echo $local['codigo']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Restricción</label>
                    <input type="text" class="form-control" name="restriccion" required>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="index.php" class="btn btn-primary">Cancelar</a>

            </form>
        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>