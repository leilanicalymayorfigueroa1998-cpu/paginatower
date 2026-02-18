<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');  // CONEXIÓN A LA BASE DE DATOS

// CUANDO SE ENVÍA EL FORMULARIO
if ($_POST) {

    $id_local = $_POST['id_local'] ?? '';
    $id_cliente = $_POST['id_cliente'] ?? '';
    $renta = $_POST['renta'] ?? '';
    $deposito = $_POST['deposito'] ?? '';
    $adicional = $_POST['adicional'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $estatus = $_POST['estatus'] ?? '';

    // INSERTAR CONTRATO
    $consulta = $conexionBD->prepare("INSERT INTO contratos 
        (id_local, id_cliente, renta, deposito, adicional, fecha_inicio, fecha_fin, estatus) 
        VALUES 
        (NULL, :id_cliente, :renta, :deposito, :adicional, :fecha_inicio, :fecha_fin, :estatus)");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->bindParam(':renta', $renta);
    $consulta->bindParam(':deposito', $deposito);
    $consulta->bindParam(':adicional', $adicional);
    $consulta->bindParam(':fecha_inicio', $fecha_inicio);
    $consulta->bindParam(':fecha_fin', $fecha_fin);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();

    // OBTENER ID DEL CONTRATO
    $id_contrato = $conexionBD->lastInsertId();

    // GENERAR PAGOS AUTOMÁTICOS
    if ($estatus == 'Activa') {

        $fechaActual = strtotime($fecha_inicio);
        $fechaFinal = strtotime($fecha_fin);

        while ($fechaActual <= $fechaFinal) {

            $periodo = date('Y-m-01', $fechaActual);

            $consultaPagos = $conexionBD->prepare("
                INSERT INTO pagos 
                (id_contrato, periodo, fecha_pago, monto, estatus)
                VALUES 
                (:id_contrato, :periodo, NULL, :monto, 'Pendiente')
            ");

            $consultaPagos->bindParam(':id_contrato', $id_contrato);
            $consultaPagos->bindParam(':periodo', $periodo);
            $consultaPagos->bindParam(':monto', $renta);
            $consultaPagos->execute();

            $fechaActual = strtotime("+1 month", $fechaActual);
        }
    }

    // REDIRECCIÓN CORRECTA
    header("Location:index.php");
    exit();
}

// CARGAR LOCALES
$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

// CARGAR CLIENTES
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
                        type="text"
                        class="form-control"
                        name="txtID"
                        id="txtID"
                        aria-describedby="helpId"
                        placeholder="ID" />
                </div>



                <div class="mb-3">
                    <label class="form-label">Local</label>
                    <select name="id_local" class="form-control" required>
                        <option value="">-- Selecciona un local --</option>
                        <?php foreach ($listaLocales as $local) { ?>
                            <option value="<?php echo $local['id_local']; ?>">
                                <?php echo $local['codigo']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Arrendatario</label>
                    <select name="id_cliente" class="form-control" required>
                        <option value="">-- Selecciona un Arrendatario--</option>
                        <?php foreach ($listaClientes as $cliente) { ?>
                            <option value="<?php echo $cliente['id_cliente']; ?>">
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
                        aria-describedby="helpId"
                        placeholder="Fecha Fin" />
                </div>


                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="">-- Selecciona estatus --</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Activa">Activa</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Finalizada">Finalizada</option>
                    </select>
                </div>

                <button type="submit" name="accion" value="agregar" class="btn btn-success">Agregar</button>
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