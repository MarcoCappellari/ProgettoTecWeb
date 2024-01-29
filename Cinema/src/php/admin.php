<?php

require_once '../../queries/queries.php';

session_start();

$admin_page = file_get_contents('../html/admin.html');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi = getPermessiByUsername($conn, $_SESSION['mail']);
    if ($permessi == true) {
        $template = file_get_contents('../html/admin.html');
        $footer = file_get_contents('../html/footer.html');

        $template = str_replace('{FOOTER}', $footer, $template);
        echo $template;
    } else {
        header('Location: 500.php');
        exit();
    }    
} else {
    header('Location: 500.php');
    exit();
}
$conn->close();

// Gestione Logout
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['logged_in'] = false;
    session_unset();
    session_destroy();

    // Reindirizza l'utente alla pagina di login o alla home
    header('Location: Accedi.php');
    exit();
}

?>