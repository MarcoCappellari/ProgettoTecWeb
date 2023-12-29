<?php
// Connessione al database
require_once '../../queries/queries.php';

if (!isset($_GET['idFilm']) || !isset($_GET['ora']) || !isset($_GET['data']) || 
    $_GET['idFilm'] === null || $_GET['ora'] === null || $_GET['data'] === null) {
    
    header('Location: ..\html\404.html'); 
    exit; 
}

$id_film = $_GET['idFilm'];
$ora_film = $_GET['ora'];
$data_film = $_GET['data'];

$FilmRow = getFilmByIdQuery($conn, $id_film);
$SeatRow = getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film);
$conn->close();

$titolo = $FilmRow['nome'];
$locandina = base64_encode($FilmRow['locandina']);

$current_fila = "";
$output = '';

while ($row = $SeatRow->fetch_assoc()) {
    if ($current_fila != $row['fila']) {
        if (!empty($current_fila)) {
            $output .= "</div>"; //fine riga dei posti
        }
        $current_fila = $row['fila'];
        $output .= "<div><span class='bold-text'>Fila " . $current_fila . "</span>: "; //il div inserito serve per andare a capo ogni riga dei posti
    }

    $stato_posto = $row['disponibile'] ? "selezionabile" : "non-selezionabile";
    $output .= "<label class='$stato_posto' for='posto_" . $row['fila'] . $row['numero_posto'] . "'>";
    $output .= "<input type='checkbox' id='posto_" . $row['fila'] . $row['numero_posto'] . "' name='posti_selezionati[]' value='" . $row['fila'] . $row['numero_posto'] . "' " . ($row['disponibile'] ? '' : 'disabled') . ">";
    $output .= "</label> ";
}
if (!empty($current_fila)) {
    $output .= "</div>"; //chiudo ultima riga dei posti
}

$ora_formattata = date('H:i', strtotime($ora_film));

$html_content = file_get_contents('..\html\posti.html');
$html_content = str_replace('{IDFILM}', $id_film, $html_content);
$html_content = str_replace('{TITOLO}', $titolo, $html_content);
$html_content = str_replace('{LOCANDINA}', $locandina, $html_content);
$html_content = str_replace('{DATA}', $data_film, $html_content);
$html_content = str_replace('{ORA}', $ora_formattata, $html_content);
$html_content = str_replace('{POSTI}', $output, $html_content);

echo $html_content;
?>