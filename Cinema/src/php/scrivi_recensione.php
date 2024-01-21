<?php

require_once '../../queries/queries.php';

session_start();

$mail_utente = $_SESSION['mail-utente'];
$content = $_GET['recensione'];

$output = writeRecensione($conn, $mail_utente, $content);

if(!$output){
    $_SESSION['conferma-recensione'] = true;
    header('Location: recensioni.php'); 
    exit();
} else {
    header('Location: ../html/500.html'); 
    exit(); 
}

?>