<?php
// Protección: solo ejecutable desde línea de comandos (CLI)
// Nunca debe ser accesible desde el navegador
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Acceso no permitido. Este script solo puede ejecutarse desde el servidor.');
}

require_once __DIR__ . '/../../bd.php';
require_once __DIR__ . '/../../services/ContratoService.php';

$service = new ContratoService($conexionBD);
$service->generarPagosIndefinidos();

echo "[" . date('Y-m-d H:i:s') . "] Pagos generados correctamente.\n";
