<?php

include('../../bd.php');

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO duenos (id_dueno, nombre, telefono, correo) 
                  VALUES (NULL, :nombre, :telefono, :correo)");
    $consulta->bindParam(':nombre', $nombre);
    $consulta->bindParam(':telefono', $telefono);
    $consulta->bindParam(':correo', $correo);
    $consulta->execute();
    header("Location:index.php");
}

include('../../templates/cabecera.php'); ?>


        <div class="card">
            <div class="card-header">Dueños</div>
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