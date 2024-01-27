<?php
require_once '../../queries/queries.php';

session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);


$film_info = '';
$risultato = '';
$primogenere = null;
$secondogenere = null;
$titolo = '';
$locandina = '';
$trama = '';
$regista = '';
$durata = '';
$showSecondForm = true;
$film_id = '';

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["form_type"]) && $_POST["form_type"] == "form1") {
    $film_id = $_POST["film_selezionato"];

    $generiFilm = getGenereById($conn, $film_id); //restituisce tutti i generi di un film
    $filmInfo = getFilmByIdQuery($conn, $film_id); //restituisce tutti i dati del film
    //generi del film
    if ($generiFilm) {
        $row = $generiFilm->fetch_assoc();
        $primogenere = $row['nome_genere'];
        if ($row = mysqli_fetch_assoc($generiFilm)) {
            $secondogenere = $row['nome_genere'];
        }
    }
    //informazioni del film
    $titolo = $filmInfo['titolo'];
    $regista = $filmInfo['regista'];
    $durata = $filmInfo['durata'];
    $locandina = $filmInfo['locandina'];
    $trama = $filmInfo['trama'];
    $showSecondForm = false;
}

$films = getFilms($conn); 
$generi = getGeneris($conn); 

//tutti i totoli del film
while ($row = $films->fetch_assoc()) {
    $film_info .= '<option value="' . $row['id'] . '">';
    $film_info .= $row['titolo'];
    $film_info .= '</option>';
}
//GENERE1 tutti i generi dei film + seleziono quello che avevo già assegnato
$genere_primo = '';
while ($row = $generi->fetch_assoc()) {
    $genere_primo .= '<option value="' . $row['nome'] . '"';
    if ($row['nome'] == $primogenere) {
        $genere_primo .= ' selected';
    }
    $genere_primo .= '>';
    $genere_primo .= $row['nome'];
    $genere_primo .= '</option>';
}
//GENERE2 tutti i generi dei film + seleziono quello che avevo già assegnato
$generi->data_seek(0);
$genere_secondo = '';
while ($row = $generi->fetch_assoc()) {
    $genere_secondo .= '<option value="' . $row['nome'] . '"';
    if ($row['nome'] == $secondogenere) {
        $genere_secondo .= ' selected';
    }
    $genere_secondo .= '>';
    $genere_secondo .= $row['nome'];
    $genere_secondo .= '</option>';
}

//SECONDO FORM
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["form_type"]) && $_POST["form_type"] == "form2") {

    $film_id = $_POST["film_id"];
    $titolo = $_POST["titolo"];
    $locandina_path = $_POST["locandina_path"];
    $locandina = $_POST["locandina"];
    $trama = $_POST["trama"];
    $regista = $_POST["regista"];
    $durata = $_POST["durata"];
    $genere_primario = $_POST["genere_primario"];
    $genere_secondario = $_POST["genere_secondario"];

    if (empty($_POST["locandina"])) {
        $locandina = $_POST["locandina_path"];
    } else {
        $locandina = $_POST["locandina"];
        $locandina = "src/images/locandine/" . $locandina;
    }

    if (isset($_POST["aggiorna_film"])) {
        // Aggiorna i dati del film nella tabella Film
        updateFilm($conn, $titolo, $locandina, $trama, $regista, $durata, $film_id);
        updateGeneri($conn, $film_id, $genere_primario, $genere_secondario);
        $risultato = "<p>Il film <span class='bold-text'>'" . $titolo . "' </span> è stato AGGIORNATO correttamente!</p>";
    } elseif (isset($_POST["elimina_film"])) {
        deleteFilm($conn, $film_id);
        $risultato = "<p>Il film <span class='bold-text'>'" . $titolo . "' </span> è stato ELIMINATO correttamente!</p>";
    }

    $conn->close();
    $showSecondForm = true;

}

$template = file_get_contents('../html/modifica_film.html');
$template = str_replace('{ID_FILM}', $film_id, $template);
$template = str_replace('{TITOLO}', $titolo, $template);
$template = str_replace('{LOCANDINA}', $locandina, $template);
$template = str_replace('{TRAMA}', "$trama", $template);
$template = str_replace('{REGISTA}', $regista, $template);
$template = str_replace('{DURATA}', $durata, $template);
$template = str_replace('{GENERE1}', $genere_primo, $template);
$template = str_replace('{GENERE2}', $genere_secondo, $template);
$template = str_replace('{FILM-OPZIONI}', $film_info, $template);
$template = str_replace('{SHOW_SECOND_FORM}', $showSecondForm ? 'hidden' : '', $template);
$template = str_replace('{RISULTATO}', $risultato, $template);
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
echo $template;

?>