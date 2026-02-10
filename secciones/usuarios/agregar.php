<?php

include('../../bd.php');

if ($_POST) {

    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    if (!empty($usuario) && !empty($contrasena) && !empty($correo) && !empty($rol)) {

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $consulta = $conexionBD->prepare("INSERT INTO usuarios 
            (usuario, contrasena, correo, rol) 
            VALUES (:usuario, :contrasena, :correo, :rol)");

        $consulta->bindParam(':usuario', $usuario);
        $consulta->bindParam(':contrasena', $contrasena_hash);
        $consulta->bindParam(':correo', $correo);
        $consulta->bindParam(':rol', $rol);
        $consulta->execute();

        header("Location:index.php");
        exit();
    }
}

include('../../templates/cabecera.php'); ?>

<br />
<div class="card">
    <div class="card-header">Datos del Usuario</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">Usuario</label>
                <input
                    type="text"
                    class="form-control"
                    name="usuario"
                    id="usuario"
                    aria-describedby="helpId"
                    placeholder="Usuario" />
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input
                    type="password"
                    class="form-control"
                    name="contrasena"
                    id="contrasena"
                    placeholder="Contraseña" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Correo</label>
                <input
                    type="email"
                    class="form-control"
                    name="correo"
                    id="correo"
                    aria-describedby="helpId"
                    placeholder="Correo" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Rol</label>
                <select class="form-control" name="rol">
                    <option value="admin">Admin</option>
                    <option value="dueno">Dueño</option>
                    <option value="cliente">Cliente</option>
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