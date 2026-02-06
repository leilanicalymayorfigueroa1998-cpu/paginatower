<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM rentas WHERE id_renta=:id_renta");
    $consulta->bindParam(':id_renta', $txtID);
    $consulta->execute();

    $ren = $consulta->fetch(PDO::FETCH_LAZY);
    $id_local = $ren['id_local'];
    $id_cliente = $ren['id_cliente'];
    $renta = $ren['renta'];
    $deposito = $ren['deposito'];
    $adicional = $ren['adicional'];
    $fecha_inicio = $ren['fecha_inicio'];
    $fecha_fin = $ren['fecha_fin'];
    $metodo = $ren['metodo'];
    $estatus = $ren['estatus'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_local = isset($_POST['id_local']) ? $_POST['id_local'] : '';
    $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $renta = isset($_POST['renta']) ? $_POST['renta'] : '';
    $deposito = isset($_POST['deposito']) ? $_POST['deposito'] : '';
    $adicional = isset($_POST['adicional']) ? $_POST['adicional'] : '';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $metodo = isset($_POST['metodo']) ? $_POST['metodo'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE rentas SET
                id_local = :id_local,
                id_cliente = :id_cliente,
                renta = :renta,
                deposito = :deposito,
                adicional = :adicional,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                metodo = :metodo,
                estatus = :estatus
                WHERE id_renta = :id_renta");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->bindParam(':renta', $renta);
    $consulta->bindParam(':deposito', $deposito);
    $consulta->bindParam(':adicional', $adicional);
    $consulta->bindParam(':fecha_inicio', $fecha_inicio);
    $consulta->bindParam(':fecha_fin', $fecha_fin);
    $consulta->bindParam(':metodo', $metodo);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->bindParam(':id_renta', $txtID);

    $consulta->execute();
    header("Location:index.php");
}

// Locales
$consultaLocales = $conexionBD->prepare("SELECT id_local, codigo FROM locales");
$consultaLocales->execute();
$listaLocales = $consultaLocales->fetchAll(PDO::FETCH_ASSOC);

// Clientes
$consultaClientes = $conexionBD->prepare("SELECT id_cliente, nombre FROM clientes");
$consultaClientes->execute();
$listaClientes = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');

?>

<br />
<div class="card">
    <div class="card-header">Pagos</div>
    <div class="card-body">

        <form action="" method="post">

            <div class="mb-3">
                <label for="" class="form-label">ID</label>
                <input
                    type="text"
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
                <label class="form-label">Cliente</label>
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
                    name="renta_mensual"
                    id="renta_mensual"
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
                <label class="form-label">Método de pago</label>
                <select name="metodo" class="form-control" required>
                    <option value="Efectivo" <?php echo ($metodo == 'Efectivo') ? 'selected' : ''; ?>>
                        Efectivo
                    </option>
                    <option value="Transferencia" <?php echo ($metodo == 'Transferencia') ? 'selected' : ''; ?>>
                        Transferencia
                    </option>
                    <option value="Depósito" <?php echo ($metodo == 'Depósito') ? 'selected' : ''; ?>>
                        Depósito
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Estatus</label>
                <select name="estatus" class="form-control" required>
                    <option value="Activa" <?php echo ($estatus == 'Activa') ? 'selected' : ''; ?>>
                        Activa
                    </option>
                    <option value="Finalizada" <?php echo ($estatus == 'Finalizada') ? 'selected' : ''; ?>>
                        Finalizada
                    </option>
                    <option value="Pendiente" <?php echo ($estatus == 'Pendiente') ? 'selected' : ''; ?>>
                        Pendiente
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>