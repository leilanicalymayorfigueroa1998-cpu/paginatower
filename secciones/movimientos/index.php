<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM movimientos_financieros WHERE id_movimiento = :id_movimiento");
    $consulta->bindParam(':id_movimiento', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT * FROM movimientos_financieros");
$consulta->execute();
$listaPropiedades = $consulta->fetchAll(PDO::FETCH_ASSOC);

// LISTADO CON JOIN
$consulta = $conexionBD->prepare(" SELECT
        m.id_movimiento,
        m.fecha,
        p.codigo AS propiedad,
        t.codigo AS operacion,
        m.nota,
        m.abono,
        m.cargo,
        m.origen
    FROM movimientos_financieros m
    INNER JOIN propiedades p
        ON p.id_propiedad = m.id_propiedad
    INNER JOIN tipo_operacion t
        ON t.id = m.id_tipo_operacion
    ORDER BY m.fecha DESC");
$consulta->execute();
$listaMovimientos = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
                class="table-responsive">
                <table
                    class="table">
                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Propiedad</th>
                            <th>Operacion</th>
                            <th>Nota</th>
                            <th>Abono</th>
                            <th>Cargo</th>
                            <th>Origen</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php foreach ($listaMovimientos as $mov) { ?>
                            <tr>
                                <td><?= $mov['id_movimiento'] ?></td>
                                <td><?= $mov['fecha'] ?></td>
                                <td><?= $mov['propiedad'] ?></td>
                                <td><?= $mov['operacion'] ?></td>
                                <td><?= $mov['nota'] ?></td>
                                <td><?= number_format($mov['abono'], 2) ?></td>
                                <td><?= number_format($mov['cargo'], 2) ?></td>
                                <td><?= $mov['origen'] ?></td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="editar.php?txtID=<?= $mov['id_movimiento'] ?>">Editar</a>
                                    <a class="btn btn-danger btn-sm" href="index.php?txtID=<?= $mov['id_movimiento'] ?>">Borrar</a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>