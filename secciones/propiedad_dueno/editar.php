<?php
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];

    $consulta = $conexionBD->prepare("SELECT * FROM propiedad_dueno WHERE id = :id");
    $consulta->bindParam(':id', $txtID);
    $consulta->execute();
    $registro = $consulta->fetch(PDO::FETCH_ASSOC);

    $id_propiedad = $registro['id_propiedad'];
    $id_dueno = $registro['id_dueno'];
}

// PROPIEDADES
$consultaProp = $conexionBD->prepare("SELECT id_propiedad, codigo FROM propiedades");
$consultaProp->execute();
$listaPropiedades = $consultaProp->fetchAll(PDO::FETCH_ASSOC);

// DUEÑOS
$consultaDueno = $conexionBD->prepare("SELECT id_dueno, nombre FROM duenos");
$consultaDueno->execute();
$listaDuenos = $consultaDueno->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {

    $txtID = $_POST['txtID'];
    $id_propiedad = $_POST['id_propiedad'];
    $id_dueno = $_POST['id_dueno'];

    // ❌ Validar duplicado (excepto el mismo registro)
    $validar = $conexionBD->prepare("
        SELECT COUNT(*) FROM propiedad_dueno
        WHERE id_propiedad = :id_propiedad
        AND id_dueno = :id_dueno
        AND id != :id
    ");
    $validar->bindParam(':id_propiedad', $id_propiedad);
    $validar->bindParam(':id_dueno', $id_dueno);
    $validar->bindParam(':id', $txtID);
    $validar->execute();

    if ($validar->fetchColumn() > 0) {
        echo "<script>alert('Esta combinación ya existe');</script>";
    } else {

        // ❌ Validar propiedad con otro dueño
        $validarProp = $conexionBD->prepare("SELECT COUNT(*) FROM propiedad_dueno
            WHERE id_propiedad = :id_propiedad
            AND id != :id");
        $validarProp->bindParam(':id_propiedad', $id_propiedad);
        $validarProp->bindParam(':id', $txtID);
        $validarProp->execute();

        if ($validarProp->fetchColumn() > 0) {
            echo "<script> alert('Esta propiedad ya tiene otro dueño'); </script>";
        } else {

            // ✅ UPDATE
            $consulta = $conexionBD->prepare("UPDATE propiedad_dueno
                SET id_propiedad = :id_propiedad,
                    id_dueno = :id_dueno
                WHERE id = :id");
            $consulta->bindParam(':id_propiedad', $id_propiedad);
            $consulta->bindParam(':id_dueno', $id_dueno);
            $consulta->bindParam(':id', $txtID);
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

        <form method="post">

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
                <label class="form-label">Propiedad</label>
                <select class="form-control" name="id_propiedad" required>
                    <?php foreach ($listaPropiedades as $prop) { ?>
                        <option
                            value="<?php echo $prop['id_propiedad']; ?>"
                            <?php echo ($prop['id_propiedad'] == $id_propiedad) ? 'selected' : ''; ?>>
                            <?php echo $prop['codigo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dueño</label>
                <select class="form-control" name="id_dueno" required>
                    <?php foreach ($listaDuenos as $dueno) { ?>
                        <option
                            value="<?php echo $dueno['id_dueno']; ?>"
                            <?php echo ($dueno['id_dueno'] == $id_dueno) ? 'selected' : ''; ?>>
                            <?php echo $dueno['nombre']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Modificar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>

        </form>

    </div>

    <div class="card-footer text-muted">


    </div>

</div>


<?php include('../../templates/pie.php'); ?>