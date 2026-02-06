<?php

include('../../bd.php');


if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM propiedad_dueno WHERE id = :id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT * FROM propiedad_dueno");
$consulta->execute();
$listaPropiedadDueño = $consulta->fetchAll(PDO::FETCH_ASSOC);

// LISTADO CON JOIN
$consulta = $conexionBD->prepare("
    SELECT
        pd.id,
        p.codigo AS propiedad,
        d.nombre AS dueno
    FROM propiedad_dueno pd
    INNER JOIN propiedades p
        ON p.id_propiedad = pd.id_propiedad
    INNER JOIN duenos d
        ON d.id_dueno = pd.id_dueno
    ORDER BY pd.id DESC
");
$consulta->execute();
$listaPropiedadDueno = $consulta->fetchAll(PDO::FETCH_ASSOC);


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
                        <th>Propiedad</th>
                        <th>Dueño</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($listaPropiedadDueno as $row) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['propiedad'] ?></td>
                            <td><?= $row['dueno'] ?></td>
                            <td>
                                <a class="btn btn-primary btn-sm"
                                    href="editar.php?txtID=<?= $row['id'] ?>">
                                    Editar
                                </a>
                                <a class="btn btn-danger btn-sm"
                                    href="index.php?txtID=<?= $row['id'] ?>">
                                    Borrar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>

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