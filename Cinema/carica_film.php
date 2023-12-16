<?php
require_once 'connessione_database.php';

if (!isset($_GET['film_name'])) {


    // Query per ottenere i film dal database
    $sql = "SELECT id, nome, locandina FROM Film";
    $result = $conn->query($sql);
    // Chiusa
    $conn->close();

    $stringa_info_film = '<div class="film-container">';

    // creo una section  per ogni film
    while ($row = $result->fetch_assoc()) {
        $stringa_info_film .= '<section class="film">';
        $stringa_info_film .= '<a href="visualizza_film.php?film=' . $row['id'] . '">';
        $stringa_info_film .= '<img class="film-image" src="data:image/jpeg;base64,' . base64_encode($row['locandina']) . '" alt="">';
        $stringa_info_film .= '<p> ' . $row['nome'] . '</p>';
        $stringa_info_film .= '</a>';
        $stringa_info_film .= '</section>';
    }
    $stringa_info_film .= '</div>';

} else {
    $film_name = $_GET['film_name'];


    $stringa_info_film = '<div id="film-non-esiste">';
    $stringa_info_film .= '<div id="immagine-errore-film"> </div>';
    $stringa_info_film .= '<div id="messaggio-errore-film">';
    $stringa_info_film .= '<p>Purtroppo, non abbiamo trovato alcun film corrispondente al nome:<span id="film-name-error">" '.$film_name.' "</span></p>'. 
                          '<p> Ti consigliamo di verificare l\'ortografia, utilizzare un nome alternativo 
                            o esplorare la nostra vasta selezione di film disponibili.</p>';
    $stringa_info_film .= '<p>Torna alla <a href="carica_film.php">lista dei film</a></p>';
    $stringa_info_film .= '</div>';
    $stringa_info_film .= '</div>';


}

// sostituisco stringa {FILM} nel file film_template.html
$template_film = file_get_contents('index3.html');
$template_film = str_replace('{FILM}', $stringa_info_film, $template_film);
echo $template_film;
?>