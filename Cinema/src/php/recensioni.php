<?php

require_once '../../queries/queries.php';

//gestione log utente e scrittura recensioni
session_start();
$recensioni_page = file_get_contents('../html/recensioni.html');
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $accedi_stringa = "Benvenuto " . $_SESSION['username'];
    if($_SESSION['conferma-recensione'] == true){
        $_SESSION['conferma-recensione'] = false;
        $conferma_recensione = "<p>Recensione inviata con successo!<br>
                                Vuoi scrivere un'altra recensione? <a href='recensioni.php'>Clicca qui</a>!</p>";
        $recensioni_page = str_replace('{SCRIVI-RECENSIONE}', $conferma_recensione, $recensioni_page);
    } else {
        $form_recensione = "<form action='scrivi_recensione.php' method='get' id='form-recensione'>
                                <textarea rows='10' cols='40' name='recensione' id='textarea-recensione'> Scrivi qui la tua recensione! </textarea>
                                <input type='submit' value='Invia' id='invia-recensione-button'>
                            </form>";
        $recensioni_page = str_replace('{SCRIVI-RECENSIONE}', $form_recensione, $recensioni_page);
    }
} else {
    $accedi_stringa = "<a href='../html/accedi.html'>Accedi</a>";
    $accedi_per_recensione =    "<p>Ops! Sembra che tu non abbia ancora effettuato l'accesso!<br>
                                Per lasciare una recensione devi prima aver effettuato l'accesso al tuo accout.<br>
                                Clicca qui per <a href='../html/accedi.html'>Accedere</a>.</p>";
    $recensioni_page = str_replace('{SCRIVI-RECENSIONE}', $accedi_per_recensione, $recensioni_page);
}
$recensioni_page = str_replace('{ACCEDI}', $accedi_stringa, $recensioni_page);

//gestione recensioni
$recensioni = getRecensioni($conn);
$conn->close();

$output_recensioni = '';

if(!$recensioni){
    $output_recensioni='<div class="div-non-esiste">
                            <div class="immagine-indisponibilità"></div>
                            <p>E\' ancora tutto molto tranquillo qui!</p>
                            <p>Condividi per primo la tua esperienza scrivendo una recensione. La tua opinione conta!</p>
                        <div>';
} else {
    while ($recensione = $recensioni->fetch_assoc()) {
        $output_recensioni.='<div class="recensione">
                                <p class="utente">'. $recensione['username'] .'</p>
                                <p class="data-recensione"><time datatime="'.$recensione['data'].'">'. $recensione['data'] .'</time></p>
                                <p class="content-recensione">"'. $recensione['content'] .'"</p> 
                            </div>';
    }
}

$recensioni_page = str_replace('{RECENSIONI}', $output_recensioni, $recensioni_page);

$footer = file_get_contents('../html/footer.html');
$recensioni_page = str_replace('{FOOTER}', $footer, $recensioni_page);

echo $recensioni_page;

?>