document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById("aggiunta-proiezione-form");
    form.addEventListener("submit", function(event) {
        // Resetta eventuali errori precedenti
        resettaErrori();

        // Verifica la convalida della data
        var data = document.getElementById("data");
        if (!validaData(data.value)) {
            mostraErrore(data, "La data non è valida.");
            event.preventDefault(); // Impedisce l'invio del modulo se la data non è valida
        }
    });

    function validaData(dataString) {
        var dataInput = new Date(dataString);
        var oggi = new Date();
        return dataInput >= oggi; // La data deve essere uguale o successiva a oggi
    }

    function mostraErrore(input, messaggio) {
        var errore = document.createElement("p");
        errore.className = "errore";
        errore.textContent = messaggio;
        input.parentNode.appendChild(errore);
    }

    function resettaErrori() {
        var errori = form.querySelectorAll(".errore");
        for (var i = 0; i < errori.length; i++) {
            errori[i].parentNode.removeChild(errori[i]);
        }
    }
});
