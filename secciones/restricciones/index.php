<?php

include('../../bd.php');


if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM restricciones WHERE id_restriccion = :id_restriccion");
    $consulta->bindParam(':id_restriccion', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT r.id_restriccion,
           l.codigo AS local,
           r.restricciones
    FROM restricciones r
    INNER JOIN locales l ON r.id_local = l.id_local");

$consulta->execute();
$lista_restricciones = $consulta->fetchAll(PDO::FETCH_ASSOC);
include('../../templates/cabecera.php');

?>

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
                        <th>Restriccciones</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($lista_restricciones as $key => $value) { ?>

                        <tr class="">
                            <td scope="row"><?php echo $value['id_restriccion'] ?> </td>
                            <td><?php echo $value['local'] ?></td>
                            <td><?php echo $value['restricciones'] ?></td>

                            <td>
                                <a
                                    name=""
                                    id=""
                                    class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id_restriccion']; ?>"
                                    role="button">Editar</a>

                                <a
                                    name=""
                                    id=""
                                    class="btn btn-danger"
                                    href="index.php?txtID=<?php echo $value['id_restriccion']; ?>"
                                    role="button">Borrar</a>

                            </td>


                        </tr>

                    <?php   } ?>

                </tbody>
            </table>
        </div>

    </div>
    <div class="card-footer text-muted"></div>
</div>

<style>
    .table th,
    .table td {
        white-space: nowrap;
        padding: 6px 10px;
        font-size: 13px;
        text-align: center;
        vertical-align: middle !important;
    }

    /* Que los botones no se encimen */
    .btn {
        padding: 4px 10px;
        font-size: 13px;
    }

    /* Para que la tabla no se vea aplastada */
    .table-responsive {
        max-height: 500px;
        overflow: auto;
    }
</style>

<?php include('../../templates/pie.php'); ?>