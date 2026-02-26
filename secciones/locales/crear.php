<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once '../../app/services/LocalService.php';

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'locales', 'crear');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $service = new LocalService($conexionBD);

    try {

        $service->crear([
            'id_propiedad'  => $_POST['id_propiedad'] ?? '',
            'codigo'        => trim($_POST['codigo'] ?? ''),
            'medidas'       => trim($_POST['medidas'] ?? ''),
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
            'estacionamiento' => trim($_POST['estacionamiento'] ?? ''),
            'estatus'       => $_POST['estatus'] ?? ''
        ]);

        header("Location:index.php");
        exit();
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

$service = new LocalService($conexionBD);
$listaPropiedades = $service->obtenerPropiedades();

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card">
        <div class="card-header">Crear Local</div>
        <div class="card-body">

           <form action="guardar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label>Propiedad</label>
                    <select name="id_propiedad" class="form-control" required>
                        <option value="">-- Selecciona una propiedad --</option>
                        <?php foreach ($listaPropiedades as $prop) { ?>
                            <option value="<?= $prop['id_propiedad']; ?>">
                                <?= strtoupper($prop['codigo']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Código Local</label>
                    <input type="text" class="form-control" name="codigo" required>
                </div>

                <div class="mb-3">
                    <label>Medidas</label>
                    <input type="text" class="form-control" name="medidas">
                </div>

                <div class="mb-3">
                    <label>Descripción</label>
                    <input type="text" class="form-control" name="descripcion">
                </div>

                <div class="mb-3">
                    <label>Estacionamiento</label>
                    <input type="text" class="form-control" name="estacionamiento">
                </div>

                <div class="mb-3">
                    <label>Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="">-- Selecciona estatus --</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupado">Ocupado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="index.php" class="btn btn-primary">Cancelar</a>

            </form>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>