<?php

include('../../bd.php');

if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_propiedad = isset($_POST['id_propiedad']) ? $_POST['id_propiedad'] : '';
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $medidas = isset($_POST['medidas']) ? $_POST['medidas'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $estacionamiento = isset($_POST['estacionamiento']) ? $_POST['estacionamiento'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO locales (`id_local`,`id_propiedad`,`codigo`,
                                                  `medidas`,`descripcion`, `estacionamiento`, `estatus`)
 VALUES (NULL, :id_propiedad, :codigo, :medidas, :descripcion, :estacionamiento, :estatus");

    $consulta->bindParam(':id_propiedad', $id_propiedad);
    $consulta->bindParam(':codigo', $codigo);
    $consulta->bindParam(':medidas', $medidas);
    $consulta->bindParam(':descripcion', $descripcion);
    $consulta->bindParam(':estacionamiento', $estacionamiento);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();

    header("Location:index.php");
}

include('../../templates/cabecera.php');
?>


<div class="card">
    <div class="card-header">Locales</div>
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
                <label for="" class="form-label">Propiedad</label>
                <input
                    type="text"
                    class="form-control"
                    name="propiedad"
                    id="propiedad"
                    aria-describedby="helpId"
                    placeholder="propiedad" />
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
                <label for="" class="form-label">Medidas</label>
                <input
                    type="text"
                    class="form-control"
                    name="medidas"
                    id="medidas"
                    aria-describedby="helpId"
                    placeholder="Medidas" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Descripcion</label>
                <input
                    type="text"
                    class="form-control"
                    name="descripcion"
                    id="descripcion"
                    aria-describedby="helpId"
                    placeholder="Descripcion" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Estacionamiento</label>
                <input
                    type="text"
                    class="form-control"
                    name="estacionamiento"
                    id="estacionamiento"
                    aria-describedby="helpId"
                    placeholder="Estacionamiento" />
            </div>


            <div class="mb-3">
                <label for="" class="form-label">Estatus</label>
                <input
                    type="text"
                    class="form-control"
                    name="estatus"
                    id="estatus"
                    aria-describedby="helpId"
                    placeholder="Estatus" />
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