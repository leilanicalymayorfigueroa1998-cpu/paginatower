function togglePropiedades() {  // MOSTRAR / OCULTAR SUBMENÚ PROPIEDADES
    const menu = document.getElementById("submenuPropiedades");   // Obtiene el menú desplegable
    const arrow = document.getElementById("arrow");  // Obtiene la flecha del ícono

    menu.classList.toggle("show"); // Agrega o quita la clase "show" para mostrar/ocultar el submenú
    arrow.classList.toggle("rotate");    // Agrega o quita la clase "rotate" para girar la flecha
}

function cerrarSesion() { // CONFIRMAR CIERRE DE SESIÓN
    Swal.fire({  // Muestra alerta de confirmación con SweetAlert
        title: '¿Cerrar sesión?',
        text: "Se cerrará tu sesión actual.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => { // Si el usuario confirma
        if (result.isConfirmed) {
            window.location = url_base + "cerrar.php";  // Redirige al archivo cerrar.php
        }
    });
}