<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once '../../services/DuenoService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'duenos', 'editar');

$service = new DuenoService($conexionBD);

$id = intval($_GET['txtID'] ?? 0);

if ($id <= 0) {
    header("Location:index.php");
    exit();
}

// ==============================
// 🔎 CARGAR DATOS (GET)
// ==============================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $dueno = $service->obtenerPorId($id);

    if (!$dueno) {
        header("Location:index.php");
        exit();
    }

    $nombre = $dueno['nombre'];
    $telefono = $dueno['telefono'];
    $correo = $dueno['correo'];
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Editar Dueño</div>
        <div class="card-body">

            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= (int)$id ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($nombre ?? '') ?>"
                        name="nombre"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input
                        type="tel"
                        class="form-control"
                        value="<?= htmlspecialchars($telefono ?? '') ?>"
                        name="telefono"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input
                        type="email"
                        class="form-control"
                        value="<?= htmlspecialchars($correo ?? '') ?>"
                        name="correo">
                </div>

                <button type="submit" class="btn btn-success">Modificar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>

            </form>

        </div>

    </div>

</div>


<?php include('../../templates/pie.php'); ?>