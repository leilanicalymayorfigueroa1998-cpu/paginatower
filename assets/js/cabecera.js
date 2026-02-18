function togglePropiedades() {

    const menu = document.getElementById("submenuPropiedades");
    const arrow = document.querySelector(".arrow");

    if (menu) {
        menu.classList.toggle("show");
    }

    if (arrow) {
        arrow.classList.toggle("rotate");
    }
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