<?php

// Cargar variables de entorno desde .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lineas = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0) continue;
        [$clave, $valor] = explode('=', $linea, 2);
        $_ENV[trim($clave)] = trim($valor);
    }
}

$servidor  = $_ENV['DB_HOST'] ?? 'localhost';
$baseDatos = $_ENV['DB_NAME'] ?? '';
$usuario   = $_ENV['DB_USER'] ?? '';
$contrasenia = $_ENV['DB_PASS'] ?? '';

try {
    $conexionBD = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8",
        $usuario,
        $contrasenia,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $error) {
    // En producción: loguear el error y mostrar mensaje genérico
    error_log("Error de conexión BD: " . $error->getMessage());
    die("Error interno del servidor. Intente más tarde.");
}

?>