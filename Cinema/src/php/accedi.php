<?php

require_once '../../queries/queries.php';
session_start(); //serve per usare $_SESSION['username'] (avvio la sessione)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se i campi sono stati inviati
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header('Location: ../html/404.html');
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = getUserByMailOrUsername($conn, $username);
    $conn->close();
    // Verifica se l'utente esiste e la password corrisponde
    if ($password == $user['password'] && $username == $user['username']) {
        $_SESSION['username'] = $user['username'];
        echo "Credenziali corrette.";
        exit(); // Interrompi lo script dopo il reindirizzamento
    } else {
        echo "Credenziali errate o password non corretta.";
    }
}


?>