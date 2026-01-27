<?php
include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $aval = isset($_POST['aval']) ? $_POST['aval'] : '';
    $correoaval = isset($_POST['correoaval']) ? $_POST['correoaval'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO clientes (id_cliente, nombre, 
        telefono, correo, aval, correoaval, direccion, ciudad) 
        VALUES (NULL, :nombre, :telefono, :correo, :aval, 
        :correoaval, :direccion, :ciudad)");

    $consulta->bindParam(':nombre', $nombre);
    $consulta->bindParam(':telefono', $telefono);
    $consulta->bindParam(':correo', $correo);
    $consulta->bindParam(':aval', $aval);
    $consulta->bindParam(':correoaval', $correoaval);
    $consulta->bindParam(':direccion', $direccion);
    $consulta->bindParam(':ciudad', $ciudad);
    $consulta->execute();

    header("Location:index.php");
}

include('../../templates/cabecera.php');

?>

        <div class="card">
            <div class="card-header">Clientes</div>
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
                        <label for="" class="form-label">Nombre</label>
                        <input
                            type="text"
                            class="form-control"
                            name="nombre"
                            id="nombre"
                            aria-describedby="helpId"
                            placeholder="Nombre" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Telefono</label>
                        <input
                            type="text"
                            class="form-control"
                            name="telefono"
                            id="telefono"
                            aria-describedby="helpId"
                            placeholder="Telefono" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Correo</label>
                        <input
                            type="text"
                            class="form-control"
                            name="correo"
                            id="correo"
                            aria-describedby="helpId"
                            placeholder="Correo" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Aval</label>
                        <input
                            type="text"
                            class="form-control"
                            name="aval"
                            id="aval"
                            aria-describedby="helpId"
                            placeholder="Aval" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Correo Aval</label>
                        <input
                            type="text"
                            class="form-control"
                            name="correoaval"
                            id="correoaval"
                            aria-describedby="helpId"
                            placeholder="correoaval" />
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
                        <label for="" class="form-label">Ciudad</label>
                        <input
                            type="text"
                            class="form-control"
                            name="ciudad"
                            id="ciudad"
                            aria-describedby="helpId"
                            placeholder="Ciudad" />
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