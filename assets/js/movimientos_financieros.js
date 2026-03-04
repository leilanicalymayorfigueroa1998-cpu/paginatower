document.querySelector("form").addEventListener("submit", function(e){

let abono = parseFloat(document.querySelector("[name='abono']").value) || 0;
let cargo = parseFloat(document.querySelector("[name='cargo']").value) || 0;

if(abono > 0 && cargo > 0){
    alert("Solo puedes capturar Abono o Cargo, no ambos.");
    e.preventDefault();
}

if(abono === 0 && cargo === 0){
    alert("Debes capturar un Abono o un Cargo.");
    e.preventDefault();
}

});

document.getElementById("tipoOperacion").addEventListener("change", function(){

let concepto = this.options[this.selectedIndex].dataset.concepto;

document.getElementById("concepto").value = concepto ?? "";

});
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const tipoOperacion = document.getElementById("tipoOperacion");
    const concepto = document.getElementById("concepto");

    // Validación Abono / Cargo
    if (form) {
        form.addEventListener("submit", function (e) {

            let abono = parseFloat(document.querySelector("[name='abono']").value) || 0;
            let cargo = parseFloat(document.querySelector("[name='cargo']").value) || 0;

            if (abono > 0 && cargo > 0) {
                alert("Solo puedes capturar Abono o Cargo, no ambos.");
                e.preventDefault();
            }

            if (abono === 0 && cargo === 0) {
                alert("Debes capturar un Abono o un Cargo.");
                e.preventDefault();
            }

        });
    }

    // Mostrar concepto del tipo de operación
    if (tipoOperacion) {
        tipoOperacion.addEventListener("change", function () {

            let textoConcepto = this.options[this.selectedIndex].dataset.concepto;

            if (concepto) {
                concepto.value = textoConcepto ?? "";
            }

        });
    }

});