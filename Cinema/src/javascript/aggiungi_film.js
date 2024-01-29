document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById("aggiunta-film-form");
    var generePrimarioSelect = document.getElementById("genere_primario");
    var genereSecondarioSelect = document.getElementById("genere_secondario");
    var generePrimarioSelezionato = generePrimarioSelect.value;

    nascondiOpzioneSelezionata(generePrimarioSelezionato);
    generePrimarioSelect.addEventListener("change", function() {
        var nuovoGenerePrimario = generePrimarioSelect.value;
        nascondiOpzioneSelezionata(nuovoGenerePrimario);
    });

    form.addEventListener("submit", function(event) {
        resettaErrori();
        var durataInput = document.getElementById("durata_input");
        var durataValue = parseInt(durataInput.value);
        if (durataValue <= 0 || isNaN(durataValue)) {
            mostraErrore(durataInput, "La durata del film deve essere un numero maggiore di zero.");
            event.preventDefault(); 
        }
    });


    function nascondiOpzioneSelezionata(genere) {
        var opzioniSecondario = genereSecondarioSelect.options;
        for (var i = 0; i < opzioniSecondario.length; i++) {
            var opzione = opzioniSecondario[i];
            if (opzione.value === genere) {
                opzione.style.display = "none";
            } else {
                opzione.style.display = "";
            }
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
