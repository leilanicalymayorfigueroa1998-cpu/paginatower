<?php
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM clientes WHERE id_cliente = :id_cliente");
    $consulta->bindParam(':id_cliente', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT * FROM clientes");
$consulta->execute();
$listaClientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Correo</th>
                        <th>Aval</th>
                        <th>Correo Aval</th>
                        <th>Direccion</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($listaClientes as $key => $value) { ?>
                        <tr class="">

                            <td scope="row"><?php echo $value['id_cliente'] ?> </td>
                            <td><?php echo $value['nombre'] ?></td>
                            <td><?php echo $value['telefono'] ?></td>
                            <td><?php echo $value['correo'] ?></td>
                            <td><?php echo $value['aval'] ?></td>
                            <td><?php echo $value['correoaval'] ?></td>
                            <td><?php echo $value['direccion'] ?></td>
                            <td><?php echo $value['ciudad'] ?></td>

                            <td>

                                <a
                                    name=""
                                    id=""
                                    class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id_cliente']; ?>"
                                    role="button">Editar</a>

                                <a
                                    name=""
                                    id=""
                                    class="btn btn-danger"
                                    href="index.php?txtID=<?php echo $value['id_cliente']; ?>"
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