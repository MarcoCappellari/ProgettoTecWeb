<?php

require_once '../../queries/queries.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_film = $_POST['idFilm'];
$ora_film = $_POST['ora'];
$data_film = $_POST['data'];

// Verifico ERROR 404
if (empty($id_film) || empty($ora_film) || empty($data_film) || absentProiection($conn, $id_film, $ora_film, $data_film)) {
    header('Location: ../html/404.html');
    exit();
}

$FilmRow = getFilmByIdQuery($conn, $id_film);
$Sala = getSalaByProiection($conn, $id_film, $ora_film, $data_film);
$conn->close();

$titolo = $FilmRow['titolo'];
$nome_sala= $Sala['nome'];
$ora_formattata = date('H:i', strtotime($ora_film));

$risultato_info='';
$prenota='<form id="biglietto-recap-form" action="./php/pagamento.php" method="post">';
$registrazione = '';
$biglietto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['posti'])) {
        $posti_selezionati = $_POST['posti'];
        foreach ($posti_selezionati as $posto) {
            $biglietto .= "<div class='biglietto-recap'>";
            $biglietto .= "<h2>$titolo</h2>";
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
            //$permessi = getPermessiByUsername($conn, $_SESSION['username']);
            $permessi = false;
            if ($permessi == true) {
                $accedi_stringa = "<a href='admin.php'>Benvenuto " . $_SESSION['username'] . "</a>";
            } else {
                $accedi_stringa = "<a href='profilo.php'>Benvenuto " . $_SESSION['username'] . "</a>";
            }
            $registrazione = '<h2>Acquista i biglietti come '.$_SESSION['username'].'</h2>';

            $prenota .= '<input type="hidden" name="idFilm" value="' . $id_film . '">';
            $prenota .= '<input type="hidden" name="data" value="' . $data_film . '">';
            $prenota .= '<input type="hidden" name="ora" value="' . $ora_film . '">';
            $prenota .= '<input type="hidden" name="sala" value="' . $nome_sala . '">';
            $prenota .= '<input type="hidden" name="posti" value="' . $posti_selezionati . '">';

        } else {
            $registrazione = '<h2>Acquista i biglietti senza registrarti</h2>';
            $prenota .= '
                <fieldset>
                <legend>Email</legend>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                </fieldset>';

            $accedi_stringa = '<a href="../html/accedi.html">Accedi</a>';
        }
        $prenota .= '<input type="submit" name="submit_button" value="Vai al pagamento">
            </form>';


    } else {
        header('Location: ../html/404.html');
        exit;
    }
}


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
