document.addEventListener("DOMContentLoaded", function(){

    const campoMedidas = document.getElementById("medidas");

    if(campoMedidas){

        campoMedidas.addEventListener("blur", function(){

            let valor = this.value.toLowerCase().replace(/\s/g,'');

            // Caso 12x5
            if(valor.includes("x")){

                let partes = valor.split("x");

                let largo = parseFloat(partes[0]);
                let ancho = parseFloat(partes[1]);

                if(!isNaN(largo) && !isNaN(ancho)){
                    let area = largo * ancho;
                    this.value = area + " m²";
                }

            }
            else{

                let numero = parseFloat(valor);

                if(!isNaN(numero)){
                    this.value = numero + " m²";
                }

            }

        });

    }

});