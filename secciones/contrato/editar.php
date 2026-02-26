<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');
include('../../includes/permisos.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'contratos', 'editar');

if (!isset($_GET['txtID']) || !is_numeric($_GET['txtID'])) {
    header("Location: index.php");
    exit();
}

$txtID = (int) $_GET['txtID'];

// Buscar contrato
$consulta = $conexionBD->prepare("SELECT * FROM contratos WHERE id_contrato = :id");
$consulta->execute([':id' => $txtID]);
$contrato = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$contrato) {
    die("Contrato no encontrado.");
}

// Cargar listas
$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo 
FROM locales 
ORDER BY codigo ASC");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

// Cargar Arrendatarios
$consultaArrendatarios = $conexionBD->prepare("SELECT id_arrendatario, nombre 
FROM arrendatarios 
ORDER BY nombre ASC");
$consultaArrendatarios->execute();
$listaArrendatarios = $consultaArrendatarios->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card">
        <div class="card-header">Editar Contrato</div>
        <div class="card-body">

            <form action="actualizar.php" method="post">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= htmlspecialchars($contrato['id_contrato']) ?>">

                <!-- LOCAL -->
                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <?php foreach ($listaLocales as $local): ?>
                            <option value="<?= htmlspecialchars($local['id_local']) ?>"
                                <?= ($local['id_local'] == $contrato['id_local']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($local['codigo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ARRENDATARIO -->
                <div class="mb-3">
                    <label class="form-label">Arrendatario</label>
                    <select name="id_arrendatario" class="form-control" required>
                        <?php foreach ($listaArrendatarios as $arr): ?>
                            <option value="<?= htmlspecialchars($arr['id_arrendatario']) ?>"
                                <?= ($arr['id_arrendatario'] == $contrato['id_arrendatario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($arr['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- RENTA -->
                <div class="mb-3">
                    <label class="form-label">Renta</label>
                    <input type="number" step="0.01" name="renta"
                        value="<?= htmlspecialchars($contrato['renta']) ?>"
                        class="form-control" required>
                </div>

                <!-- DEPOSITO -->
                <div class="mb-3">
                    <label class="form-label">Depósito</label>
                    <input type="number" step="0.01" name="deposito"
                        value="<?= htmlspecialchars($contrato['deposito']) ?>"
                        class="form-control">
                </div>

                <!-- ADICIONAL -->
                <div class="mb-3">
                    <label class="form-label">Adicional</label>
                    <input type="number" step="0.01" name="adicional"
                        value="<?= htmlspecialchars($contrato['adicional']) ?>"
                        class="form-control">
                </div>

                <!-- FECHAS -->
                <div class="mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio"
                        value="<?= htmlspecialchars($contrato['fecha_inicio']) ?>"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date"
                        class="form-control"
                        name="fecha_fin"
                        id="fecha_fin"
                        value="<?= htmlspecialchars($contrato['fecha_fin']) ?>"
                        readonly>
                </div>

                <!-- ESTATUS -->
                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <?php
                        $estados = ['Pendiente', 'Activa', 'Cancelada', 'Finalizada'];
                        foreach ($estados as $estado):
                        ?>
                            <option value="<?= $estado ?>"
                                <?= ($estado == $contrato['estatus']) ? 'selected' : '' ?>>
                                <?= $estado ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Duración</label>
                    <select name="duracion" id="duracion" class="form-control" required>

                        <option value="6"
                            <?= ($contrato['duracion'] == '6') ? 'selected' : '' ?>>
                            6 meses
                        </option>

                        <option value="12"
                            <?= ($contrato['duracion'] == '12') ? 'selected' : '' ?>>
                            12 meses
                        </option>

                        <option value="indefinido"
                            <?= ($contrato['duracion'] == 'indefinido') ? 'selected' : '' ?>>
                            Indefinido
                        </option>

                    </select>
                </div>

                <button type="submit" class="btn btn-success">Modificar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>