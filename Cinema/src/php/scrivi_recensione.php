<?php

require_once '../../queries/queries.php';

session_start();
$mail_utente = $_SESSION['mail'];
$content = clearInput($_GET['recensione']);

$output = writeRecensione($conn, $mail_utente, $content);

if(!$output){
    $_SESSION['conferma-recensione'] = true;
    header('Location: recensioni.php'); 
    exit();
} else {
    header('Location: 500.php'); 
    exit(); 
}

?>