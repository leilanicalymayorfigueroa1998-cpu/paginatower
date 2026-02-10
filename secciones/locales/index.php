<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM locales WHERE id_local = :id_local");
    $consulta->bindParam(':id_local', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare(" SELECT
        l.id_local,
        p.codigo AS propiedad,
        l.codigo,
        l.medidas,
        l.descripcion,
        l.estacionamiento,
        l.estatus
    FROM locales l
    INNER JOIN propiedades p
        ON p.id_propiedad = l.id_propiedad
    ORDER BY l.id_local DESC");

$consulta->execute();
$listaLocales = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
            class="table-responsive">
            <table
                class="table">
                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Propiedad</th>
                        <th>Codigo</th>
                        <th>Medidas</th>
                        <th>Descripcion</th>
                        <th>Estacionamiento</th>
                        <th>Estatus</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($listaLocales as $key => $value) { ?>

                        <tr>
                            <td><?php echo $value['id_local']; ?></td>
                            <td><?php echo $value['propiedad']; ?></td>
                            <td><?php echo $value['codigo']; ?></td>
                            <td><?php echo $value['medidas']; ?></td>
                            <td><?php echo $value['descripcion']; ?></td>
                            <td><?php echo $value['estacionamiento']; ?></td>
                            <td><?php echo $value['estatus']; ?></td>
                            <td>
                                <a class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id_local']; ?>">
                                    Editar
                                </a>
                                <a class="btn btn-danger"
                                    href="index.php?txtID=<?php echo $value['id_local']; ?>">
                                    Borrar
                                </a>
                            </td>
                        </tr>

                    <?php   } ?>

                </tbody>
            </table>
        </div>

    </div>
    <div class="card-footer text-muted"></div>
</div>

<?php include('../../templates/pie.php'); ?>