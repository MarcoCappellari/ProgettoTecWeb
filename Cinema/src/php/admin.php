<?php
require_once '../../queries/queries.php';
session_start();

$admin_page = file_get_contents('../html/admin.html');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi = getPermessiByUsername($conn, $_SESSION['mail']);
    if ($permessi == true) {
        $accedi_stringa = "<a href='src/php/admin.php'>Area amministrativa</a>";
    } else {
        header('Location: ../html/500.html');
        exit();
    }
    $accedi_stringa = "Area amministrativa";
} else {
    header('Location: ../html/500.html');
    exit();
}
$conn->close();
$admin_page = str_replace('{ACCEDI}', $accedi_stringa, $admin_page);
echo $admin_page;
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