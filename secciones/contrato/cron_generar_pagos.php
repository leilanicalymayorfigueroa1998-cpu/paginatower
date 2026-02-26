<?php
require_once 'bd.php';
require_once 'services/ContratoService.php';

$service = new ContratoService($conexionBD);
$service->generarPagosIndefinidos();

echo "Pagos generados correctamente.";