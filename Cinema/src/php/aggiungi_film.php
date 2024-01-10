<?php

require_once '../../queries/queries.php';
session_start();
$risultato_info='';
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi = getPermessiByUsername($conn, $_SESSION['username']);
    if ($permessi == true) {
        $accedi_stringa = "<a href='admin.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    } else {
        $accedi_stringa = "<a href='profilo.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }
} else {
    $accedi_stringa = '<a href="../html/accedi.html">Accedi</a>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'];
    $locandina = $_POST['locandina'];
    $trama_film = $_POST['trama'];
    $regista= $_POST['regista'];
    $durata = $_POST['durata'];
    $genere_primario = $_POST['genere1'];
    $genere_secondario = $_POST['genere2'];

    echo '<script>console.log("' . $titolo . '");</script>';
    // Inserimento del nuovo film nel database
    $sql = "INSERT INTO Film (nome, regista, locandina, durata, trama) VALUES ('$titolo' , '$regista' , '$durata' ,$locandina, '$trama_film')";
    $conn->query($sql);


}

$generi = getGeneris($conn);
$conn->close();

$genere_info  = '';
while ($row = $generi->fetch_assoc()) {
    $genere_info .= '<option value="' . $row['id'] . '">';
    $genere_info  .= $row['nome_genere'];
    $genere_info  .= '</option>';
}


$template = file_get_contents('../html/aggiungi_film.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{GENERE-OPZIONI}', $genere_info, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
echo $template;

?>
