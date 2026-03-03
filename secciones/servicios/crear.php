<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

verificarPermiso($conexionBD, $_SESSION['id_rol'], 'servicios', 'crear');

$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Nuevo Servicio</div>
        <div class="card-body">

            <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'error'): ?>
                <div class="alert alert-warning">
                    <?= htmlspecialchars($_GET['detalle'] ?? 'Todos los campos son obligatorios'); ?>
                </div>
            <?php endif; ?>

            <form action="guardar.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <option value="">-- Selecciona un local --</option>
                        <?php foreach ($listaLocales as $local): ?>
                            <option value="<?= $local['id_local']; ?>">
                                <?= htmlspecialchars($local['codigo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- CFE -->
                <div class="mb-3">
                    <label class="form-label">CFE Activo</label>
                    <select name="cfe" id="cfe" class="form-control" onchange="toggleContrato('cfe')">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="mb-3" id="grupo_contrato_cfe">
                    <label class="form-label">Contrato CFE</label>
                    <input type="text" name="contrato_cfe" id="contrato_cfe" class="form-control">
                </div>

                <!-- Agua -->
                <div class="mb-3">
                    <label class="form-label">Agua Activo</label>
                    <select name="agua" id="agua" class="form-control" onchange="toggleContrato('agua')">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="mb-3" id="grupo_contrato_agua">
                    <label class="form-label">Contrato Agua</label>
                    <input type="text" name="contrato_agua" id="contrato_agua" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>
    </div>

</div>

<script src="../../assets/js/servicios.js"></script>

<?php include('../../templates/pie.php'); ?>