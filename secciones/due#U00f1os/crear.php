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

verificarPermiso($conexionBD, $idRol, 'duenos', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $service = new DuenoService($conexionBD);

    try {

        $service->crear([
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo'   => trim($_POST['correo'] ?? '')
        ]);

        header("Location:index.php");
        exit();

    } catch (Exception $e) {
        die($e->getMessage());
    }
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Nuevo Dueño</div>
        <div class="card-body">

       <form action="" method="post" autocomplete="off">

    <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input
            type="text"
            class="form-control"
            name="nombre"
            required
            maxlength="100"
            placeholder="Nombre completo">
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input
            type="tel"
            class="form-control"
            name="telefono"
            required
            maxlength="20"
            placeholder="Ej. 55 1234 5678">
    </div>

    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input
            type="email"
            class="form-control"
            name="correo"
            maxlength="150"
            placeholder="correo@ejemplo.com">
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a class="btn btn-secondary" href="index.php">Cancelar</a>

</form>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>