<?php
require_once 'connessione_database.php';

// Query per ottenere i film dal database
$sql = "SELECT id, nome, locandina FROM Film";
$result = $conn->query($sql);
// Chiusa
$conn->close();

$stringa_info_film = '';

// creo una section  per ogni film
while ($row = $result->fetch_assoc()) {
    $stringa_info_film .= '<section class="film">';
    $stringa_info_film .= '<a href="visualizza_film.php?film=' . $row['id'] . '">';
    $stringa_info_film .= '<img class="film-image" src="data:image/jpeg;base64,' . base64_encode($row['locandina']) . '" alt="">';
    $stringa_info_film .= '<p> ' . $row['nome'] . '</p>';
    $stringa_info_film .= '</a>';
    $stringa_info_film .= '</section>';
}

// sostituisco stringa {FILM} nel file film_template.html
$template_film = file_get_contents('index3.html');
$template_film = str_replace('{FILM}', $stringa_info_film, $template_film);

// Stampa il risultato
echo $template_film;
?>
