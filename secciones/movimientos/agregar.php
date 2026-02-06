<?php

include('../../bd.php');

if ($_POST) {

    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $id_propiedad = isset($_POST['id_propiedad']) ? $_POST['id_propiedad'] : '';
    $id_tipo_operacion = isset($_POST['id_tipo_operacion']) ? $_POST['id_tipo_operacion'] : '';
    $nota = isset($_POST['nota']) ? $_POST['nota'] : '';
    $abono = isset($_POST['abono']) ? $_POST['abono'] : '';
    $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
    $origen = isset($_POST['origen']) ? $_POST['origen'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("INSERT INTO movimientos_financieros
     (id_movimiento, fecha, id_propiedad, id_tipo_operacion, descripcion, 
     nota, abono, cargo, origen)
     VALUES (NULL, :fecha, :id_propiedad, :id_tipo_operacion, :descripcion, 
    :nota, :abono, :cargo, :origen");

    $consulta->bindParam(':fecha', $fecha);
    $consulta->bindParam(':id_propiedad', $id_propiedad);
    $consulta->bindParam(':id_tipo_operacion', $id_tipo_operacion);
    $consulta->bindParam(':nota', $nota);
    $consulta->bindParam(':abono', $abono);
    $consulta->bindParam(':cargo', $cargo);
    $consulta->bindParam(':origen', $origen);
    $consulta->execute();

    header("Location:index.php");
}

//Tabla Tipo de Operaciones 
$consultaTipos = $conexionBD->prepare("SELECT id, codigo, concepto 
FROM tipo_operacion 
ORDER BY codigo");
$consultaTipos->execute();
$tiposOperacion = $consultaTipos->fetchAll(PDO::FETCH_ASSOC);

//Tabla Propiedades
$consultaPropiedades = $conexionBD->prepare("SELECT id_propiedad, codigo 
FROM propiedades
ORDER BY codigo");
$consultaPropiedades->execute();
$propiedades = $consultaPropiedades->fetchAll(PDO::FETCH_ASSOC);


include('../../templates/cabecera.php');

?>

<div class="card">
    <div class="card-header">Movimientos Financieros</div>
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
                <label for="" class="form-label">Fecha</label>
                <input
                    type="date"
                    class="form-control"
                    name="fecha"
                    id="fecha"
                    aria-describedby="helpId"
                    placeholder="Fecha" />
            </div>

            <div class="mb-3">
                <label class="form-label">Propiedad</label>
                <select name="id_propiedad" class="form-control" required>
                    <option value="">Seleccione una propiedad</option>

                    <?php foreach ($propiedades as $p) { ?>
                        <option value="<?php echo $p['id_propiedad']; ?>">
                            <?php echo $p['codigo']; ?>
                        </option>
                    <?php } ?>
                </select>

                <small class="form-text text-muted">
                    Las propiedades se toman del catálogo de Propiedades.
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de operación</label>
                <select name="id_tipo_operacion" class="form-select" required>
                    <option value="">Seleccione un código</option>

                    <?php foreach ($tiposOperacion as $tipo) { ?>
                        <option value="<?php echo $tipo['id']; ?>">
                            <?php echo $tipo['codigo'] . " - " . $tipo['concepto']; ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Nota</label>
                <input
                    type="text"
                    class="form-control"
                    name="Nota"
                    id="Nota"
                    aria-describedby="helpId"
                    placeholder="Descripcion" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Abono</label>
                <input
                    type="text"
                    class="form-control"
                    name="abono"
                    id="Abono"
                    aria-describedby="helpId"
                    placeholder="Abono" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Cargo</label>
                <input
                    type="text"
                    class="form-control"
                    name="cargo"
                    id="cargo"
                    aria-describedby="helpId"
                    placeholder="Cargo" />
            </div>

            <div class="mb-3">
                <label for="origen" class="form-label">Origen</label>
                <select class="form-select" name="origen" id="origen" required>
                    <option value="">Seleccione un origen</option>
                    <option value="CUENTA">Cuenta</option>
                    <option value="EFECTIVO">Efectivo</option>
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