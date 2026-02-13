<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("DELETE FROM usuarios WHERE id = :id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();
    header("Location:index.php");
}

$consulta = $conexionBD->prepare("SELECT 
        u.id,
        u.usuario,
        u.correo,
        r.nombre AS rol,
        c.nombre AS cliente,
        d.nombre AS dueno
    FROM usuarios u
    LEFT JOIN roles r ON u.id_rol = r.id_rol
    LEFT JOIN clientes c ON u.id_cliente = c.id_cliente
    LEFT JOIN duenos d ON u.id_dueno = d.id_dueno");

$consulta->execute();
$listaUsuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

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
                        <th>Nombre del Usuario</th>
                        <th>Correo</th>
                        <th>Roles</th>
                        <th>Arrendatario</th>
                        <th>Dueño</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($listaUsuarios as $key => $value) { ?>

                        <tr class="">
                            <td scope="row"><?php echo $value['id'] ?> </td>
                            <td scope="row"><?php echo $value['usuario'] ?></td>
                            <td scope="row"><?php echo $value['correo'] ?></td>
                            <td scope="row"><?php echo $value['rol'] ?></td>
                            <td scope="row"><?php echo $value['cliente'] ?></td>
                            <td scope="row"><?php echo $value['dueno'] ?></td>
                            <td>
                                <a
                                    name=""
                                    id=""
                                    class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id']; ?>"
                                    role="button">Editar</a>

                                <a class="btn btn-danger"
                                    href="index.php?txtID=<?php echo $value['id']; ?>"
                                    onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">
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