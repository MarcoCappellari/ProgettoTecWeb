<?php
require_once 'queries/queries.php';

//risultato della query
$result=getFilms($conn);
$conn->close();
$stringa_info_film = '';

// creo una section  per ogni film
if ($result->num_rows == 0) {
    $stringa_info_film .= '<div class="div-non-esiste">';
    $stringa_info_film .= '<div class="immagine-indisponibilità"></div>';
    $stringa_info_film .= '<p>Ci dispiace, al momento non abbiamo film disponibili nella nostra programmazione.</p>
                           <p> Torna presto per scoprire le ultime novità!</p>';
    $stringa_info_film .= '</div>';
} else {
    $stringa_info_film .= '<div id="film-container">';
    while ($row = $result->fetch_assoc()) {
        $stringa_info_film .= '<section class="film">';
        $stringa_info_film .= '<a href="src/php/info_film.php?film=' . $row['id'] . '">';
        $stringa_info_film .= '<img class="film-image" src="data:image/jpeg;base64,' . base64_encode($row['locandina']) . '" alt="">';
        $stringa_info_film .= '<p> ' . $row['nome'] . '</p>';
        $stringa_info_film .= '</a>';
        $stringa_info_film .= '</section>';
    }
    $stringa_info_film .= '</div>';
}

$template_film = file_get_contents('src/html/index.html');
$template_film = str_replace('{FILM}', $stringa_info_film, $template_film);

$stringa_footer= file_get_contents('src/html/footer.html');
$template_film = str_replace('{FOOTER}', $stringa_footer, $template_film);

echo $template_film;
?>
