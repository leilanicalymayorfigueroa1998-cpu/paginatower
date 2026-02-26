<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'crear');

// Cargar locales disponibles

$consultaLocales = $conexionBD->prepare("
    SELECT id_local, codigo 
    FROM locales 
    ORDER BY codigo ASC
");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

$consultaArrendatarios = $conexionBD->prepare("
    SELECT id_arrendatario, nombre 
    FROM arrendatarios 
    ORDER BY nombre ASC
");
$consultaArrendatarios->execute();
$listaArrendatarios = $consultaArrendatarios->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Nuevo Contrato</div>
        <div class="card-body">

            <form action="guardar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <!-- LOCAL -->
                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <option value="">-- Selecciona un local --</option>
                        <?php foreach ($listaLocales as $local): ?>
                            <option value="<?= htmlspecialchars($local['id_local']) ?>">
                                <?= htmlspecialchars($local['codigo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ARRENDATARIO -->
                <div class="mb-3">
                    <label class="form-label">Arrendatario</label>
                    <select name="id_arrendatario" class="form-control" required>
                        <option value="">-- Selecciona un arrendatario --</option>
                        <?php foreach ($listaArrendatarios as $arrendatario): ?>
                            <option value="<?= htmlspecialchars($arrendatario['id_arrendatario']) ?>">
                                <?= htmlspecialchars($arrendatario['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RENTA -->
                <div class="mb-3">
                    <label class="form-label">Renta</label>
                    <input type="number" step="0.01" class="form-control" name="renta" required>
                </div>

                <!-- DEPÓSITO -->
                <div class="mb-3">
                    <label class="form-label">Depósito</label>
                    <input type="number" step="0.01" class="form-control" name="deposito">
                </div>

                <!-- ADICIONAL -->
                <div class="mb-3">
                    <label class="form-label">Adicional</label>
                    <input type="number" step="0.01" class="form-control" name="adicional">
                </div>


                <!-- FECHA INICIO -->
                <div class="mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" required>
                </div>

                <!-- FECHA FIN -->
                <div class="mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date"
                        class="form-control"
                        name="fecha_fin"
                        id="fecha_fin"
                        readonly>
                </div>

                <!-- ESTATUS -->
                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="">-- Selecciona estatus --</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Activa">Activa</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Finalizada">Finalizada</option>
                    </select>
                </div>

                <!-- TIPO CONTRATO -->
                <div class="mb-3">
                    <label class="form-label">Duración</label>
                    <select name="duracion" id="duracion" class="form-control" required>
                        <option value="">-- Selecciona duración --</option>
                        <option value="6">6 meses</option>
                        <option value="12">12 meses</option>
                        <option value="indefinido">Indefinido</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a class="btn btn-primary" href="index.php">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>