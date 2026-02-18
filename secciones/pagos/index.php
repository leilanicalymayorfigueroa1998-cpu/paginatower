<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM pagos WHERE id_pago = :id_pago");
    $consulta->bindParam(':id_pago', $txtID);
    $consulta->execute();
    header("Location:index.php");
    exit();
}

$consulta = $conexionBD->prepare("SELECT p.id_pago,
       l.codigo AS contrato,
       p.fecha_pago,
       p.monto,
       p.metodo_pago,
       p.estatus
FROM pagos p
INNER JOIN contratos r ON p.id_contrato = r.id_contrato
INNER JOIN locales l ON r.id_local = l.id_local
ORDER BY p.id_pago DESC");

$consulta->execute();
$listaPagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
                            <th>Renta</th>
                            <th>Fecha pago</th>
                            <th>Monto</th>
                            <th>Metodo Pago</th>
                            <th>Estatus</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($listaPagos as $key => $value) { ?>
                            <tr class="">
                                <td scope="row"><?php echo $value['id_pago'] ?> </td>
                                <td><?php echo $value['contrato'] ?></td>
                                <td><?php echo $value['fecha_pago'] ?></td>
                                <td><?php echo $value['monto'] ?></td>
                                <td><?php echo $value['metodo_pago'] ?></td>
                                <td>
                                    <?php if ($value['estatus'] == 'Pagado') { ?>

                                        <span class="badge bg-success">Pagado</span>

                                    <?php } elseif ($value['estatus'] == 'Pendiente') { ?>

                                        <span class="badge bg-warning text-dark">Pendiente</span>

                                    <?php } elseif ($value['estatus'] == 'Vencido') { ?>

                                        <span class="badge bg-danger">Vencido</span>

                                    <?php } else { ?>

                                        <span class="badge bg-secondary">Cancelado</span>

                                    <?php } ?>
                                </td>



                                <td>
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-primary"
                                        href="editar.php?txtID=<?php echo $value['id_pago']; ?>"
                                        role="button">Editar</a>

                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-danger"
                                        href="index.php?txtID=<?php echo $value['id_pago']; ?>"
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