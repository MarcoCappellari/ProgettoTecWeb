<?php
require_once '../../queries/queries.php';

session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi=getPermessiByUsername($conn, $_SESSION['username']);
    if($permessi==True){
        $accedi_stringa = "<a href='admin.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }else{
        $accedi_stringa = "<a href='profilo.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }
} else {
    $accedi_stringa = '<a href="src/html/accedi.html">Accedi</a>';
}

if (isset($_GET['film'])) {
    // Recupera l'ID del film dalla query string
    $idFilm = $_GET['film'];
} else {
    header('Location: ../html/404.html'); //nel caso in cui chiamo la pagina senza specificare id
    exit;
}

$resultFilm = getFilmByIdQuery($conn, $idFilm);
$attori = getFilmActorsById($conn, $idFilm);
$generi = getFilmGenresById($conn, $idFilm);
$proiezioniFilm = getOrariByFilmId($conn, $idFilm);

$conn->close();


$titolo = $resultFilm['nome'];
$immagine = base64_encode($resultFilm['locandina']);
$regista = $resultFilm['regista'];
$durata = $resultFilm['durata'];
$trama = $resultFilm['trama'];

// Leggi il contenuto del file HTML
$template = file_get_contents('../html/info_film.html'); //linux

// Sostituisci le variabili nel template
$template = str_replace('{TITOLO}', $titolo, $template);
$template = str_replace('{IMMAGINE}', $immagine, $template);
$template = str_replace('{TRAMA}', " $trama", $template);
$template = str_replace('{REGISTA}', $regista, $template);
$template = str_replace('{DURATA}', $durata, $template);
$template = str_replace('{GENERI_SECTION}', $generi, $template);

// Aggiungi  attori se presenti nel film (es. film di animazione non ha attori)
if (!empty($attori)) {
    $attoriSection = "<p><span class='bold-text'>Attori:</span> $attori</p>";
    $template = str_replace('{ATTORI_SECTION}', $attoriSection, $template);
} else {
    $template = str_replace('{ATTORI_SECTION}', '', $template);
}

// Aggiungi la variabile degli orari al template HTML
if ($proiezioniFilm) {

    ksort($proiezioniFilm); //ordino array in base alla data (chiave) da + recente a + lontana
    $proiezioniHTML = '';
    $count = 0;
    foreach ($proiezioniFilm as $data => $orari) {
        $proiezioniHTML .= "<div id='data-ora'>";
        $proiezioniHTML .= "<p>Data: <span class='film-data'>$data</span></p>";
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
            $proiezioniHTML .= "<p><span class='bold-text'>Prossime date di Riproduzione:</span></p>";
        }
    }
    if ($count == 1) {
        $proiezioniHTML .= "<p> Nessun'altra programmazione oltre a quella del <span class='film-data'>$data</span> è stata programmata, (le nuove riproduzioni saranno inserite mercoledì sera).</p>";
    }
    $template = str_replace('{PROIEZIONI}', $proiezioniHTML, $template);

} else {
    $template = str_replace('{PROIEZIONI}', "<p> Nessuna riproduzione è ancora stata programmata, la preghiamo di attendere (le nuove riproduzioni saranno inserite mercoledì sera).</p>", $template);
}

$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
echo $template;

?>