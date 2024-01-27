<?php

require_once '../../queries/queries.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['utente']) || !isset($_POST['password'])) {
        header('Location: ../html/404.html');
        exit();
    }

    $utente = $_POST['utente']; //potrebbe essere mail o username
    $password = $_POST['password'];

    $user = getUserByMailOrUsername($conn, $utente);
    $conn->close();
    if ($user === null) {
        echo "Utente non trovato.";
        exit();
    }
    /*
        //dovrebbe essere inutile, basta $mail = $user['mail'];
        if ($utente == $user['mail']) {
            $mail = $user['mail'];
        } else {

            $mail = getUserByMail($conn, $utente);
        }
    */

    $mail = $user['mail'];

    if ($password == $user['password'] && $mail == $user['mail']) {
        echo "Credenziali corrette.";
        $_SESSION['mail'] = $user['mail'];
        //$_SESSION['mail'] = $mail; $user['mail'] posso sostituirlo
        $_SESSION['logged_in'] = true;
        header('Location: ../../index.php');
        exit();
    } else {
        echo "Credenziali errate o password non corretta.";
    }
}

?>