<?php

require_once '../../queries/queries.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['utente']) || !isset($_POST['password'])) {
        header('Location: ../html/404.html');
        exit();
    }

    $utente = $_POST['utente'];
    $password = $_POST['password'];

    $user = getUserByMailOrUsername($conn, $utente);

    if ($user === null) {
        echo "Utente non trovato.";
        exit();
    }

    if ($utente == $user['mail']) {
        $username = getUserByMail($conn, $utente);
    } else {
        $username = $user['username'];
    }

    $conn->close();

    if ($password == $user['password'] && $username == $user['username']) {
        echo "Credenziali corrette.";
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        exit();
    } else {
        echo "Credenziali errate o password non corretta.";
    }
}

?>