<?php
require_once '../../queries/queries.php';

session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$risultato='';
if (isset($_POST['elimina_proiezioni'])) {
    $proiezioni_selezionate = $_POST['proiezioni_selezionate'];

    foreach ($proiezioni_selezionate as $proiezione_id) {

        // Prima elimina i biglietti associati alla proiezione
        $sql_delete_biglietti = "DELETE FROM Biglietto WHERE id_proiezione = $proiezione_id";
        $conn->query($sql_delete_biglietti);
        // Esegui la query per eliminare la proiezione
        $sql_delete_proiezione = "DELETE FROM Proiezione WHERE id = $proiezione_id";
        $conn->query($sql_delete_proiezione);
    }
    $risultato.="<p>La proiezione è stata eliminata!</p>";
}

$result_proiezioni = getProiezioni($conn);
$conn->close();
// Variabile per tenere traccia del titolo corrente
if ($result_proiezioni) {
    $current_title = null;
    $tutte_proiezioni = '';
    // Mostra tutte le proiezioni divise per film

    while ($row = $result_proiezioni->fetch_assoc()) {
        if ($current_title !== $row['titolo']) {
            // Se il titolo è cambiato, mostra il nuovo titolo
            $tutte_proiezioni .= "<br/><h3>" . $row['titolo'] . "</h3>";
            $current_title = $row['titolo'];
        }

        $oraFormattata = date('H:i', strtotime($row['ora']));
        $tutte_proiezioni .= "<input type='checkbox' name='proiezioni_selezionate[]' value='" . $row['id'] . "'>";
        $tutte_proiezioni .= "<label for='proiezioni_selezionate[]'>";
        $tutte_proiezioni .= "<span class='film-data'>" . $row['data'] . "</span>";
        $tutte_proiezioni .= "<span class='film-ora'>" . $oraFormattata . "</span>";
        $tutte_proiezioni .= "</label>";
        $tutte_proiezioni .= "</br>";
    }

    $tutte_proiezioni .= "<input type='submit' name='elimina_proiezioni' value='Elimina'>";
} else {
    $tutte_proiezioni = "<p>Non è presente nessuna proiezione. Se desidera aggiungerne una, clicca sul seguente link.</p>";
    $tutte_proiezioni .= "<a href='aggiungi_proiezione.php'>Aggiungi proiezioni</a>";
    $risultato='';
}

$template = file_get_contents('../html/elimina_proiezione.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{PROIEZIONI}', $tutte_proiezioni, $template);
$template = str_replace('{RISULTATO}', $risultato, $template);
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>