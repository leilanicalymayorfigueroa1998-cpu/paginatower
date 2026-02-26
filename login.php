<?php
session_start();
include("bd.php");

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (!empty($usuario) && !empty($contrasena)) {

        $consulta = $conexionBD->prepare(" SELECT u.*, 
                   r.nombre AS nombre_rol,
                   d.nombre AS nombre_dueno,
                   c.nombre AS nombre_arrendatario
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            LEFT JOIN duenos d ON u.id_dueno = d.id_dueno
            LEFT JOIN arrendatarios c ON u.id_arrendatario = c.id_arrendatario
            WHERE u.usuario = :usuario");

        $consulta->execute([':usuario' => $usuario]);

        $usuarioBD = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuarioBD && password_verify($contrasena, $usuarioBD['contrasena'])) {

            // 🔐 Seguridad
            session_regenerate_id(true);

            $nombreCompleto = $usuarioBD['nombre_dueno']
                ?: $usuarioBD['nombre_arrendatario']
                ?: $usuarioBD['usuario'];

            $_SESSION['id'] = $usuarioBD['id'];
            $_SESSION['usuario'] = $usuarioBD['usuario'];
            $_SESSION['nombre_completo'] = $nombreCompleto;
            $_SESSION['id_rol'] = $usuarioBD['id_rol'];
            $_SESSION['rol'] = $usuarioBD['nombre_rol'];

            // 🔥 Cargar permisos en sesión
            $consultaPermisos = $conexionBD->prepare("SELECT m.nombre AS modulo, p.accion
                FROM rol_permiso rp
                INNER JOIN permisos p ON rp.id_permiso = p.id_permiso
                INNER JOIN modulos m ON p.id_modulo = m.id_modulo
                WHERE rp.id_rol = :rol
            ");

            $consultaPermisos->execute([
                ':rol' => $usuarioBD['id_rol']
            ]);

            $_SESSION['permisos'] = $consultaPermisos->fetchAll(PDO::FETCH_ASSOC);

            // 🔐 Si el rol no tiene permisos, bloquear acceso
            if (empty($_SESSION['permisos'])) {
                session_destroy();
                die("Este rol no tiene permisos asignados.");
            }

            header("Location: index.php");
            exit();
        } else {
            $mensaje = "Usuario o contraseña incorrectos";
        }
    } else {
        $mensaje = "Todos los campos son obligatorios";
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4">

            </div>

            <div class="col-md-4">

                <?php if (!empty($mensaje)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <strong> Error: <?php echo htmlspecialchars($mensaje); ?> </strong>
                    </div>
                <?php } ?>

                <form action="#" method="post">

                    <div class="card">
                        <div class="card-header">
                            Login
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <label for="" class="form-label">Usuario</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="usuario"
                                    id="usuario"
                                    aria-describedby="helpId"
                                    placeholder="Escriba su usuario" />
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Contraseña</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    name="contrasena"
                                    id="contrasena"
                                    aria-describedby="helpId"
                                    placeholder="Escriba su contraseña" />
                            </div>

                            <button type="submit" class="btn btn-primary">Iniciar Sesion</button>

                        </div>
                </form>

            </div>
        </div>
    </div>
    </div>

    <header>
        <!-- place navbar here -->
    </header>
    <main></main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>