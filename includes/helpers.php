<?php

function obtenerColorRol($rol)
{  // Función que devuelve un color según el rol del usuario

    switch ($rol) {  // Evalúa el rol recibido

        case 'admin': return '#2563eb'; // Azul para administrador
        case 'usuario': return '#16a34a'; // Verde para usuario normal
        case 'cliente': return '#dc2626'; // Rojo para cliente
        default: return '#6b7280'; // Gris si no coincide
        
    }
}

?>