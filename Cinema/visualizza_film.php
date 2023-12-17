<?php

// ID del film
$idFilm = $_GET['film'];

$resultOrariMinimi = null;

require_once 'connessione_database.php';

// QUERY
// Query per ottenere i dati del film
$queryFilm = "SELECT * FROM Film WHERE id='$idFilm'";
$resultFilm = $conn->query($queryFilm);

// Query per ottenere la data più recente
$queryDataMinima = "SELECT MIN(data) as min_data FROM Riproduzione WHERE id_film = '$idFilm'";
$resultDataMinima = $conn->query($queryDataMinima);

// verifico che la query precedente abbia avuto un risultato, se si -> prelevo la data_minima
if ($resultDataMinima) {
    $rowDataMinima = $resultDataMinima->fetch_assoc();
    $dataMinima = $rowDataMinima['min_data'];

    // Query per ottenere gli ORARI per la data più piccola
    $queryOrariMinimi = "SELECT Riproduzione.ora, Riproduzione.data
                         FROM Riproduzione
                         WHERE Riproduzione.id_film = '$idFilm' AND Riproduzione.data = '$dataMinima'
                         ORDER BY Riproduzione.ora";
    $resultOrariMinimi = $conn->query($queryOrariMinimi);

} else {
    echo "Errore nella query per la data minima: " . $conn->error;
}

// Query per ottenere gli attori
$queryAttori = "SELECT Attori.nome, Attori.cognome FROM Attori
                    LEFT JOIN Partecipano ON Attori.id = Partecipano.id_attore
                    WHERE Partecipano.id_film = '$idFilm'";
$resultAttori = $conn->query($queryAttori);

// Query per ottenere i generi
$queryGeneri = "SELECT Conforme.nome_genere FROM Conforme
                    WHERE Conforme.id_film = '$idFilm'";
$resultGeneri = $conn->query($queryGeneri);

// Query per ottenere la trama
$queryTrama = "SELECT trama FROM Film WHERE id='$idFilm'";
$resultTrama = $conn->query($queryTrama);

// Chiudi la connessione al database
$conn->close();


// Se la query per il film ha avuto successo
if ($resultFilm->num_rows > 0) {
    // Ottieni i dati del film
    $rowFilm = $resultFilm->fetch_assoc();
    $titolo = $rowFilm['nome'];
    $immagine = base64_encode($rowFilm['locandina']);
    $regista = $rowFilm['regista'];
    $durata = $rowFilm['durata'];

    // Variabili per memorizzare attori, generi e trama
    $attori = "";
    while ($rowAttori = $resultAttori->fetch_assoc()) {
        $attori .= $rowAttori['nome'] . ' ' . $rowAttori['cognome'] . ', ';
    }
    $attori = rtrim($attori, ', ');

    $generi = "";
    while ($rowGeneri = $resultGeneri->fetch_assoc()) {
        $generi .= $rowGeneri['nome_genere'] . ', ';
    }
    $generi = rtrim($generi, ', ');

    if ($resultTrama) {
        $rowTrama = $resultTrama->fetch_assoc();
        $trama = $rowTrama['trama'];
    }

    // Leggi il contenuto del file HTML
    $template = file_get_contents('film_template.html');

    // Sostituisci le variabili nel template
    $template = str_replace('{TITOLO}', $titolo, $template);
    $template = str_replace('{IMMAGINE}', $immagine, $template);
    $template = str_replace('{REGISTA}', $regista, $template);
    $template = str_replace('{DURATA}', $durata, $template);

    // Aggiungi sezione attori al template
    if (!empty($attori)) {
        $attoriSection = "<p><strong>Attori:</strong> $attori</p>";
        $template = str_replace('{ATTORI_SECTION}', $attoriSection, $template);
    } else {
        $template = str_replace('{ATTORI_SECTION}', '', $template);
    }

    // Aggiungi sezione generi al template
    if (!empty($generi)) {
        $generiSection = " $generi";
        $template = str_replace('{GENERI_SECTION}', $generiSection, $template);
    } else {
        $template = str_replace('{GENERI_SECTION}', '', $template);
    }

    // Aggiungi data al template
    if (!empty($dataMinima)) {
        $dataHTML = "<p class='film-data'>$dataMinima</p>";
        $template = str_replace('{DATA}', $dataHTML, $template);
    } else {
        $template = str_replace('{DATA}', '', $template);
    }

    // Aggiungi la variabile degli orari al template HTML
    if ($resultOrariMinimi && $resultOrariMinimi->num_rows > 0) {
        // Variabile per memorizzare gli orari HTML
        $orariHTML = "<div class='data-orari'>
                            <p><strong>Ora:</strong></p>
                                <div class='orari-container'>";


        while ($rowOrariMinimi = $resultOrariMinimi->fetch_assoc()) {
            $oraCorrente = date('H:i', strtotime($rowOrariMinimi['ora']));

            // Aggiungi ogni ora con una classe
            $orariHTML .= "<p class='film-ora'>$oraCorrente</p>";
        }

        $orariHTML .= "</div></div>";
        $orariHTML .= "<a href='altri_orari.php?idFilm=$idFilm'><strong>Mostra altri orari</strong></a>";

        $template = str_replace('{ORARI}', $orariHTML, $template);
    } else {
        $template = str_replace('{ORARI}', "<p> Nessuna riproduzione è ancora stata programmata, la preghiamo di attendere (le nuove riproduzioni saranno inserite mercoledì sera).</p>", $template);
    }

    $template = str_replace('{TRAMA}', " $trama", $template);

    // Restituisci la pagina HTML risultante
    echo $template;

} else {
    echo "Nessun film trovato con l'ID '$idFilm'.";
}

?>