<?php
// Connessione al database (sostituisci con le tue credenziali)
require_once '../../queries/queries.php';

session_start();

include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$result_film = getFilms($conn);
$datalist_risultati = '';
while ($row = $result_film->fetch_assoc()) {
    $titolo = $row['titolo'];
    $datalist_risultati .= "<option value='$titolo'>";
}

if(isset(clearInput($_GET['film_name']))) {
    $film_name = $_GET['film_name'];
    $result = getFilmByName($conn, $film_name);
    $conn->close();
}

if ($film_name === '') {
    $result = null;
}


// Elabora i risultati
if ($result) {
    $film_id = $result['id'];
    header("Location: info_film.php?film=$film_id");
} else {
    $html_content = file_get_contents('../html/barra_ricerca.html'); 
    $footer = file_get_contents('../html/footer.html');

    $html_content = str_replace('{ACCEDI}', $accedi_stringa, $html_content);
    $html_content = str_replace('{NOMEFILM}', $film_name, $html_content);
    
    $html_content = str_replace('{DATALIST-RISULTATI}', $datalist_risultati, $html_content);
    $html_content = str_replace('{FOOTER}', $footer, $html_content);
    
    echo $html_content;

}

?>