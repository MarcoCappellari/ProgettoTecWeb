<?php

require_once '../../queries/queries.php';

session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$risultato = '';
$utente = getUserByMailOrUsername($conn, $_SESSION['mail']);
if ($utente) {
    $mail = $utente['mail'];
    $username = $utente['username'];
    $nome = $utente['nome'];
    $cognome = $utente['cognome'];
    $password = $utente['password'];
} else {
    header('Location: ../html/accedi.html');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mail = $_POST['mail'];
    $username = $_POST['username'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $nuova_password = $_POST['password'];
    $conferma_password = $_POST['confirm_password'];

    if (!empty($nuova_password) && $nuova_password === $conferma_password) {
        $password = $nuova_password ? $_POST['password'] : $password;
    }

    $update_user = updateUserInfo($conn, $mail, $username, $nome, $cognome, $password); 
    if($update_user){
        $risultato = "Dati aggiornati correttamente!";
    }else{
        $risultato = "Errore nell'aggiornamento dei dati!";
    }
}
//$risultato = $_SESSION['mail'];
$template = file_get_contents('../html/modifica_utente.html');
$template = str_replace('{MAIL}', $mail, $template);
$template = str_replace('{USERNAME}', $username, $template);
$template = str_replace('{NOME}', $nome, $template);
$template = str_replace('{COGNOME}', $cognome, $template);
$template = str_replace('{RISULTATO}', $risultato, $template);
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
echo $template;
?>