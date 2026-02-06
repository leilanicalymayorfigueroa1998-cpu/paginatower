<?php

include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM movimientos_financieros 
    WHERE id_movimiento=:id_movimiento");
    $consulta->bindParam(':id_movimiento', $txtID);
    $consulta->execute();

    $financiero = $consulta->fetch(PDO::FETCH_LAZY);
    $id_movimiento = $financiero['id_movimiento'];
    $fecha = $financiero['fecha'];
    $id_propiedad = $financiero['id_propiedad'];
    $id_tipo_operacion = $financiero['id_tipo_operacion'];
    $nota = $financiero['nota'];
    $abono = $financiero['abono'];
    $cargo = $financiero['cargo'];
    $origen = $financiero['origen'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_movimiento = isset($_POST['id_movimiento']) ? $_POST['id_movimiento'] : '';
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $id_propiedad = isset($_POST['id_propiedad']) ? $_POST['id_propiedad'] : '';
    $id_tipo_operacion = isset($_POST['id_tipo_operacion']) ? $_POST['id_tipo_operacion'] : '';
    $nota = isset($_POST['nota']) ? $_POST['nota'] : '';
    $abono = isset($_POST['abono']) ? $_POST['abono'] : '';
    $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
    $origen = isset($_POST['origen']) ? $_POST['origen'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    $consulta = $conexionBD->prepare("UPDATE movimientos_financieros SET 
                    fecha = :fecha,
                    id_propiedad = :id_propiedad,
                    id_tipo_operacion = :id_tipo_operacion,
                    nota = :nota,
                    abono = :abono,
                    cargo = :cargo,
                    origen = :origen
                WHERE id_movimiento = :id_movimiento");

    $consulta->bindParam(':fecha', $fecha);
    $consulta->bindParam(':id_propiedad', $id_propiedad);
    $consulta->bindParam(':id_tipo_operacion', $id_tipo_operacion);
    $consulta->bindParam(':nota', $nota);
    $consulta->bindParam(':abono', $abono);
    $consulta->bindParam(':cargo', $cargo);
    $consulta->bindParam(':origen', $origen);
    $consulta->bindParam(':id_movimiento', $txtID);;
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
                    value="<?php echo $txtID ?>"
                    aria-describedby="helpId"
                    placeholder="ID" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Fecha</label>
                <input
                    type="text"
                    class="form-control"
                    name="fecha"
                    id="fecha"
                    value="<?php echo $fecha ?>"
                    aria-describedby="helpId"
                    placeholder="Fecha" />
            </div>

            <div class="mb-3">
                <label class="form-label">Propiedad</label>
                <select name="id_propiedad" class="form-control" required>
                    <option value="">Seleccione una propiedad</option>

                    <?php foreach ($propiedades as $p) { ?>
                        <option
                            value="<?php echo $p['id_propiedad']; ?>"
                            <?php echo ($p['id_propiedad'] == $id_propiedad) ? 'selected' : ''; ?>>

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
                    <?php foreach ($tiposOperacion as $tipo) { ?>
                        <option value="<?php echo $tipo['id']; ?>"
                            <?php echo ($tipo['id'] == $id_tipo_operacion) ? 'selected' : ''; ?>>
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
                    name="nota"
                    id="nota"
                    value="<?php echo $nota ?>"
                    aria-describedby="helpId"
                    placeholder="Nota" />
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Abono</label>
                <input
                    type="text"
                    class="form-control"
                    name="abono"
                    id="Abono"
                    value="<?php echo $abono ?>"
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
                    value="<?php echo $cargo ?>"
                    aria-describedby="helpId"
                    placeholder="Cargo" />
            </div>

            <div class="mb-3">
                <label class="form-label">Origen</label>
                <select name="origen" class="form-select" required>
                    <option value="CUENTA" <?php echo ($origen == 'CUENTA') ? 'selected' : ''; ?>>
                        Cuenta
                    </option>
                    <option value="EFECTIVO" <?php echo ($origen == 'EFECTIVO') ? 'selected' : ''; ?>>
                        Efectivo
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