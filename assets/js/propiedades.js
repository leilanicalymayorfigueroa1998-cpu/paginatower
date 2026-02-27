document.addEventListener("DOMContentLoaded", function() {

    const buscador = document.getElementById("buscadorPropiedades");

    if (!buscador) return;

    buscador.addEventListener("keyup", function() {

        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll("#tablaPropiedades tbody tr");

        filas.forEach(function(fila) {

            let textoFila = fila.textContent.toLowerCase();

            fila.style.display = textoFila.includes(filtro) ? "" : "none";

        });

    });

});