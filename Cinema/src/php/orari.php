<?php
require_once '../../queries/queries.php';

if (isset($_GET['idFilm'])) {
    // Recupera l'ID del film dalla query string
    $idFilm = $_GET['idFilm'];
} else {
    header('Location: ..\html\404.html'); //se utente non inserisce id
    exit; 
}

// Processa i risultati della query
$rowFilm = getFilmByIdQuery($conn, $idFilm);
$proiezioniFilm = getOrariByFilmId($conn, $idFilm);
$conn->close();

$nomeFilm = $rowFilm['nome'];
$locandina = base64_encode($rowFilm['locandina']); // Converte la locandina in formato base64

// Inserisci dinamicamente gli orari nel template HTML
$proiezioniHTML = '';

ksort($proiezioniFilm); //ordino array in base alla data (chiave) da + recente a + lontana

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
    $proiezioniHTML .= "</div></ul></div>";
}

$htmlContent = file_get_contents('../html/orari.html');//linux
//$html_content = file_get_contents('..\html\orari.html'); //windows
$htmlContent = str_replace('{TITOLO}', $nomeFilm, $htmlContent);
$htmlContent = str_replace('{LOCANDINA}', $locandina, $htmlContent);
$htmlContent = str_replace('{IDFILM}', $idFilm, $htmlContent);
$htmlContent = str_replace('{PROIEZIONI}', $proiezioniHTML, $htmlContent);

// Stampare il contenuto HTML risultante
echo $htmlContent;

?>