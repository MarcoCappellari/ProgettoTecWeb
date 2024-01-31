<?php

require_once '../../queries/queries.php';
session_start();

$id_film = $_POST['idFilm'];
$data = $_POST['data'];
$ora = $_POST['ora'];
$sala = $_POST['sala'];
$id_sala = getIdSala($conn, $sala);
$posti = $_POST['posti'];

$id_proiezione = getIdProiezione($conn, $id_film, $ora, $id_sala);
$posti_selezionati_json = $_POST['posti'];
$posti_selezionati = json_decode($posti_selezionati_json, true);
$mail='';

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
    $mail = $_SESSION['mail'];
} else  {
    $mail = $_POST['email'];
    if($mail==''){
        header('Location: 500.php');
        exit();
    }
}

foreach ($posti_selezionati as $posto) {
    $posto_fila = $posto;
    $fila = preg_replace("/[^a-zA-Z]/", "", $posto_fila);
    $posto_numero = $posto;
    $num_posto = preg_replace("/[^0-9]/", "", $posto_numero);

    inserisciBiglietto($conn, $id_proiezione, $mail, $fila, $num_posto, $id_sala);
}

header('Location: biglietti.php');
exit();

?>