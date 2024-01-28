document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("aggiunta-proiezione-form");
    form.addEventListener("submit", function (event) {
        // Resetta eventuali errori precedenti
        resettaErrori();

        // Verifica la convalida della data
        var data = document.getElementById("data");
        if (!validaData(data.value)) {
            mostraErrore(data, "La data non è valida.");
            event.preventDefault(); // Impedisce l'invio del modulo se la data non è valida
            return; // Esce dalla funzione per evitare ulteriori controlli
        }

        // Verifica la convalida dell'ora
        var ora = document.getElementById("ora");
        if (!validaOra(ora.value)) {
            mostraErrore(ora, "L'ora non è valida.");
            event.preventDefault(); // Impedisce l'invio del modulo se l'ora non è valida
            return; // Esce dalla funzione per evitare ulteriori controlli
        }
    });

    function validaData(dataString) {
        var dataInput = new Date(dataString);
        var oggi = new Date();
        oggi.setDate(oggi.getDate() - 1);
        return dataInput >= oggi;
    }

    function validaOra(oraString) {
        // Separare l'ora e i minuti
        var oreMinuti = oraString.split(':');
        var ore = parseInt(oreMinuti[0], 10);
        var minuti = parseInt(oreMinuti[1], 10);

        // Controlla se le ore sono comprese tra 0 e 23 e se i minuti sono compresi tra 0 e 59
        if (ore >= 0 && ore <= 23 && minuti >= 0 && minuti <= 59) {
            return true;
        } else {
            return false;
        }
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
