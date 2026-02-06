<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM propiedades WHERE id_propiedad=:id_propiedad");
    $consulta->bindParam(':id_propiedad', $txtID);
    $consulta->execute();

    $propiedad = $consulta->fetch(PDO::FETCH_LAZY);
    $codigo = $propiedad['codigo'];
    $direccion = $propiedad['direccion'];
    $latitud = $propiedad['latitud'];
    $longitud = $propiedad['longitud'];
    $tipo = $propiedad['tipo'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : '';
    $longitud = isset($_POST['longitud']) ? $_POST['longitud'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("UPDATE propiedades SET 
                    codigo = :codigo, 
                    direccion = :direccion,
                    latitud = :latitud,
                    longitud = :longitud,
                    tipo = :tipo
                WHERE id_propiedad = :id_propiedad");

    $consulta->bindParam(':codigo', $codigo);
    $consulta->bindParam(':direccion', $direccion);
    $consulta->bindParam(':latitud', $latitud);
    $consulta->bindParam(':longitud', $longitud);
    $consulta->bindParam(':tipo', $tipo);
    $consulta->bindParam(':id_propiedad', $txtID);;
    $consulta->execute();
    header("Location:index.php");
}

include('../../templates/cabecera.php');

$consultaDuenos = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
$consultaDuenos->execute();
$listaDuenos = $consultaDuenos->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="card">
    <div class="card-header">Propiedades</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
                    class="form-control"
                    name="txtID"
                    id="txtID"
                    value="<?php echo $txtID ?>"
                    aria-describedby="helpId"
                    placeholder="ID" />
            </div>


            <div class="mb-3">
                <label for="" class="form-label">Codigo Propiedad</label>
                <input
                    type="text"
                    class="form-control"
                    name="codigo"
                    id="codigo"
                    value="<?php echo $codigo ?>"
                    aria-describedby="helpId"
                    placeholder="Codigo" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Direccion</label>
                <input
                    type="text"
                    class="form-control"
                    name="direccion"
                    id="direccion"
                    value="<?php echo $direccion ?>"
                    aria-describedby="helpId"
                    placeholder="Direccion" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Latitud</label>
                <input
                    type="text"
                    class="form-control"
                    name="latitud"
                    id="latitud"
                    value="<?php echo $latitud ?>"
                    aria-describedby="helpId"
                    placeholder="Latitud" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Longitud</label>
                <input
                    type="text"
                    class="form-control"
                    name="longitud"
                    id="longitud"
                    value="<?php echo $longitud ?>"
                    aria-describedby="helpId"
                    placeholder="Longitud" />
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select" required>
                    <option value="Local" <?php echo ($tipo == 'Local') ? 'selected' : ''; ?>>Local</option>
                    <option value="Casa" <?php echo ($tipo == 'Casa') ? 'selected' : ''; ?>>Casa</option>
                    <option value="Departamento" <?php echo ($tipo == 'Departamento') ? 'selected' : ''; ?>>Departamento</option>
                    <option value="Oficina" <?php echo ($tipo == 'Oficina') ? 'selected' : ''; ?>>Oficina</option>
                </select>
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