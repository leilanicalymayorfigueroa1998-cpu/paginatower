<?php
include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

$idRol = $_SESSION['id_rol'];
verificarPermiso($conexionBD, $idRol, 'arrendatarios', 'crear');


include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">

    <div class="card">
        <div class="card-header">Nuevo Arrendatario</div>
        <div class="card-body">

            <form action="guardar.php" method="post" autocomplete="off">

                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF(); ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text"
                        class="form-control"
                        name="nombre"
                        required
                        placeholder="Nombre completo">
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono *</label>
                    <input type="tel"
                        class="form-control"
                        name="telefono"
                        required
                        placeholder="Teléfono de contacto">
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email"
                        class="form-control"
                        name="correo"
                        placeholder="correo@ejemplo.com">
                </div>

                <div class="mb-3">
                    <label class="form-label">Aval</label>
                    <input type="text"
                        class="form-control"
                        name="aval"
                        placeholder="Nombre del aval">
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo del Aval</label>
                    <input type="email"
                        class="form-control"
                        name="correoaval"
                        placeholder="correoaval@ejemplo.com">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text"
                        class="form-control"
                        name="direccion"
                        placeholder="Dirección completa">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text"
                        class="form-control"
                        name="ciudad"
                        placeholder="Ciudad">
                </div>

                <button type="submit" class="btn btn-success">
                    Guardar
                </button>

                <a class="btn btn-secondary" href="index.php">
                    Cancelar
                </a>

            </form>
        </div>

    </div>

    <?php include('../../templates/pie.php'); ?>