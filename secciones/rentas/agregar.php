<?php

include('../../bd.php');

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

    $consulta = $conexionBD->prepare("INSERT INTO rentas (`id_renta`, `id_local`, `id_cliente`, `renta`,
  `deposito`,`adicional`,`fecha_inicio`, `fecha_fin`, `metodos`,`estatus`) 
            VALUES (NULL, :id_renta, :id_local, :id_cliente, :renta,
  :deposito, :adicional, :fecha_inicio, :fecha_fin, :metodo, :estatus");

    $consulta->bindParam(':id_local', $id_local);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->bindParam(':renta', $renta);
    $consulta->bindParam(':deposito', $deposito);
    $consulta->bindParam(':adicional', $adicional);
    $consulta->bindParam(':fecha_inicio', $fecha_inicio);
    $consulta->bindParam(':fecha_fin', $fecha_fin);
    $consulta->bindParam(':metodo', $metodo);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->execute();

    header("Location:index.php");
}

include('../../templates/cabecera.php');

?>

<br />
<div class="card">
    <div class="card-header">Rentas</div>
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
                <label for="" class="form-label">Local</label>
                <input
                    type="text"
                    class="form-control"
                    name="id_local"
                    id="id_local"
                    aria-describedby="helpId"
                    placeholder="Local" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Cliente</label>
                <input
                    type="text"
                    class="form-control"
                    name="id_cliente"
                    id="id_cliente"
                    aria-describedby="helpId"
                    placeholder="Cliente" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Renta </label>
                <input
                    type="text"
                    class="form-control"
                    name="renta_mensual"
                    id="renta_mensual"
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
                <label for="" class="form-label">Metodo</label>
                <input
                    type="date"
                    class="form-control"
                    name="metodo"
                    id="metodo"
                    aria-describedby="helpId"
                    placeholder="Metodo" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Estatus</label>
                <input
                    type="date"
                    class="form-control"
                    name="estatus"
                    id="estatus"
                    aria-describedby="helpId"
                    placeholder="Estatus" />
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>