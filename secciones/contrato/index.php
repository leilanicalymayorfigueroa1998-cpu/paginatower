<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php'); // Conexión a la base de datos

// ELIMINAR RENTA

if (isset($_GET['txtID'])) {  // Si viene un ID por la URL (ej: index.php?txtID=5)

    $txtID = $_GET['txtID'];    // Guardamos el ID que viene por GET

    // Preparamos consulta para eliminar la renta
    $consulta = $conexionBD->prepare("DELETE FROM contratos WHERE id_contrato = :id_contrato");

    $consulta->bindParam(':id_contrato', $txtID);   // Vinculamos el parámetro
    $consulta->execute();   // Ejecutamos la consulta
    header("Location:index.php");   // Redirigimos nuevamente al listado
    exit;
}

// Consulta que obtiene rentas junto con datos del local y cliente
$consulta = $conexionBD->prepare("SELECT
        r.id_contrato,
        l.codigo AS local,
        c.nombre AS cliente,
        r.renta,
        r.deposito,
        r.adicional,
        r.fecha_inicio,
        r.fecha_fin,
        r.estatus,

        IFNULL(SUM(
            CASE 
                WHEN p.estatus IN ('Pendiente','Vencido') 
                THEN p.monto 
                ELSE 0 
            END
        ),0) AS deuda_total

    FROM contratos r

    INNER JOIN locales l ON l.id_local = r.id_local
    INNER JOIN clientes c ON c.id_cliente = r.id_cliente
    LEFT JOIN pagos p ON p.id_contrato = r.id_contrato

    GROUP BY r.id_contrato
    ORDER BY r.id_contrato DESC
");
$consulta->execute(); // Ejecutamos la consulta
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC); // Guardamos todos los resultados en un arreglo

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

                <table class="table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Local</th>
                            <th>Arrendatario</th>
                            <th>Renta</th>
                            <th>Depósito</th>
                            <th>Adicional</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estatus</th>
                            <th>Deuda</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($listaRentas as $value) { ?>
                            <tr>

                                <!-- Mostramos datos básicos -->
                                <td><?php echo $value['id_contrato']; ?></td>
                                <td><?php echo $value['local']; ?></td>
                                <td><?php echo $value['cliente']; ?></td>
                                <td><?php echo $value['renta']; ?></td>
                                <td><?php echo $value['deposito']; ?></td>
                                <td><?php echo $value['adicional']; ?></td>
                                <td><?php echo $value['fecha_inicio']; ?></td>
                                <td><?php echo $value['fecha_fin']; ?></td>

                                <!-- COLUMNA ESTATUS DINÁMICO -->
                                <td>
                                    <?php

                                    $hoy = date('Y-m-d');

                                    switch ($value['estatus']) {

                                        case 'Finalizada':
                                            echo '<span class="badge bg-secondary">Finalizada</span>';
                                            break;

                                        case 'Cancelada':
                                            echo '<span class="badge bg-dark">Cancelada</span>';
                                            break;

                                        case 'Pendiente':
                                            echo '<span class="badge bg-warning text-dark">Pendiente</span>';
                                            break;

                                        case 'Activa':
                                            if ($value['fecha_fin'] < $hoy) {
                                                echo '<span class="badge bg-danger">Vencida</span>';
                                            } elseif ($value['fecha_fin'] <= date('Y-m-d', strtotime('+5 days'))) {
                                                echo '<span class="badge bg-warning text-dark">Por vencer</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Activa</span>';
                                            }
                                            break;
                                    }


                                    ?>
                                </td>

                                <td>
                                    <?php if ($value['deuda_total'] > 0) { ?>
                                        <span class="badge bg-danger">
                                            $<?php echo number_format($value['deuda_total'], 2); ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">$0.00</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <!-- Botón editar -->
                                    <a class="btn btn-primary"
                                        href="editar.php?txtID=<?php echo $value['id_contrato']; ?>">
                                        Editar
                                    </a>

                                    <!-- Botón borrar -->
                                    <a class="btn btn-danger"
                                        href="index.php?txtID=<?php echo $value['id_contrato']; ?>">
                                        Borrar
                                    </a>
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