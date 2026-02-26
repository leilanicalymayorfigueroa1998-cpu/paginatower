<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM usuarios WHERE id=:id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();

    $user = $consulta->fetch(PDO::FETCH_ASSOC);
    $usuario = $user['usuario'];
    $correo = $user['correo'];
    $id_rol = $user['id_rol'];
    $id_cliente = $user['id_cliente'];
    $id_dueno = $user['id_dueno'];
}

if ($_POST) {

    $txtID  = $_POST['txtID'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $id_rol = $_POST['id_rol'] ?? '';

    $id_cliente = null;
    $id_dueno = null;

    if ($id_rol == 3) { // Arrendatario
        if (empty($_POST['id_cliente'])) {
            die("Debe seleccionar un arrendatario.");
        }
        $id_cliente = $_POST['id_cliente'];
    }

    if ($id_rol == 2) { // Dueño
        if (empty($_POST['id_dueno'])) {
            die("Debe seleccionar un dueño.");
        }
        $id_dueno = $_POST['id_dueno'];
    }

    // 🔐 Si escribió nueva contraseña
    if (!empty($contrasena)) {

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $consulta = $conexionBD->prepare("UPDATE usuarios 
            SET usuario =:usuario, 
                contrasena = :contrasena,
                correo = :correo,
                id_rol = :id_rol,
                id_cliente = :id_cliente,
                id_dueno = :id_dueno
            WHERE id=:id");

        $consulta->bindParam(':contrasena', $contrasena_hash);
    } else {

        $consulta = $conexionBD->prepare("UPDATE usuarios 
            SET usuario =:usuario, 
                correo = :correo,
                id_rol = :id_rol,
                id_cliente = :id_cliente,
                id_dueno = :id_dueno
            WHERE id=:id");
    }

    $consulta->bindParam(':usuario', $usuario);
    $consulta->bindParam(':correo', $correo);
    $consulta->bindParam(':id_rol', $id_rol);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->bindParam(':id_dueno', $id_dueno);
    $consulta->bindParam(':id', $txtID);

    $consulta->execute();
    header("Location:index.php");
    exit();
}


include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="content">

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
                        value="<?php echo htmlspecialchars($usuario); ?>"
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
                        vvalue="<?php echo htmlspecialchars($correo); ?>"
                        name="correo"
                        id="correo"
                        aria-describedby="helpId"
                        placeholder="Correo" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="id_rol" class="form-select">

                        <?php
                        $roles = $conexionBD->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roles as $rol_item) {
                            $selected = ($rol_item['id_rol'] == $id_rol) ? "selected" : "";
                            echo "<option value='{$rol_item['id_rol']}' $selected>
                    {$rol_item['nombre']}
                  </option>";
                        }
                        ?>

                    </select>
                </div>


                <div class="mb-3" id="grupo_cliente">
                    <label class="form-label">Seleccionar Arrendatario</label>
                    <select name="id_cliente" class="form-select">
                        <option value="">Seleccione arrendatario</option>
                        <?php
                        $consultaClientes = $conexionBD->prepare("SELECT id_cliente, nombre FROM clientes");
                        $consultaClientes->execute();
                        $clientes = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($clientes as $cliente) {
                            $selected = ($cliente['id_cliente'] == $id_cliente) ? "selected" : "";
                            echo "<option value='{$cliente['id_cliente']}' $selected>{$cliente['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3" id="grupo_dueno">
                    <label class="form-label">Seleccionar Dueño</label>
                    <select name="id_dueno" class="form-select">
                        <option value="">Seleccione dueño</option>
                        <?php
                        $consultaDuenos = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
                        $consultaDuenos->execute();
                        $duenos = $consultaDuenos->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($duenos as $dueno) {
                            $selected = ($dueno['id_dueno'] == $id_dueno) ? "selected" : "";
                            echo "<option value='{$dueno['id_dueno']}' $selected>{$dueno['nombre']}</option>";
                        }
                        ?>
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


    </div>

</div>

<script src="../../assets/js/usuarios.js"></script>

<?php include('../../templates/pie.php'); ?>