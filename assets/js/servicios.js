function toggleContrato(tipo) {
    const select = document.getElementById(tipo);
    const grupo = document.getElementById('grupo_contrato_' + tipo);
    const input = document.getElementById('contrato_' + tipo);

    if (!select || !grupo || !input) return;

    if (select.value === "1") {
        grupo.style.display = "block";
        input.required = true;
    } else {
        grupo.style.display = "none";
        input.required = false;
        input.value = "";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    toggleContrato('cfe');
    toggleContrato('agua');
});