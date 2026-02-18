<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php'); // CONEXIÓN A BASE DE DATOS

// CARGAR DATOS PARA EDITAR

if (isset($_GET['txtID'])) {  // Si viene un ID por la URL (ej: editar.php?txtID=3)

    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : ''; // Guardamos el ID recibido

    // Buscamos la renta correspondiente en la base de datos
    $consulta = $conexionBD->prepare("SELECT * FROM contratos WHERE id_contrato = :id_contrato");
    $consulta->bindParam(':id_contrato', $txtID);       // Vinculamos el parámetro
    $consulta->execute();                            // Ejecutamos la consulta
    $con = $consulta->fetch(PDO::FETCH_LAZY);        // Guardamos el resultado

    // Asignamos cada campo a una variable
    $id_local = $con['id_local'];
    $id_cliente = $con['id_cliente'];
    $renta = $con['renta'];
    $deposito = $con['deposito'];
    $adicional = $con['adicional'];
    $fecha_inicio = $con['fecha_inicio'];
    $fecha_fin = $con['fecha_fin'];
    $estatus = $con['estatus'];
}

// ACTUALIZAR DATOS (CUANDO SE ENVÍA EL FORMULARIO)

if ($_POST) {

    // Recibimos datos del formulario
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $renta = isset($_POST['renta']) ? $_POST['renta'] : '';
    $deposito = isset($_POST['deposito']) ? $_POST['deposito'] : '';
    $adicional = isset($_POST['adicional']) ? $_POST['adicional'] : '';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';

    // Preparamos consulta para actualizar
    $consulta = $conexionBD->prepare("UPDATE contratos SET
            id_local = :id_local,
            id_cliente = :id_cliente,
            renta = :renta,
            deposito = :deposito,
            adicional = :adicional,
            fecha_inicio = :fecha_inicio,
            fecha_fin = :fecha_fin,
            estatus = :estatus
        WHERE id_contrato = :id_contrato ");

    // Vinculamos todos los parámetros
    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->bindParam(':renta', $renta);
    $consulta->bindParam(':deposito', $deposito);
    $consulta->bindParam(':adicional', $adicional);
    $consulta->bindParam(':fecha_inicio', $fecha_inicio);
    $consulta->bindParam(':fecha_fin', $fecha_fin);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->bindParam(':id_contrato', $txtID);
    $consulta->execute();   // Ejecutamos actualización

    // 🔹 Si el contrato se cancela o finaliza, cancelar pagos pendientes
    if ($estatus == 'Cancelada' || $estatus == 'Finalizada') {

        $consultaPagos = $conexionBD->prepare("
        UPDATE pagos 
        SET estatus = 'Cancelado'
        WHERE id_contrato = :id_contrato
        AND estatus = 'Pendiente'
    ");

        $consultaPagos->bindParam(':id_contrato', $txtID);
        $consultaPagos->execute();
    }

    header("Location:index.php");
    exit();
}

// CARGAR LISTA DE LOCALES
$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

// CARGAR LISTA DE CLIENTES
$consultaClientes = $conexionBD->prepare("SELECT id_cliente, nombre FROM clientes");
$consultaClientes->execute();
$listaClientes = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="main-content">

    <div class="card">
        <div class="card-header">Contratos</div>
        <div class="card-body">

            <form action="" method="post">

                <div class="mb-3">
                    <label for="" class="form-label">ID</label>
                    <input
                        type="hidden"
                        class="form-control"
                        name="txtID"
                        id="txtID"
                        value="<?php echo $txtID ?>"
                        aria-describedby="helpId"
                        placeholder="ID" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <?php foreach ($listaLocales as $local) { ?>
                            <option value="<?php echo $local['id_local']; ?>"
                                <?php echo ($local['id_local'] == $id_local) ? 'selected' : ''; ?>>
                                <?php echo $local['codigo']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Arrendatario</label>
                    <select name="id_cliente" class="form-control" required>
                        <?php foreach ($listaClientes as $cliente) { ?>
                            <option value="<?php echo $cliente['id_cliente']; ?>"
                                <?php echo ($cliente['id_cliente'] == $id_cliente) ? 'selected' : ''; ?>>
                                <?php echo $cliente['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Renta </label>
                    <input
                        type="text"
                        class="form-control"
                        name="renta"
                        id="renta"
                        value="<?php echo $renta ?>"
                        aria-describedby="helpId"
                        placeholder="Renta" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Deposito</label>
                    <input
                        type="text"
                        class="form-control"
                        name="deposito"
                        id="deposito"
                        value="<?php echo $deposito ?>"
                        aria-describedby="helpId"
                        placeholder="Deposito" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Adicional</label>
                    <input
                        type="text"
                        class="form-control"
                        name="adicional"
                        id="adicional"
                        value="<?php echo $adicional ?>"
                        aria-describedby="helpId"
                        placeholder="Adicional" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Fecha Inicio</label>
                    <input
                        type="date"
                        class="form-control"
                        name="fecha_inicio"
                        id="fecha_inicio"
                        value="<?php echo $fecha_inicio ?>"
                        aria-describedby="helpId"
                        placeholder="Fecha Inicio" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Fecha Fin</label>
                    <input
                        type="date"
                        class="form-control"
                        name="fecha_fin"
                        id="fecha_fin"
                        value="<?php echo $fecha_fin ?>"
                        aria-describedby="helpId"
                        placeholder="Fecha Fin" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="Pendiente" <?php echo ($estatus == 'Pendiente') ? 'selected' : ''; ?>>
                            Pendiente
                        </option>
                        <option value="Activa" <?php echo ($estatus == 'Activa') ? 'selected' : ''; ?>>
                            Activa
                        </option>
                        <option value="Cancelada" <?php echo ($estatus == 'Cancelada') ? 'selected' : ''; ?>>
                            Cancelada
                        </option>
                        <option value="Finalizada" <?php echo ($estatus == 'Finalizada') ? 'selected' : ''; ?>>
                            Finalizada
                        </option>

                    </select>
                </div>


                <button type="submit" name="accion" value="agregar" class="btn btn-success">Modificar</button>
                <a
                    name=""
                    id=""
                    class="btn btn-primary"
                    href="index.php"
                    role="button">Cancelar</a>


            </form>

        </div>


    </div>

</div>

<?php include('../../templates/pie.php'); ?>