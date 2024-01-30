<?php
require_once 'queries/queries.php';
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['mail'])) {
    $permessi = getPermessiByUsername($conn, $_SESSION['mail']);
    if ($permessi == true) {
        $accedi_stringa = "<a href='src/php/admin.php'>Area amministrativa</a>";
    } else {
        $accedi_stringa = "<a href='src/php/profilo.php'>Area personale</a>";
    }
} else {
    $accedi_stringa = '<a href="src/php/accedi.php">Accedi</a>';
}

//risultato della query
$result = getFilms($conn);
$conn->close();
$datalist_risultati = '';
$titoliFilm = $result;

while ($row = $result->fetch_assoc()) {
    $titolo = $row['titolo'];
    $datalist_risultati .= "<option value='$titolo'>";
}
mysqli_data_seek($result, 0);
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
        $stringa_info_film .= '<img class="film-image" src="' . $row['locandina'] . '" alt="">';
        $stringa_info_film .= '<p> ' . $row['titolo'] . '</p>';
        $stringa_info_film .= '</a>';
        $stringa_info_film .= '</section>';
    }
    $stringa_info_film .= '</div>';
}

$template_film = file_get_contents('src/html/index.html');
$template_film = str_replace('{ACCEDI}', $accedi_stringa, $template_film);
$template_film = str_replace('{FILM}', $stringa_info_film, $template_film);
$template_film = str_replace('{DATALIST-RISULTATI}', $datalist_risultati, $template_film);

$stringa_footer= file_get_contents('src/html/footer.html');
$template_film = str_replace('{FOOTER}', $stringa_footer, $template_film);

echo $template_film;
?>