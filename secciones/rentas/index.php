<?php

include('../../bd.php'); // Conexión a la base de datos

// ELIMINAR RENTA

if (isset($_GET['txtID'])) {  // Si viene un ID por la URL (ej: index.php?txtID=5)

    $txtID = $_GET['txtID'];    // Guardamos el ID que viene por GET

    // Preparamos consulta para eliminar la renta
    $consulta = $conexionBD->prepare("DELETE FROM rentas WHERE id_renta = :id_renta");

    $consulta->bindParam(':id_renta', $txtID);   // Vinculamos el parámetro
    $consulta->execute();   // Ejecutamos la consulta
    header("Location:index.php");   // Redirigimos nuevamente al listado
    exit;
}

// Consulta que obtiene rentas junto con datos del local y cliente
$consulta = $conexionBD->prepare(" SELECT
        r.id_renta,                -- ID de la renta
        l.codigo AS local,         -- Código del local
        c.nombre AS cliente,       -- Nombre del cliente
        r.renta,                   -- Monto mensual
        r.deposito,                -- Depósito
        r.adicional,               -- Cargos adicionales
        r.fecha_inicio,            -- Fecha de inicio del contrato
        r.fecha_fin,               -- Fecha final del contrato
        r.metodo,                  -- Método de pago
        r.estatus                  -- Estatus guardado en BD
    FROM rentas r
    INNER JOIN locales l           -- Relacionamos la tabla locales
        ON l.id_local = r.id_local
    INNER JOIN clientes c          -- Relacionamos la tabla clientes
        ON c.id_cliente = r.id_cliente
    ORDER BY r.id_renta DESC       -- Ordenamos del más reciente al más antiguo
");

$consulta->execute(); // Ejecutamos la consulta
$listaRentas = $consulta->fetchAll(PDO::FETCH_ASSOC); // Guardamos todos los resultados en un arreglo

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
                        <th>Arrendatario</th>
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

                            <!-- Mostramos datos básicos -->
                            <td><?php echo $value['id_renta']; ?></td>
                            <td><?php echo $value['local']; ?></td>
                            <td><?php echo $value['cliente']; ?></td>
                            <td><?php echo $value['renta']; ?></td>
                            <td><?php echo $value['deposito']; ?></td>
                            <td><?php echo $value['adicional']; ?></td>
                            <td><?php echo $value['fecha_inicio']; ?></td>
                            <td><?php echo $value['fecha_fin']; ?></td>
                            <td><?php echo $value['metodo']; ?></td>

                            <!-- COLUMNA ESTATUS DINÁMICO -->
                            <td>
                                <?php

                                $hoy = date('Y-m-d'); // Obtenemos la fecha actual

                                if ($value['estatus'] == 'Finalizada') {  // Si el estatus fue marcado manualmente como Finalizada

                                    echo '<span class="badge bg-secondary">Finalizada</span>';
                                } elseif ($value['fecha_fin'] < $hoy) {        // Si la fecha final ya pasó → está vencida

                                    echo '<span class="badge bg-danger">Vencida</span>';
                                } elseif ($value['fecha_fin'] <= date('Y-m-d', strtotime('+5 days'))) { // Si faltan 5 días o menos → está por vencer

                                    echo '<span class="badge bg-warning text-dark">Por vencer</span>';
                                } else {  // En cualquier otro caso → está activa

                                    echo '<span class="badge bg-success">Activa</span>';
                                }

                                // CÁLCULO DE DÍAS RESTANTES

                                $diferencia = floor(   // Calculamos la diferencia en días
                                    (strtotime($value['fecha_fin']) - strtotime($hoy)) / 86400
                                );

                                if ($diferencia > 0) {  // Si aún no vence, mostramos cuántos días faltan
                                    echo "<br><small class='text-muted'>
                  Faltan $diferencia días
                  </small>";
                                }

                                ?>
                            </td>

                            <td>
                                <!-- Botón editar -->
                                <a class="btn btn-primary"
                                    href="editar.php?txtID=<?php echo $value['id_renta']; ?>">
                                    Editar
                                </a>

                                <!-- Botón borrar -->
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