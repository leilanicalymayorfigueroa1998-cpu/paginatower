<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../bd.php');

if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : '';

    $consulta = $conexionBD->prepare("SELECT * FROM locales WHERE id_local=:id_local");
    $consulta->bindParam(':id_local', $txtID);
    $consulta->execute();

    $locales = $consulta->fetch(PDO::FETCH_LAZY);
    $id_propiedad = $locales['id_propiedad'];
    $codigo = $locales['codigo'];
    $medidas = $locales['medidas'];
    $descripcion = $locales['descripcion'];
    $estacionamiento = $locales['estacionamiento'];
    $estatus = $locales['estatus'];
}

if ($_POST) {
    $txtID  = isset($_POST['txtID']) ? $_POST['txtID'] : '';
    $id_propiedad = isset($_POST['id_propiedad']) ? $_POST['id_propiedad'] : '';
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $medidas = isset($_POST['medidas']) ? $_POST['medidas'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $estacionamiento = isset($_POST['estacionamiento']) ? $_POST['estacionamiento'] : '';
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    print_r($_POST);

    $consulta = $conexionBD->prepare("UPDATE locales SET 
                    id_propiedad = :id_propiedad,
                    codigo = :codigo,
                    medidas = :medidas,
                    descripcion = :descripcion,
                    estacionamiento = :estacionamiento,
                    estatus = :estatus
                WHERE id_local = :id_local");

    $consulta->bindParam(':id_propiedad', $id_propiedad);
    $consulta->bindParam(':codigo', $codigo);
    $consulta->bindParam(':medidas', $medidas);
    $consulta->bindParam(':descripcion', $descripcion);
    $consulta->bindParam(':estacionamiento', $estacionamiento);
    $consulta->bindParam(':estatus', $estatus);
    $consulta->bindParam(':id_local', $txtID);;
    $consulta->execute();
    header("Location:index.php");
}

$consultaProp = $conexionBD->prepare("SELECT id_propiedad, codigo, direccion FROM propiedades");
$consultaProp->execute();
$listaPropiedades = $consultaProp->fetchAll(PDO::FETCH_ASSOC);

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');

?>

<div class="main-content">

    <div class="card">
        <div class="card-header">Locales</div>
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
                    <label class="form-label">Propiedad</label>
                    <select name="id_propiedad" class="form-control" required>
                        <?php foreach ($listaPropiedades as $prop) { ?>
                            <option value="<?php echo $prop['id_propiedad']; ?>"
                                <?php echo ($prop['id_propiedad'] == $id_propiedad) ? 'selected' : ''; ?>>
                                <?php echo $prop['codigo']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="" class="form-label">Codigo</label>
                    <input
                        type="text"
                        class="form-control"
                        name="codigo"
                        id="codigo"
                        value="<?php echo $codigo ?>"
                        aria-describedby="helpId"
                        placeholder="Codigo" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Medidas</label>
                    <input
                        type="text"
                        class="form-control"
                        name="medidas"
                        id="medidas"
                        value="<?php echo $medidas ?>"
                        aria-describedby="helpId"
                        placeholder="Medidas" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Descripcion</label>
                    <input
                        type="text"
                        class="form-control"
                        name="descripcion"
                        id="descripcion"
                        value="<?php echo $descripcion ?>"
                        aria-describedby="helpId"
                        placeholder="Descripcion" />
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">Estacionamiento</label>
                    <input
                        type="text"
                        class="form-control"
                        name="estacionamiento"
                        id="estacionamiento"
                        value="<?php echo $estacionamiento ?>"
                        aria-describedby="helpId"
                        placeholder="Estacionamiento" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select name="estatus" class="form-control" required>
                        <option value="Disponible" <?php echo ($estatus == 'Disponible') ? 'selected' : ''; ?>>
                            Disponible
                        </option>
                        <option value="Ocupado" <?php echo ($estatus == 'Ocupado') ? 'selected' : ''; ?>>
                            Ocupado
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