<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM restricciones WHERE id_restriccion=:id_restriccion");
    $consulta->bindParam(':id_restriccion', $txtID);
    $consulta->execute();

    $restri = $consulta->fetch(PDO::FETCH_LAZY);
    $id_restriccion = $restri['id_restriccion'];
    $id_local = $restri['id_local'];
    $restricciones = $restri['restriccion'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $restricciones = isset($_POST['restricciones']) ? $_POST['restricciones'] : '';

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE restricciones
        SET id_restriccion = :id_restriccion,
        restricciones = :restricciones
        WHERE id_local =: id_local,");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':restricciones', $restricciones);
    $consulta->bindParam(':id_restricciones', $txtID);

    $consulta->execute();
    header("Location:index.php");
}

$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php'); ?>


<div class="card">
    <div class="card-header">Restricciones</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $txtID; ?>"
                    name="txtID"
                    id="txtID"
                    aria-describedby="helpId"
                    placeholder="ID" />
            </div>

            <div class="mb-3">
                <label class="form-label">Local</label>
                <select name="id_local" class="form-control" required>
                    <?php foreach ($listaLocales as $local) { ?>
                        <option value="<?php echo $local['id_local']; ?>"
                            <?php echo ($local['id_local'] == $id_local) ? 'selected' : ''; ?>>
                            <?php echo $local['codigo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Restricciones</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $restricciones; ?>"
                    name="restricciones"
                    id="restricciones"
                    aria-describedby="helpId"
                    placeholder="Restricciones" />
            </div>

            <button type="submit" name="accion" value="agregar" class="btn btn-success">Modificar</button>
            <a
                name=""
                id=""
                class="btn btn-primary"
                href="index.php"
                role="button">Cancelar</a>


        </form>

    </div>

    <div class="card-footer text-muted">


    </div>

</div>


<?php include('../../templates/pie.php'); ?>