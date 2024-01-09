<?php
session_start();

$template_film = file_get_contents('../html/profilo.html');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $accedi_stringa = "Benvenuto " . $_SESSION['username'];
} else {
    header('Location: ../html/500.html');
    exit();
}
$template_film = str_replace('{ACCEDI}', $accedi_stringa, $template_film);
echo $template_film;
// Verifica se l'utente è loggato
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['logged_in'] = false;
    // Termina la sessione
    session_unset();
    session_destroy();

    // Reindirizza l'utente alla pagina di login o alla home
    header('Location: ../../index.php');

}

?>