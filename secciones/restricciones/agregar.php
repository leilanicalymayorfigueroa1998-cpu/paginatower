<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $restriccion = isset($_POST['restriccion']) ? $_POST['restriccion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO restricciones (id_restriccion, id_local, restriccion) 
                  VALUES (NULL, :id_local, :restriccion)");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':restriccion', $restriccion);
    $consulta->execute();
    header("Location:index.php");
}

$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="main-content">

    <div class="card">
        <div class="card-header">Restricciones</div>
        <div class="card-body">

            <form action="" method="post">

                <div class="mb-3">
                    <label for="" class="form-label">ID</label>
                    <input
                        type="text"
                        class="form-control"
                        name="txtID"
                        id="txtID"
                        aria-describedby="helpId"
                        placeholder="ID" />
                </div>

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

                    <small class="form-text text-muted">
                        Si el local no existe, primero agrégalo en el módulo de Locales.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Restriccion</label>
                    <input
                        type="text"
                        class="form-control"
                        name="restriccion"
                        id="restriccion"
                        aria-describedby="helpId"
                        placeholder="Restriccion" />
                </div>

                <button type="submit" name="accion" value="agregar" class="btn btn-success">Agregar</button>
                <a
                    name=""
                    id=""
                    class="btn btn-primary"
                    href="index.php"
                    role="button">Cancelar</a>


            </form>

        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>