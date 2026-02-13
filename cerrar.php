<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye completamente la sesión
header("Location: login.php"); // Redirige al usuario al login
exit(); // Detiene la ejecución del script
