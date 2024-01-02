<?php
if (isset($_POST['posti_selezionati'])) {
    $posti_selezionati = $_POST['posti_selezionati'];
    echo "Hai prenotato i seguenti posti: ";
    foreach ($posti_selezionati as $posto) {
        echo htmlspecialchars($posto) . " ";
    }
}

?>
