<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM pagos WHERE id_pago = :id_pago");
    $consulta->bindParam(':id_pago', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT * FROM pagos");
$consulta->execute();
$listaPagos = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
                        <th>Renta</th>
                        <th>Fecha pago</th>
                        <th>Monto</th>
                        <th>Estatus</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($listaPagos as $key => $value) { ?>
                        <tr class="">
                            <td scope="row"><?php echo $value['id_pago'] ?> </td>
                            <td><?php echo $value['id_renta'] ?></td>
                            <td><?php echo $value['fecha_pago'] ?></td>
                            <td><?php echo $value['monto'] ?></td>
                            <td><?php echo $value['estatus'] ?></td>

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

    <div class="card-footer text-muted"></div>

</div>

<?php include('../../templates/pie.php'); ?>