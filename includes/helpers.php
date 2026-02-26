<?php

function obtenerColorRol($rol)
{  // Función que devuelve un color según el rol del usuario

    switch ($rol) {  // Evalúa el rol recibido

        case 'admin': return '#2563eb'; // Azul para administrador
        case 'usuario': return '#16a34a'; // Verde para usuario normal
        case 'arrendatario': return '#dc2626'; // Rojo para cliente
        default: return '#6b7280'; // Gris si no coincide
        
    }
}

// 🔐 Generar token CSRF
function generarTokenCSRF() {

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}


// 🔐 Validar token CSRF
function validarTokenCSRF($token) {

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

?>