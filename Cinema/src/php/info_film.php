<?php
require_once '../../queries/queries.php';

session_start();

include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

if (isset($_GET['film'])) {
    // Recupera l'ID del film dalla query string
    $idFilm = $_GET['film'];
} else {
    header('Location: 404.php'); 
    exit;
}

$resultFilm = getFilmByIdQuery($conn, $idFilm);
$generi = getFilmGenresById($conn, $idFilm);
$proiezioniFilm = getOrariByFilmId($conn, $idFilm);

$conn->close();


$titolo = $resultFilm['titolo'];
$immagine = $resultFilm['locandina']; 
$regista = $resultFilm['regista'];
$durata = $resultFilm['durata'];
$trama = $resultFilm['trama'];

// Leggi il contenuto del file HTML
$template = file_get_contents('../html/info_film.html');

// Sostituisci le variabili nel template
$template = str_replace('{TITOLO}', $titolo, $template);
$template = str_replace('{IMMAGINE}', $immagine, $template);
$template = str_replace('{TRAMA}', " $trama", $template);
$template = str_replace('{REGISTA}', $regista, $template);
$template = str_replace('{DURATA}', $durata, $template);
$template = str_replace('{GENERI_SECTION}', $generi, $template);


// Aggiungi la variabile degli orari al template HTML
if ($proiezioniFilm) {

    ksort($proiezioniFilm); //ordino array in base alla data (chiave) da + recente a + lontana
    $proiezioniHTML = '';
    $count = 0;
    foreach ($proiezioniFilm as $data => $orari) {
        $proiezioniHTML .= "<div id='data-ora'>";
        $proiezioniHTML .= "<p>Data: <time class='film-data' datatime=".$data.">$data</time></p>";
        $proiezioniHTML .= "<p>Ora:</p>";
        $proiezioniHTML .= "<ul>";
        $proiezioniHTML .= "<div class='orari-container'>";

        foreach ($orari as $ora) {

            $oraFormattata = date('H:i', strtotime($ora));
            //PER OGNI ORA, collego un link alla pagina di selezione posto
            $link = "posti.php?idFilm=$idFilm&data=$data&ora=" . urlencode($ora);
            // Crea il link con l'orario come testo del link
            $proiezioniHTML .= "<li class='film-ora'><a href='$link'>$oraFormattata</a></li>";
        }
        $count++;
        $proiezioniHTML .= "</div></ul></div>";
        if ($count == 1) {
            $proiezioniHTML .= "<p class='header-info-film'>Prossime date di Proiezione:</p>";
        }
    }
    if ($count == 1) {
        $proiezioniHTML .= "<p> Nessun'altra programmazione oltre a quella del <span class='film-data'>$data</span> è stata programmata, (le nuove proiezioni saranno inserite mercoledì sera).</p>";
    }
    $template = str_replace('{PROIEZIONI}', $proiezioniHTML, $template);

} else {
    $template = str_replace('{PROIEZIONI}', "<p> Nessuna proiezione è ancora stata programmata, la preghiamo di attendere (le nuove proiezioni saranno inserite mercoledì sera).</p>", $template);
}

$template = str_replace('{ACCEDI}', $accedi_stringa, $template);

$footer = file_get_contents('../html/footer.html');
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>