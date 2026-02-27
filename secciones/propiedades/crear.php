<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once(__DIR__ . '/../../services/PropiedadService.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'propiedades', 'crear');

$service = new PropiedadService($conexionBD);

// 🔹 Obtener tipos SIEMPRE (no solo en POST)
$tipos = $service->obtenerTipos();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    try {

        $service->crear([
            'codigo'    => trim($_POST['codigo'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'latitud'   => $_POST['latitud'] !== '' ? floatval($_POST['latitud']) : null,
            'longitud'  => $_POST['longitud'] !== '' ? floatval($_POST['longitud']) : null,
            'id_tipo'   => $_POST['id_tipo'] ?? null,
            'id_dueno'  => $_POST['id_dueno'] ?? null
        ]);

        header("Location:index.php");
        exit();
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

$consultaDuenos = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
$consultaDuenos->execute();
$listaDuenos = $consultaDuenos->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Propiedades</div>
        <div class="card-body">

            <form action="guardar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" name="codigo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dueño</label>
                    <select name="id_dueno" class="form-control" required>
                        <option value="">Seleccione dueño</option>
                        <?php foreach ($listaDuenos as $dueno): ?>
                            <option value="<?= $dueno['id_dueno']; ?>">
                                <?= htmlspecialchars($dueno['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="id_tipo" class="form-control" required>
                        <option value="">Seleccione tipo</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?= $tipo['id_tipo']; ?>">
                                <?= htmlspecialchars($tipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Latitud</label>
                    <input type="number" step="0.0000001" class="form-control" name="latitud">
                </div>

                <div class="mb-3">
                    <label class="form-label">Longitud</label>
                    <input type="number" step="0.0000001" class="form-control" name="longitud">
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>

            </form>
        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>