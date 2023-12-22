<?php
// Connessione al database (sostituisci con le tue credenziali)
require_once './queries/queries.php';

if(isset($_GET['film_name'])) {
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
    header("Location: visualizza_film.php?film=$film_id");
    //exit();
} else {
    $html_content = file_get_contents('ricerca_errata.html');
    $html_content = str_replace('{NOMEFILM}', $film_name, $html_content);
    echo $html_content;

}

?>