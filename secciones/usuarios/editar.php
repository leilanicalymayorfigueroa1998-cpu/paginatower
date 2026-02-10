<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM usuarios WHERE id=:id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();

    $user = $consulta->fetch(PDO::FETCH_ASSOC);
    $usuario = $user['usuario'];
    $correo = $user['correo'];
    $rol = $user['rol'];
}

if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    if (!empty($contrasena)) {

        // 🔐 Si escribió nueva contraseña, la encriptamos
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $consulta = $conexionBD->prepare("UPDATE usuarios 
            SET usuario =:usuario, 
                contrasena = :contrasena,
                correo = :correo,
                rol = :rol
            WHERE id=:id");

        $consulta->bindParam(':contrasena', $contrasena_hash);
    } else {

        // ✨ Si NO escribió contraseña, no la tocamos
        $consulta = $conexionBD->prepare("UPDATE usuarios 
            SET usuario =:usuario, 
                correo = :correo,
                rol = :rol
            WHERE id=:id");
    }

    $consulta->bindParam(':usuario', $usuario);
    $consulta->bindParam(':correo', $correo);
    $consulta->bindParam(':rol', $rol);
    $consulta->bindParam(':id', $txtID);

    $consulta->execute();
    header("Location:index.php");
}


include('../../templates/cabecera.php');

?>

<br />
<div class="card">
    <div class="card-header">Datos del Usuario</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="hidden"
                    name="txtID"
                    value="<?php echo $txtID; ?>">
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
                    type="password"
                    class="form-control"
                    name="contrasena"
                    placeholder="Nueva contraseña (opcional)" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Correo</label>
                <input
                    type="email"
                    class="form-control"
                    value="<?php echo $correo ?>"
                    name="correo"
                    id="correo"
                    aria-describedby="helpId"
                    placeholder="Correo" />
            </div>

            <div class="mb-3">
                <select class="form-control" name="rol" id="rol">
                    <option value="admin" <?php if ($rol == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="dueno" <?php if ($rol == 'dueno') echo 'selected'; ?>>Dueño</option>
                    <option value="cliente" <?php if ($rol == 'cliente') echo 'selected'; ?>>Cliente</option>
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