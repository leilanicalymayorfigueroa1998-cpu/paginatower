<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];

    $consulta = $conexionBD->prepare("DELETE FROM rentas WHERE id_renta = :id_renta");
    $consulta->bindParam(':id_renta', $txtID);
    $consulta->execute();
    header("Location:index.php");
    exit;
}

$consulta = $conexionBD->prepare("SELECT
        r.id_renta,
        l.codigo AS local,
        c.nombre AS cliente,
        r.renta,
        r.deposito,
        r.adicional,
        r.fecha_inicio,
        r.fecha_fin,
        r.metodo,
        r.estatus
    FROM rentas r
    INNER JOIN locales l 
        ON l.id_local = r.id_local
    INNER JOIN clientes c 
        ON c.id_cliente = r.id_cliente
    ORDER BY r.id_renta DESC");
$consulta->execute();
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC);

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

            <table class="table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Local</th>
                        <th>Cliente</th>
                        <th>Renta</th>
                        <th>Depósito</th>
                        <th>Adicional</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Método</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($listaRentas as $value) { ?>
                        <tr>
                            <td><?php echo $value['id_renta']; ?></td>
                            <td><?php echo $value['local']; ?></td>
                            <td><?php echo $value['cliente']; ?></td>
                            <td><?php echo $value['renta']; ?></td>
                            <td><?php echo $value['deposito']; ?></td>
                            <td><?php echo $value['adicional']; ?></td>
                            <td><?php echo $value['fecha_inicio']; ?></td>
                            <td><?php echo $value['fecha_fin']; ?></td>
                            <td><?php echo $value['metodo']; ?></td>
                            <td><?php echo $value['estatus']; ?></td>
                            <td>
                                <a class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id_renta']; ?>">
                                    Editar
                                </a>
                                <a class="btn btn-danger"
                                    href="index.php?txtID=<?php echo $value['id_renta']; ?>">
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

<?php include('../../templates/pie.php'); ?>