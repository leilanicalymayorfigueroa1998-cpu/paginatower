<?php

include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : '';
    $longitud = isset($_POST['longitud']) ? $_POST['longitud'] : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO propiedades (id_propiedad, codigo, direccion, latitud, longitud, tipo)
                    VALUES (NULL, :codigo, :direccion, :latitud, :longitud, :tipo)");

    $consulta->bindParam(':codigo', $codigo);
    $consulta->bindParam(':direccion', $direccion);
    $consulta->bindParam(':latitud', $latitud);
    $consulta->bindParam(':longitud', $longitud);
    $consulta->bindParam(':tipo', $tipo);
    $consulta->execute();

    header("Location:index.php?mensaje=editado");
}

$consultaDuenos = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
$consultaDuenos->execute();
$listaDuenos = $consultaDuenos->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');

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
                    aria-describedby="helpId"
                    placeholder="Longitud" />
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="">-- Selecciona tipo de propiedad --</option>
                    <option value="Local">Local</option>
                    <option value="Casa">Casa</option>
                    <option value="Departamento">Departamento</option>
                    <option value="Oficina">Oficina</option>
                </select>
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>