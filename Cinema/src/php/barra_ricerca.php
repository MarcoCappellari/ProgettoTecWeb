<?php
// Connessione al database (sostituisci con le tue credenziali)
require_once '../../queries/queries.php';

session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi=getPermessiByUsername($conn, $_SESSION['username']);
    if($permessi==True){
        $accedi_stringa = "<a href='admin.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }else{
        $accedi_stringa = "<a href='profilo.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }
} else {
    $accedi_stringa = '<a href="src/html/accedi.html">Accedi</a>';
}

if(isset($_GET['film_name'])) {
    $film_name = $_GET['film_name'];
    $result = getFilmByName($conn, $film_name);
    $conn->close();
}

if ($film_name === '') {
    $result = null;
}

// Elabora i risultati
if ($result) {
    $film_id = $result['id'];
    header("Location: info_film.php?film=$film_id");
    //exit();
} else {
    $html_content = file_get_contents('../html/barra_ricerca.html'); //linux
    $html_content = str_replace('{ACCEDI}', $accedi_stringa, $html_content);
    $html_content = str_replace('{NOMEFILM}', $film_name, $html_content);
    echo $html_content;

}

?>