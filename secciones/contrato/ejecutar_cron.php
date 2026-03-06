<?php
/**
 * ejecutar_cron.php
 * ─────────────────────────────────────────────────────────────
 * Página protegida para ejecutar el cron de pagos desde el
 * navegador. Solo accesible para usuarios autenticados con
 * permiso de administrador (id_rol = 1).
 *
 * URL: /secciones/contrato/ejecutar_cron.php
 * ─────────────────────────────────────────────────────────────
 */

include('../../includes/auth.php');
include('../../includes/helpers.php');
include('../../includes/permisos.php');
include('../../bd.php');

require_once __DIR__ . '/../../services/ContratoService.php';

// ── Solo admins ────────────────────────────────────────────────
$idRol = $_SESSION['id_rol'] ?? null;

if (!$idRol) {
    header("Location: ../../login.php");
    exit();
}

// Cambiar 1 por el id_rol de administrador en tu sistema
if ((int)$idRol !== 1) {
    http_response_code(403);
    die("Acceso denegado. Solo administradores pueden ejecutar esta acción.");
}

// ── Ejecutar solo si se confirma vía POST con CSRF ─────────────
$resultado   = null;
$generados   = null;
$error       = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = "Token de seguridad inválido. Recarga la página e intenta de nuevo.";
    } else {
        unset($_SESSION['csrf_token']);

        try {
            $service  = new ContratoService($conexionBD);
            $generados = $service->generarPagosIndefinidos();
            $resultado = "success";
        } catch (Exception $e) {
            $error = "Error al ejecutar el cron: " . htmlspecialchars($e->getMessage());
        }
    }
}

include('../../templates/cabecera.php');
include('../../templates/topbar.php');
include('../../templates/sidebar.php');
?>

<div class="content">
    <div class="card" style="max-width:600px; margin:0 auto;">
        <div class="card-header">
            <strong>⚙️ Generar Pagos Mensuales (Contratos Indefinidos)</strong>
        </div>
        <div class="card-body">

            <?php if ($resultado === 'success'): ?>
                <div class="alert alert-success">
                    ✅ <strong>Cron ejecutado correctamente.</strong><br>
                    Se generaron pagos para <strong><?= $generados ?></strong>
                    contrato(s) indefinido(s) activo(s) este mes
                    <em>(<?= date('F Y') ?>)</em>.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    ❌ <?= $error ?>
                </div>
            <?php endif; ?>

            <p class="mb-3">
                Esta acción genera automáticamente el pago del mes
                <strong><?= date('F Y') ?></strong> para todos los contratos
                <em>indefinidos activos</em> que aún no tengan pago registrado.
            </p>

            <div class="alert alert-warning">
                ⚠️ <strong>Nota:</strong> Normalmente esto se ejecuta automáticamente
                el día 1 de cada mes vía Cron Job del servidor.
                Usa este botón solo si el cron no corrió o para pruebas.
            </div>

            <form action="ejecutar_cron.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">
                <button type="submit" class="btn btn-primary">
                    ▶ Ejecutar ahora
                </button>
                <a href="index.php" class="btn btn-secondary ms-2">Cancelar</a>
            </form>

        </div>
    </div>
</div>

<?php include('../../templates/pie.php'); ?>
