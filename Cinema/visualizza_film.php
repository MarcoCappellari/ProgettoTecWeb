<?php
require_once './queries/queries.php';

if (isset($_GET['film'])) {
    // Recupera l'ID del film dalla query string
    $idFilm = $_GET['film'];
} else {
    header('Location: 404.html'); //nel caso in cui chiamo la pagina senza specificare id
    exit; 
}

$resultOrariMinimi = null;

$resultFilm = getFilmByIdQuery($conn, $idFilm);
$dataMinima = getFirstDateOfFilm($conn, $idFilm);
$attori = getFilmActorsById($conn, $idFilm);
$generi = getFilmGenresById($conn, $idFilm);
// verifico che la query precedente abbia avuto un risultato, se si -> prelevo la data_minima
if ($dataMinima) {
    $resultOrariMinimi = getTimesByFilmIdAndDate($conn, $idFilm, $dataMinima);
}

$conn->close();

$titolo = $resultFilm['nome'];
$immagine = base64_encode($resultFilm['locandina']);
$regista = $resultFilm['regista'];
$durata = $resultFilm['durata'];
$trama = $resultFilm['trama'];

// Leggi il contenuto del file HTML
$template = file_get_contents('film_template.html');

// Sostituisci le variabili nel template
$template = str_replace('{TITOLO}', $titolo, $template);
$template = str_replace('{IMMAGINE}', $immagine, $template);
$template = str_replace('{TRAMA}', " $trama", $template);
$template = str_replace('{REGISTA}', $regista, $template);
$template = str_replace('{DURATA}', $durata, $template);
$template = str_replace('{GENERI_SECTION}', $generi, $template);
$template = str_replace('{IDFILM}', $idFilm, $template);

// Aggiungi  attori se presenti nel film (es. film di animazione non ha attori)
if (!empty($attori)) {
    $attoriSection = "<p><strong>Attori:</strong> $attori</p>";
    $template = str_replace('{ATTORI_SECTION}', $attoriSection, $template);
} else {
    $template = str_replace('{ATTORI_SECTION}', '', $template);
}

// Aggiungi data se presente
if (!empty($dataMinima)) {
    $dataHTML = "<p class='film-data'>$dataMinima</p>";
    $template = str_replace('{DATA}', $dataHTML, $template);
} else {
    $template = str_replace('{DATA}', '', $template);
}

// Aggiungi la variabile degli orari al template HTML
if ($resultOrariMinimi && $resultOrariMinimi->num_rows > 0) {
    // Variabile per memorizzare gli orari HTML
    $orariHTML = " <p><strong>Ora:</strong></p>
                <div id='data-ora'>
                <ul>
                <div class='orari-container'>";


    while ($rowOrariMinimi = $resultOrariMinimi->fetch_assoc()) {
        $link = "seleziona_posti.php?idFilm=$idFilm&data=$dataMinima&ora=" . urlencode($rowOrariMinimi['ora']);
        $oraFormattata = date('H:i', strtotime($rowOrariMinimi['ora']));
        $orariHTML .= "<li class='film-ora'><a href='$link'>$oraFormattata</a></li>";
    }

    $orariHTML .= "</div></ul>";
    $orariHTML .= "<a href='altri_orari.php?idFilm=$idFilm'><strong>Mostra tutte le date</strong></a>";
    $orariHTML .= "</div>";
    $template = str_replace('{ORARI}', $orariHTML, $template);
} else {
    $template = str_replace('{ORARI}', "<p> Nessuna riproduzione è ancora stata programmata, la preghiamo di attendere (le nuove riproduzioni saranno inserite mercoledì sera).</p>", $template);
}

echo $template;

?>