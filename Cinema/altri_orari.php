<?php

if (isset($_GET['idFilm'])) {
    // Recupera l'ID del film dalla query string
    $idFilm = $_GET['idFilm'];

    // Connessione al database
    require_once 'connessione_database.php';

    // Esegui la query per ottenere gli orari di proiezione e le informazioni sul film
    $query = "SELECT Riproduzione.data, Riproduzione.ora, Film.nome, Film.locandina 
              FROM Riproduzione
              JOIN Film ON Riproduzione.id_film = Film.id
              WHERE Riproduzione.id_film = $idFilm";

    $result = $conn->query($query);

    if ($result) {
        // Creare un array per memorizzare gli orari recuperati dal database
        $proiezioniFilm = array();

        // Processa i risultati della query
        while ($row = $result->fetch_assoc()) {
            $data = $row['data'];
            $ora = $row['ora'];
            $nomeFilm = $row['nome'];
            $locandina = base64_encode($row['locandina']); // Converte la locandina in formato base64

            // Aggiungi le informazioni all'array per la data corrispondente
            $proiezioniFilm[$data][] = array('ora' => $ora);
        }

        // Carica il contenuto del template HTML
        $htmlContent = file_get_contents('altri_orari.html');

        // Inserisci dinamicamente gli orari nel template HTML
        $proiezioniHTML = '';

        // Visualizza la locandina e il nome del film solo una volta all'inizio della pagina
        $htmlContent = str_replace('{TITOLO}', $nomeFilm, $htmlContent);
        $htmlContent = str_replace('{LOCANDINA}', $locandina, $htmlContent);
        $htmlContent = str_replace('{IDFILM}', $idFilm, $htmlContent);

        ksort($proiezioniFilm); //ordino array in base alla data (chiave) da + recente a + lontana
        foreach ($proiezioniFilm as $data => $proiezioni) {
            $proiezioniHTML .= "<div id='data-ora'>";
            $proiezioniHTML .= "<p>Data: $data</p>";
            $proiezioniHTML .= "<p>Ora:</p>";
            $proiezioniHTML .= "<ul>";
            $proiezioniHTML .= "<div class='orari-container'>";

            foreach ($proiezioni as $proiezione) {
                $oraFormattata = date('H:i', strtotime($proiezione['ora']));
               //PER OGNI ORA, collego un link alla pagina di selezione posto
                $link = "seleziona_posti.php?idFilm=$idFilm&data=$data&ora=" . urlencode($proiezione['ora']);
    
                // Crea il link con l'orario come testo del link
                $proiezioniHTML .= "<li class='film-ora'><a href='$link'>$oraFormattata</a></li>";
            }
            $proiezioniHTML .= "</div></ul></div>";

        }
      //  $proiezioniHTML .= "</div>";
        // Sostituisci il segnaposto {PROIEZIONI} nel template HTML
        $htmlContent = str_replace('{PROIEZIONI}', $proiezioniHTML, $htmlContent);

        // Stampare il contenuto HTML risultante
        echo $htmlContent;

        // Rilascia le risorse del risultato
        $result->free();
    } else {
        echo "<p>Errore nella query: " . $conn->error . "</p>";
    }

    // Chiudi la connessione al database
    $conn->close();
} else {
    echo "<p>Nessun ID del film fornito.</p>";
}
?>