<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM usuarios WHERE id=:id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();

    $user = $consulta->fetch(PDO::FETCH_LAZY);
    $usuario = $user['usuario'];
    $contrasena = $user['contrasena'];
    $correo = $user['correo'];
    $rol = $user['rol'];
}

if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE usuarios 
        SET usuario =:usuario, 
                contrasena = :contrasena,
                correo = :correo,
                rol = :rol
                WHERE id=:id");

    $consulta->bindParam(':usuario', $usuario);
    $consulta->bindParam(':contrasena', $contrasena);
    $consulta->bindParam(':correo', $correo);
    $consulta->bindParam(':rol', $rol);
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();
    header("Location:index.php");
}


include('../../templates/cabecera.php'); ?>

<br />
<div class="card">
    <div class="card-header">Datos del Usuario</div>
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
                <label for="" class="form-label">Usuario</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $usuario ?>"
                    name="usuario"
                    id="usuario"
                    aria-describedby="helpId"
                    placeholder="Usuario" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Contraseña</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $contrasena ?>"
                    name="contrasena"
                    id="contrasena"
                    aria-describedby="helpId"
                    placeholder="Contraseña" />
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

            <div class="mb-3">
                <label for="" class="form-label">Rol</label>
                <input
                    type="text"
                    class="form-control"
                    value="<?php echo $rol ?>"
                    name="rol"
                    id="rol"
                    aria-describedby="helpId"
                    placeholder="Rol" />
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