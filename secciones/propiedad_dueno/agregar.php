<?php
include('../../bd.php');

// PROPIEDADES
$consultaProp = $conexionBD->prepare("SELECT id_propiedad, codigo FROM propiedades");
$consultaProp->execute();
$listaPropiedades = $consultaProp->fetchAll(PDO::FETCH_ASSOC);

// DUEÑOS
$consultaDueno = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
$consultaDueno->execute();
$listaDuenos = $consultaDueno->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {

    $id_propiedad = $_POST['id_propiedad'];
    $id_dueno = $_POST['id_dueno'];

    // ❌ Validar duplicado exacto
    $validar = $conexionBD->prepare("SELECT COUNT(*) FROM propiedad_dueno
        WHERE id_propiedad = :id_propiedad
        AND id_dueno = :id_dueno");
    $validar->bindParam(':id_propiedad', $id_propiedad);
    $validar->bindParam(':id_dueno', $id_dueno);
    $validar->execute();

    if ($validar->fetchColumn() > 0) {
        echo "<script>alert('Esta propiedad ya está asignada a este dueño');</script>";
    } else {

        // ❌ Validar propiedad con dueño activo
        $validarProp = $conexionBD->prepare(" SELECT COUNT(*) FROM propiedad_dueno
            WHERE id_propiedad = :id_propiedad");
        $validarProp->bindParam(':id_propiedad', $id_propiedad);
        $validarProp->execute();

        if ($validarProp->fetchColumn() > 0) {
            echo "<script>alert('Esta propiedad ya tiene un dueño asignado');</script>";
        } else {

            // ✅ INSERT
            $consulta = $conexionBD->prepare("INSERT INTO propiedad_dueno (id, id_propiedad, id_dueno)
                VALUES (NULL, :id_propiedad, :id_dueno) ");
            $consulta->bindParam(':id_propiedad', $id_propiedad);
            $consulta->bindParam(':id_dueno', $id_dueno);
            $consulta->execute();

            header("Location:index.php");
            exit();
        }
    }
}

include('../../templates/cabecera.php');
?>

<div class="card">
    <div class="card-header">Propiedad-Dueño</div>
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
                <label class="form-label">Propiedad</label>
                <select class="form-control" name="id_propiedad" required>
                    <option value="">Seleccione una propiedad</option>
                    <?php foreach ($listaPropiedades as $prop) { ?>
                        <option value="<?php echo $prop['id_propiedad']; ?>">
                            <?php echo $prop['codigo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dueño</label>
                <select class="form-control" name="id_dueno" required>
                    <option value="">Seleccione un dueño</option>
                    <?php foreach ($listaDuenos as $dueno) { ?>
                        <option value="<?php echo $dueno['id_dueno']; ?>">
                            <?php echo $dueno['nombre']; ?>
                        </option>
                    <?php } ?>
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

    <div class="card-footer text-muted">


    </div>

</div>

<?php include('../../templates/pie.php'); ?>