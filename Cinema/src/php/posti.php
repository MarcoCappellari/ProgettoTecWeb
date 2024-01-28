<?php

require_once '../../queries/queries.php';
session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

// Recupero i valori dai parametri GET
$id_film = $_GET['idFilm'];
$ora_film = $_GET['ora'];
$data_film = $_GET['data'];

// Verifico ERROR 404
if (empty($id_film) || empty($ora_film) || empty($data_film) || absentProiection($conn, $id_film, $ora_film, $data_film)) {
    header('Location: ../html/404.html');
    exit();
}

$FilmRow = getFilmByIdQuery($conn, $id_film);
$SeatResults = getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film);
$Sala = getSalaByProiection($conn, $id_film, $ora_film, $data_film);
$conn->close();

$titolo = $FilmRow['titolo'];
$locandina = $FilmRow['locandina'];
$nome_sala= $Sala['nome'];

$current_fila = "";
$output = '';

$input = '';
$input .= '<input type="hidden" name="idFilm" value="' . $id_film . '">';
$input .= '<input type="hidden" name="data" value="' . $data_film . '">';
$input .= '<input type="hidden" name="ora" value="' . $ora_film . '">';

while ($row = $SeatResults->fetch_assoc()) {
    //Aggiornamento fila corrente
    if ($current_fila != $row['fila']) {
        if (!empty($current_fila)) {
            $output .= "</ul>";
        }
        $current_fila = $row['fila'];
        $output .= "<ul class='fila-posti'>";
    }

    $stato_posto = $row['disponibile'] ? "disponibile" : "occupato";
    $output .= "<li aria-label='posto". $row['fila'].$row['numero']."'>";

    $output .= "<input type='checkbox' class='hidden-checkbox' id='posto_" . $row['fila'] . $row['numero'] . "' name='posti[]' value='" . $row['fila'] . $row['numero'] . "' " . ($row['disponibile'] ? '' : 'disabled') . ">";
    $output .= "<label for='posto_" . $row['fila'] . $row['numero'] . "' class='label-posto'>" . $row['fila'] . $row['numero'] . "</label>";

    $output .= "</li>";
}

if (!empty($current_fila)) {
    $output .= "</ul>";
}

$SeatResults->free();

$ora_formattata = date('H:i', strtotime($ora_film));

$html_content = file_get_contents('../html/posti.html');

$html_content = str_replace('{IDFILM}', $id_film, $html_content);
$html_content = str_replace('{TITOLO}', $titolo, $html_content);
$html_content = str_replace('{LOCANDINA}', $locandina, $html_content);
$html_content = str_replace('{DATA}', $data_film, $html_content);
$html_content = str_replace('{ORA}', $ora_formattata, $html_content);
$html_content = str_replace('{SALA}', $nome_sala, $html_content);
$html_content = str_replace('{POSTI}', $output, $html_content);

$html_content = str_replace('{INPUT}', $input, $html_content);
$html_content = str_replace('{ACCEDI}', $accedi_stringa, $html_content);
$footer_html = file_get_contents('../html/footer.html');

$html_content = str_replace('{FOOTER}', $footer_html, $html_content);

echo $html_content;
?>