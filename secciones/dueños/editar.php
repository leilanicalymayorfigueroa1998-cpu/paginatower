<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM duenos WHERE id_dueno=:id_dueno");
    $consulta->bindParam(':id_dueno', $txtID);
    $consulta->execute();

    $dueño = $consulta->fetch(PDO::FETCH_LAZY);
    $nombre = $dueño['nombre'];
    $telefono = $dueño['telefono'];
    $correo = $dueño['correo'];
}

if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE duenos 
        SET nombre =:nombre, 
                telefono = :telefono,
                correo = :correo
                WHERE id_dueno=:id_dueno");

    $consulta->bindParam(':nombre', $nombre);
    $consulta->bindParam(':telefono', $telefono);
    $consulta->bindParam(':correo', $correo);
    $consulta->bindParam(':id_dueno', $txtID);

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
                            value="<?php echo $txtID; ?>"
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
                            value="<?php echo $nombre ?>"
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
                            value="<?php echo $telefono ?>"
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
                            value="<?php echo $correo ?>"
                            name="correo"
                            id="correo"
                            aria-describedby="helpId"
                            placeholder="Correo" />
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