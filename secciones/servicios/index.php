<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM servicios WHERE id_servicio = :id_servicio");
    $consulta->bindParam(':id_servicio', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT s.id_servicio,
           l.codigo AS local,
           s.cfe,
           s.agua
    FROM servicios s
    INNER JOIN locales l ON s.id_local = l.id_local");
$consulta->execute();
$lista_servicios = $consulta->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="main-content">

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
                class="table-responsive-sm">
                <table
                    class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Local</th>
                            <th>CFE</th>
                            <th>Agua</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($lista_servicios as $key => $value) { ?>

                            <tr class="">
                                <td scope="row"><?php echo $value['id_servicio'] ?> </td>
                                <td><?php echo $value['local'] ?></td>
                                <td><?php echo $value['cfe'] ?></td>
                                <td><?php echo $value['agua'] ?></td>
                                <td>
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-primary"
                                        href="editar.php?txtID=<?php echo $value['id_servicio']; ?>"
                                        role="button">Editar</a>

                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-danger"
                                        href="index.php?txtID=<?php echo $value['id_servicio']; ?>"
                                        role="button">Borrar</a>

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