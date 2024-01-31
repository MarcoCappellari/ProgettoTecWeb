<?php
require_once '../../queries/queries.php';

session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);
$film_info = '';
$risultato='';
$tutte_proiezioni='';
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["form_type"]) && $_POST["form_type"] == "form1"){
    $film_id = $_POST["film_selezionato"];
    $film_data = getFilmByIdQuery($conn, $film_id);
    $film_titolo = $film_data['titolo'];
    $result_proiezioni = getProiezioniFilm($conn , $film_id);

    if ($result_proiezioni) {
        $tutte_proiezioni = '<form method="post" action="../php/elimina_proiezione.php">
                            <h2>Seleziona le proiezioni da eliminare per <cite>'.$film_titolo.'</cite></h2>';

        while ($row = $result_proiezioni->fetch_assoc()) {
            $oraFormattata = date('H:i', strtotime($row['ora']));
            $tutte_proiezioni .= "<input type='checkbox' name='proiezioni_selezionate[]' value='" . $row['id'] . "'>";
            $tutte_proiezioni .= "<label for='proiezioni_selezionate[]'>";
            $tutte_proiezioni .= "<span class='film-data'>" . $row['id_sala'] . "</span>";
            $tutte_proiezioni .= "<span class='film-data'>" . $row['data'] . "</span>";
            $tutte_proiezioni .= "<span class='film-ora'>" . $oraFormattata . "</span>";
            $tutte_proiezioni .= "</label>";
            $tutte_proiezioni .= "</br>";
        }

        $tutte_proiezioni .= "<input type='submit' name='elimina_proiezioni' value='Elimina'>
        </form>";
    } else {
        $tutte_proiezioni = "<p>Non è presente nessuna proiezione. Se desidera aggiungerne una, clicca sul seguente link.</p>";
        $tutte_proiezioni .= "<a href='aggiungi_proiezione.php'>Aggiungi Proiezioni</a>";
        $risultato='';
    }

}
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
$films = getFilms($conn);

$conn->close();

while ($row = $films->fetch_assoc()) {
    $film_info .= '<option value="' . $row['id'] . '">';
    $film_info .= $row['titolo'];
    $film_info .= '</option>';
}

$template = file_get_contents('../html/elimina_proiezione.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{PROIEZIONI}', $tutte_proiezioni, $template);
$template = str_replace('{FILM-OPZIONI}', $film_info, $template);
$template = str_replace('{RISULTATO}', $risultato, $template);
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>