<?php

require_once '../../queries/queries.php';
session_start();

include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$risultato_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film = $_POST['film'];
    $data = $_POST['data'];
    $ora = $_POST['ora'];
    $sala = $_POST['sala'];

    $oggi = date('Y-m-d');

    if ($data < $oggi) {
        $risultato_info = '<p>La data inserita è precedente a oggi.</p>';
    } else {
        $count_proiezione = verificaProiezione($conn, $sala, $data, $ora);
        if ($count_proiezione > 0) {
            $risultato_info = '<p>Esiste già una proiezione programmata per quell\'ora e quella sala.</p>';
        } else {
            $count_prec = verificaProiezioniPrecedenti($conn, $sala, $data, $ora);
            $count_succ = verificaProiezioniSuccessive($conn, $sala, $data, $ora);
    
            if ($count_prec > 0 || $count_succ > 0) {
                $risultato_info = '<p>Esiste già una proiezione programmata nelle 3 ore precedenti o successive.</p>';
            } else {
                inserisciProiezione($conn, $film, $sala, $ora, $data);
                $risultato_info = '<p>Proiezione inserita con SUCCESSO!</p>';
            }
        }
    }
}

$films = getFilms($conn);
$sale = getSala($conn);
$conn->close();

$film_info = '';
while ($row = $films->fetch_assoc()) {
    $film_info .= '<option value="' . $row['id'] . '">';
    $film_info .= $row['titolo'];
    $film_info .= '</option>';
}

$sale_info = '';
while ($row = $sale->fetch_assoc()) {
    $sale_info .= '<option value="' . $row['id'] . '">';
    $sale_info .= $row['nome'];
    $sale_info .= '</option>';
}

$template = file_get_contents('../html/aggiungi_proiezione.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{SALA-OPZIONI}', $sale_info, $template);
$template = str_replace('{FILM-OPZIONI}', $film_info, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
echo $template;

?>