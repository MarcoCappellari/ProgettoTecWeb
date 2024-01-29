document.addEventListener("DOMContentLoaded", function() {

    var aggiuntaFilmForm = document.getElementById("aggiunta-film-form");
    if (aggiuntaFilmForm) {
        gestisciForm(aggiuntaFilmForm);
    }


    var modificaFilmForm = document.getElementById("modifica-film-form");
    if (modificaFilmForm) {
        gestisciForm(modificaFilmForm);
    }

    function gestisciForm(form) {
        var generePrimarioSelect = form.querySelector("#genere_primario");
        var genereSecondarioSelect = form.querySelector("#genere_secondario");


        nascondiOpzioneSelezionata(generePrimarioSelect.value, genereSecondarioSelect);

        genereSecondarioSelect.addEventListener("change", function() {
            var genereSelezionato = genereSecondarioSelect.value;
            var opzioniPrimario = generePrimarioSelect.options;
            for (var i = 0; i < opzioniPrimario.length; i++) {
                var opzione = opzioniPrimario[i];
                if (opzione.value === genereSelezionato) {
                    opzione.disabled = true;
                } else {
                    opzione.disabled = false;
                }
            }
        });


        generePrimarioSelect.addEventListener("change", function() {
            nascondiOpzioneSelezionata(generePrimarioSelect.value, genereSecondarioSelect);
        });

        form.addEventListener("submit", function(event) {
            resettaErrori(form);
            var durataInput = form.querySelector("#durata_input");
            var durataValue = parseInt(durataInput.value);
            if (durataValue <= 0 || isNaN(durataValue)) {
                mostraErrore(durataInput, "La durata del film deve essere un numero maggiore di zero.");
                event.preventDefault(); 
            }
        });
    }

    function nascondiOpzioneSelezionata(genere, genereSecondarioSelect) {
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

    // Funzione per resettare gli errori del form
    function resettaErrori(form) {
        var errori = form.querySelectorAll(".errore");
        for (var i = 0; i < errori.length; i++) {
            errori[i].parentNode.removeChild(errori[i]);
        }
    }
});
