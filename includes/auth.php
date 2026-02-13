<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Inicia o continúa la sesión actual.
$url_base = "http://localhost/paginatower/";  // Variable que guarda la ruta principal del sistema.
if (!isset($_SESSION['usuario'])) {  // VERIFICAR SI EL USUARIO ESTÁ LOGUEADO
    header("Location:" . $url_base . "login.php");  // Redirige al login
    exit(); // Detiene la ejecución del archivo
}
