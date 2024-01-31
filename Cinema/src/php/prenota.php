<?php

require_once '../../queries/queries.php';
session_start();

include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$id_film = $_POST['idFilm'];
$ora_film = $_POST['ora'];
$data_film = $_POST['data'];

if (empty($id_film) || empty($ora_film) || empty($data_film) || absentProiection($conn, $id_film, $ora_film, $data_film)) {
    header('Location: 404.php');
    exit();
}

$FilmRow = getFilmByIdQuery($conn, $id_film); 
$Sala = getSalaByProiection($conn, $id_film, $ora_film, $data_film);


$titolo = $FilmRow['titolo'];
$nome_sala= $Sala['nome'];
$ora_formattata = date('H:i', strtotime($ora_film));

$risultato_info='';
$prenota='<form id="biglietto-recap-form" action="conferma_prenotazione.php" method="post">';
$registrazione = '';
$biglietto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['posti'])) {
        $posti_selezionati = $_POST['posti'];
        foreach ($posti_selezionati as $posto) {
            $biglietto .= "<div class='biglietto-recap'>";
            $biglietto .= "<p id='titolo-biglietto'>$titolo</p>";
            $biglietto .= "<div class='biglietto-recap-field'>";
            $biglietto .= "<p>$nome_sala</p>";
            $biglietto .= "<p>Poltrona: $posto</p>";
            $biglietto .= "</div>";
            $biglietto .= "<div class='biglietto-recap-field'>";
            $biglietto .= "<time datatime=“.$ora_formattata.”> $ora_formattata </time>";
            $biglietto .= "<time datatime=“.$data_film.”> $data_film </time>";
            $biglietto .= "</div>";
            $biglietto .= "</div>";
        }

        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $registrazione = '<h2>Prenota i biglietti come '.$_SESSION['mail'].'</h2>
                                <p>I biglietti che acquisterai verranno inviati alla precedente <span lang="en">mail<span></p>';

            $prenota .= '<input type="hidden" name="idFilm" value="' . $id_film . '">';
            $prenota .= '<input type="hidden" name="data" value="' . $data_film . '">';
            $prenota .= '<input type="hidden" name="ora" value="' . $ora_film . '">';
            $prenota .= '<input type="hidden" name="sala" value="' . $nome_sala . '">';
            $prenota .= '<input type="hidden" name="posti" value="'. htmlspecialchars(json_encode($posti_selezionati)) .'">';

        } else {
            $registrazione = '<h2>Acquista i biglietti senza registrarti</h2>';
            $prenota .= '
                <fieldset>
                <legend>Email</legend>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                </fieldset>';

            $accedi_stringa = '<a href="accedi.php">Accedi</a>';
        }
        $prenota .= '<input type="submit" name="submit_button" value="Conferma Prenotazione">
            </form>';


    } else {
        $prenota .= "<div class='immagine-indisponibilità'></div>
                    <div class='div-non-esiste'>
                        <p>Non hai selezionato alcun posto da prenotare! <br>
                        Per tornare alla home <a href='../../index.php'>Clicca qui</a></p>
                    </div>    
                </form>";
    }
}
$conn->close();

$ora_codificata = urlencode($ora_film);

$template = file_get_contents('../html/prenota.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
$template = str_replace('{BIGLIETTI}', $biglietto, $template);
$template = str_replace('{PRENOTA}', $prenota, $template);
$template = str_replace('{REGISTRAZIONE}', $registrazione, $template);

$template = str_replace('{IDFILM}', $id_film, $template);
$template = str_replace('{TITOLO}', $titolo, $template);
$template = str_replace('{DATA}', $data_film, $template);
$template = str_replace('{ORA}', $ora_codificata, $template);
$template = str_replace('{SALA}', $nome_sala, $template);

$stringa_footer= file_get_contents('../html/footer.html');
$template = str_replace('{FOOTER}', $stringa_footer, $template);
echo $template;

?>
