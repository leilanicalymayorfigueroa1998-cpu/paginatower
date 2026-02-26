<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');


if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM propiedades WHERE id_propiedad = :id_propiedad");
    $consulta->bindParam(':id_propiedad', $txtID);
    $consulta->execute();
    header("Location:index.php?mensaje=eliminado");
    exit();
}

$consulta = $conexionBD->prepare("SELECT * FROM propiedades");
$consulta->execute();
$listaPropiedades = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<?php if (isset($_GET['mensaje'])) { ?>

    <?php if ($_GET['mensaje'] == "agregado") { ?>
        <div class="alert alert-success">
            Propiedad agregada correctamente ✅
        </div>
    <?php } ?>

    <?php if ($_GET['mensaje'] == "editado") { ?>
        <div class="alert alert-primary">
            Propiedad correctamente ✏️
        </div>
    <?php } ?>

    <?php if ($_GET['mensaje'] == "eliminado") { ?>
        <div class="alert alert-danger">
            Propiedad eliminada correctamente 🗑️
        </div>
    <?php } ?>

<?php } ?>

<div class="content">

    <div class="card">
        <div class="card-header">

            <a
                name=""
                id=""
                class="btn btn-success"
                href="agregar.php"
                role="button">Agregar</a>
        </div>

        <div class="card-body">
            <div
                class="table-responsive">
                <table
                    class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Codigo</th>
                            <th>Direccion</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Tipo</th>
                            <th style="width: 120px;">Acciones</th>


                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaPropiedades as $key => $value) { ?>

                            <tr class="">
                                <td scope="row"><?php echo $value['id_propiedad'] ?> </td>
                                <td><?php echo $value['codigo'] ?></td>
                                <td><?php echo $value['direccion'] ?></td>
                                <td><?php echo $value['latitud'] ?> </td>
                                <td><?php echo $value['longitud'] ?></td>
                                <td><?php echo $value['tipo'] ?></td>

                                <td>

                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-primary"
                                        href="editar.php?txtID=<?php echo $value['id_propiedad']; ?>"
                                        role="button">Editar</a>

                                    <a class="btn btn-danger"
                                        href="index.php?txtID=<?php echo $value['id_propiedad']; ?>"
                                        onclick="return confirm('¿Estás segura de eliminar esta propiedad?');">
                                        Borrar
                                    </a>

                                </td>


                            </tr>

                        <?php   } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>