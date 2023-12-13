<?php
require_once 'connessione_database.php';

// Query per ottenere i film dal database
$sql = "SELECT id, nome, locandina FROM Film";
$result = $conn->query($sql);
// Chiudi la connessione
$conn->close();

// Inizializza la stringa HTML dei film
$filmHtml = '';

// Itera sui risultati della query e costruisci la stringa HTML dei film
while ($row = $result->fetch_assoc()) {
    $filmHtml .= '<section class="film">';
    $filmHtml .= '<a href="visualizza_film.php?film=' . $row['id'] . '">';
    $filmHtml .= '<img class="film-image" src="data:image/jpeg;base64,' . base64_encode($row['locandina']) . '" alt="">';
    $filmHtml .= '<p> ' . $row['nome'] . '</p>';
    $filmHtml .= '</a>';
    $filmHtml .= '</section>';
}



// Sostituisci la stringa {FILM} nel file HTML con la stringa HTML dei film
$html = file_get_contents('index3.html');
$html = str_replace('{FILM}', $filmHtml, $html);

// Stampa il risultato
echo $html;
?>
