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

    //dovrebbe essere inutile, basta $username = $user['username'];
    if ($utente == $user['mail']) {
        $username = getUserByMail($conn, $utente);
    } else {
        $username = $user['username'];
    }

    $conn->close();

    if ($password == $user['password'] && $username == $user['username']) {
        echo "Credenziali corrette.";
        $_SESSION['mail-utente']= $user['mail'];
        $_SESSION['username'] = $username; //$user['username'] posso sostituirlo
        $_SESSION['logged_in'] = true;
        header('Location: ../../index.php');
        exit();
    } else {
        echo "Credenziali errate o password non corretta.";
    }
}

?>