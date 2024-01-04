<?php

require_once '../../queries/queries.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header('Location: ../html/404.html');
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = getUserByMailOrUsername($conn, $username);

    $conn->close();

    if ($user === null) {
        echo "Utente non trovato.";
        exit();
    }

    if ($password == $user['password'] && ($username == $user['username'] || $username == $user['mail'])) {
        echo "Credenziali corrette.";
        exit();
    } else {
        echo "Credenziali errate o password non corretta.";
    }
}

?>
