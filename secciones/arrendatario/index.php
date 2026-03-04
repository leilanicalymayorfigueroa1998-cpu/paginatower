<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'ver');

// 🔹 Optimizar permisos (evitar múltiples consultas)
$puedeCrear    = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'crear');
$puedeEditar   = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'editar');
$puedeEliminar = tienePermiso($conexionBD, $idRol, 'arrendatarios', 'eliminar');

// 🔹 Consulta optimizada (columnas específicas)
$consulta = $conexionBD->prepare("
    SELECT 
        id_arrendatario,
        nombre,
        telefono,
        correo,
        aval,
        correoaval,
        direccion,
        ciudad
    FROM arrendatarios
    ORDER BY id_arrendatario DESC
");

$consulta->execute();
$listaArrendatarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Arrendatario</h5>

            <?php if ($puedeCrear): ?>
                <a class="btn btn-success" href="crear.php"> + Nuevo Arrendatario</a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Aval</th>
                            <th>Correo Aval</th>
                            <th>Dirección</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaArrendatarios as $value): ?>
                            <tr>
                                <td><?= htmlspecialchars($value['nombre']) ?></td>
                                <td><?= htmlspecialchars($value['telefono']) ?></td>
                                <td><?= htmlspecialchars($value['correo']) ?></td>
                                <td><?= htmlspecialchars($value['aval']) ?></td>
                                <td><?= htmlspecialchars($value['correoaval']) ?></td>
                                <td><?= htmlspecialchars($value['direccion']) ?></td>
                                <td><?= htmlspecialchars($value['ciudad']) ?></td>

                                <td>

                                    <?php if ($puedeEditar): ?>
                                        <a class="btn btn-primary btn-sm"
                                            href="editar.php?txtID=<?= htmlspecialchars($value['id_arrendatario']) ?>">
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($puedeEliminar): ?>
                                        <form method="POST" action="eliminar.php" style="display:inline;">
                                            <input type="hidden" name="id_arrendatario"
                                                value="<?= htmlspecialchars($value['id_arrendatario']) ?>">
                                            <input type="hidden" name="csrf_token"
                                                value="<?= generarTokenCSRF(); ?>">
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este arrendatario?');">
                                                Borrar
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </td>

                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($listaArrendatarios)): ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    No hay arrendatarios registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <?php include('../../templates/pie.php'); ?>