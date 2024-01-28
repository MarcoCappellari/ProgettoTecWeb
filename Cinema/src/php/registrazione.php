<?php

require_once '../../queries/queries.php';
session_start();

$risultato_info = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_cont = 0;
    $email = $_POST['email'];
    $username = $_POST['utente'];
    $password = $_POST['password'];
    $password_conferma = $_POST['password_conferma'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];

    $check_1 = getUserByMailOrUsername($conn, $username);
    if ($check_1 !== null) {
        $risultato_info =  "<p>Esiste gia' un utente con questo Username</p>";
        $check_cont = 1;
    }

    $check_2 = getUserByMailOrUsername($conn, $email);
    if ($check_1 !== null) {
        $risultato_info = "<p>Esiste gia' un utente con questo Email</p>";
        $check_cont = 1;
    }

    if($password !== $password_conferma ){
        $risultato_info =  "<p>Le password non corrispondono</p>";
        $check_cont = 1;
    }

    if($check_cont === 0){
        $sql = "INSERT INTO Utente (mail, username, nome, cognome, permessi, password) VALUES ('$email','$username','$nome','$cognome',0,'$password')";
        $conn->query($sql);
        $risultato_info='<p>Utente registrato con SUCCESSO!</p>';
    }
    $conn->close();
}

$template = file_get_contents('../html/registrazione.html');

$template = str_replace('{FEEDBACK}', $risultato_info, $template);
echo $template;

?>