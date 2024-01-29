<?php

require_once '../../queries/queries.php';

session_start();
// Gestione Logout
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['logged_in'] = false;
    session_unset();
    session_destroy();

    header('Location: accedi.php');
    exit();
}

$template = file_get_contents('../html/profilo.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{FOOTER}', $footer, $template);
echo $template;

?>