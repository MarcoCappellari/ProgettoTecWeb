<?php

require_once '../../queries/queries.php';
session_start();

$risultato_info = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_cont = 0;
    $email = clearInput($_POST['email']);
    $username = clearInput($_POST['utente']);
    $password = clearInput($_POST['password']);
    $password_conferma = clearInput($_POST['password_conferma']);
    $nome = clearInput($_POST['nome']);
    $cognome = clearInput($_POST['cognome']);


    $check_1 = getUserByMailOrUsername($conn, $username);
    if ($check_1 !== null) {
        $risultato_info =  "<p class='signin-error'>Esiste gia' un utente con questo Username</p>";
        $check_cont = 1;
    }

    $check_2 = getUserByMailOrUsername($conn, $email);
    if ($check_1 !== null) {
        $risultato_info = "<p class='signin-error'>Esiste gia' un utente con questo Email</p>";
        $check_cont = 1;
    }

    if($password !== $password_conferma ){
        $risultato_info =  "<p class='signin-error'>Le password non corrispondono</p>";
        $check_cont = 1;
    }

    if($check_cont === 0){
        effettuaRegistrazione($conn, $email, $username, $nome, $cognome, $password);
        $risultato_info="<p class='signin-success'>Utente registrato con SUCCESSO!</p>";
    }
    $conn->close();
}

$template = file_get_contents('../html/registrazione.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{FEEDBACK}', $risultato_info, $template);
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>