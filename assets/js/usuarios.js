document.addEventListener("DOMContentLoaded", function() {

    const rolSelect = document.querySelector("select[name='id_rol']");
    const grupoCliente = document.getElementById("grupo_cliente");
    const grupoDueno = document.getElementById("grupo_dueno");

    function actualizarCampos() {
        const rol = rolSelect.value;

        grupoCliente.style.display = "none";
        grupoDueno.style.display = "none";

        if (rol == 3) {
            grupoCliente.style.display = "block";
        }

        if (rol == 2) {
            grupoDueno.style.display = "block";
        }
    }

    if (rolSelect) {
        actualizarCampos();
        rolSelect.addEventListener("change", actualizarCampos);
    }
});
