<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
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
    </div>
</div>

<?php include('../../templates/pie.php'); ?>