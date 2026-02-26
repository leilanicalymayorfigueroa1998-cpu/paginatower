<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

$mensaje_error = '';

if ($_POST) {

    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $id_rol = isset($_POST['id_rol']) ? $_POST['id_rol'] : '';
    $id_cliente = null;
    $id_dueno   = null;

    // 🔹 1️⃣ Validar campos obligatorios
    if (empty($usuario) || empty($contrasena) || empty($correo) || empty($id_rol)) {

        $mensaje_error = "Todos los campos obligatorios deben completarse.";
    } else {

        // 🔹 2️⃣ Validación según rol
        if ($id_rol == 3 && empty($_POST['id_cliente'])) {
            $mensaje_error = "Debe seleccionar un arrendatario.";
        }

        if ($id_rol == 2 && empty($_POST['id_dueno'])) {
            $mensaje_error = "Debe seleccionar un dueño.";
        }

        // 🔹 3️⃣ Validar usuario duplicado
        if (empty($mensaje_error)) {
            $verificar = $conexionBD->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
            $verificar->bindParam(':usuario', $usuario);
            $verificar->execute();

            if ($verificar->fetchColumn() > 0) {
                $mensaje_error = "El usuario ya existe.";
            }
        }

        // 🔹 4️⃣ Si no hay errores → insertar
        if (empty($mensaje_error)) {

            if ($id_rol == 3) {
                $id_cliente = $_POST['id_cliente'];
            }

            if ($id_rol == 2) {
                $id_dueno = $_POST['id_dueno'];
            }

            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $consulta = $conexionBD->prepare("INSERT INTO usuarios 
            (usuario, contrasena, correo, id_rol, id_cliente, id_dueno) 
            VALUES (:usuario, :contrasena, :correo, :id_rol, :id_cliente, :id_dueno)");

            $consulta->bindParam(':usuario', $usuario);
            $consulta->bindParam(':contrasena', $contrasena_hash);
            $consulta->bindParam(':correo', $correo);
            $consulta->bindParam(':id_rol', $id_rol);
            $consulta->bindValue(':id_cliente', $id_cliente);
            $consulta->bindValue(':id_dueno', $id_dueno);
            $consulta->execute();

            header("Location:index.php");
            exit();
        }
    }
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="content">

    <div class="card">
        <div class="card-header">Datos del Usuario</div>
        <div class="card-body">

            <?php if (!empty($mensaje_error)) { ?>
                <div class="alert alert-danger">
                    <?php echo $mensaje_error; ?>
                </div>
            <?php } ?>

            <form action="" method="post">

                <div class="mb-3">
                    <label for="" class="form-label">Usuario</label>
                    <input type="text"
                        class="form-control"
                        name="usuario"
                        value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        name="contrasena" required>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Correo</label>
                    <input type="email"
                        class="form-control"
                        name="correo"
                        value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Rol</label>
                    <select class="form-select" name="id_rol">
                        <?php
                        $roles = $conexionBD->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roles as $rol_item) {
                            echo "<option value='{$rol_item['id_rol']}'>{$rol_item['nombre']}</option>";
                        } ?>
                    </select>
                </div>

                <div class="mb-3" id="grupo_cliente">
                    <label class="form-label">Arrendatario</label>
                    <select name="id_cliente" class="form-select">
                        <option value="">Seleccione Arrendatario</option>
                        <?php
                        $consultaClientes = $conexionBD->prepare("SELECT id_cliente, nombre FROM clientes");
                        $consultaClientes->execute();
                        $clientes = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($clientes as $cliente) {
                            echo "<option value='{$cliente['id_cliente']}'>{$cliente['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3" id="grupo_dueno">
                    <label class="form-label">Dueño</label>
                    <select name="id_dueno" class="form-select">
                        <option value="">Seleccione dueño</option>
                        <?php
                        $consultaDuenos = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
                        $consultaDuenos->execute();
                        $duenos = $consultaDuenos->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($duenos as $dueno) {
                            echo "<option value='{$dueno['id_dueno']}'>{$dueno['nombre']}</option>";
                        }
                        ?>
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

    </div>

</div>

<script src="../../assets/js/usuarios.js"></script>
<?php include('../../templates/pie.php'); ?>