<?php
function gestisciAccesso($conn) {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $permessi = getPermessiByUsername($conn, $_SESSION['mail']);
        if ($permessi == true) {
            $accedi_stringa = "<a href='admin.php'>Area amministrativa</a>";
        } else {
            $accedi_stringa = "<a href='profilo.php'>Area personale</a>";
        }
    } else {
        $accedi_stringa = '<a href="../html/accedi.html">Accedi</a>';
    }
    return $accedi_stringa;
}
?>
