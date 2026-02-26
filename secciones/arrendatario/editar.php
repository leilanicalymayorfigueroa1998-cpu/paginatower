<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

// 🔐 Verificar rol
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'editar');

// 🔎 Validar ID
$id = intval($_GET['txtID'] ?? $_POST['txtID'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

/* ==============================
   🔎 CARGAR DATOS (GET)
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $consulta = $conexionBD->prepare("
        SELECT * FROM arrendatarios 
        WHERE id_arrendatario = :id
    ");

    $consulta->execute([':id' => $id]);
    $tenant = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$tenant) {
        header("Location: index.php");
        exit();
    }

    extract($tenant);
}

/* ==============================
   🔄 ACTUALIZAR (POST)
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        die("Acceso inválido (CSRF)");
    }

    unset($_SESSION['csrf_token']);

    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $aval = trim($_POST['aval'] ?? '');
    $correoaval = trim($_POST['correoaval'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');

    // ✅ Validaciones
    if (empty($nombre) || empty($telefono)) {
        die("Nombre y teléfono son obligatorios.");
    }

    if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Correo inválido.");
    }

    if (!empty($correoaval) && !filter_var($correoaval, FILTER_VALIDATE_EMAIL)) {
        die("Correo del aval inválido.");
    }

    $consulta = $conexionBD->prepare("
        UPDATE arrendatarios SET
            nombre = :nombre,
            telefono = :telefono,
            correo = :correo,
            aval = :aval,
            correoaval = :correoaval,
            direccion = :direccion,
            ciudad = :ciudad
        WHERE id_arrendatario = :id
    ");

    $consulta->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':correo' => $correo ?: null,
        ':aval' => $aval ?: null,
        ':correoaval' => $correoaval ?: null,
        ':direccion' => $direccion ?: null,
        ':ciudad' => $ciudad ?: null,
        ':id' => $id
    ]);

    header("Location: index.php");
    exit();
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Editar Arrendatario</div>
        <div class="card-body">

            <form method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">
                <input type="hidden" name="txtID" value="<?= (int)$id ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control"
                        name="nombre"
                        value="<?= htmlspecialchars($nombre ?? '') ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control"
                        name="telefono"
                        value="<?= htmlspecialchars($telefono ?? '') ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control"
                        name="correo"
                        value="<?= htmlspecialchars($correo ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Aval</label>
                    <input type="text" class="form-control"
                        name="aval"
                        value="<?= htmlspecialchars($aval ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo Aval</label>
                    <input type="email" class="form-control"
                        name="correoaval"
                        value="<?= htmlspecialchars($correoaval ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control"
                        name="direccion"
                        value="<?= htmlspecialchars($direccion ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control"
                        name="ciudad"
                        value="<?= htmlspecialchars($ciudad ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-success">Modificar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>

            </form>
        </div>

    </div>

</div>

<?php include('../../templates/pie.php'); ?>