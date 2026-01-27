<?php

include('../../bd.php');

if ($_POST) {
$txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$rol = isset($_POST['rol']) ? $_POST['rol'] : '';

$consulta = $conexionBD->prepare("INSERT INTO usuarios (id, usuario, contrasena, correo, rol) 
                  VALUES (NULL, :usuario, :contrasena, :correo, :rol)");
            $consulta->bindParam(':usuario', $usuario);
            $consulta->bindParam(':contrasena', $contrasena);
            $consulta->bindParam(':correo', $correo);
            $consulta->bindParam(':rol', $rol);
            $consulta->execute();
             header("Location:index.php");  


}

include('../../templates/cabecera.php'); ?>

<br/>
<div class="card">
    <div class="card-header">Datos del Usuario</div>
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
                <label for="" class="form-label">Contraseña</label>
                <input
                    type="text"
                    class="form-control"
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
                    name="rol"
                    id="rol"
                   
                    aria-describedby="helpId"
                    placeholder="Rol" />
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