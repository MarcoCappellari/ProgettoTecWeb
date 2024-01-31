<?php

require_once '../../queries/queries.php';
session_start();

$utente_errato="";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['utente']) || !isset($_POST['password'])) {
        header('Location: 404.php');
        exit();
    }

    $utente = $_POST['utente']; //potrebbe essere mail o username
    $password = $_POST['password'];

    $user = getUserByMailOrUsername($conn, $utente);
    $conn->close();
    if ($user === null) {
        $utente_errato="<p>Credenziali errate o password non corretta!</p>";
    } else {

        $mail = $user['mail'];

        if ($password == $user['password'] && $mail == $user['mail']) {
            $_SESSION['mail'] = $user['mail'];
            $_SESSION['logged_in'] = true;
            if($user['permessi']){
                header('Location: admin.php');
            } else{
                header('Location: profilo.php');
            }
            exit();
        } else { 
            $utente_errato="<p>Credenziali errate o password non corretta!</p>";
        }
    }
}

$template = file_get_contents('../html/accedi.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{UTENTE-ERRATO}', $utente_errato, $template);
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>