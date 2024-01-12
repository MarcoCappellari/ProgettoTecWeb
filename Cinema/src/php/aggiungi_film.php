<?php

require_once '../../queries/queries.php';
error_reporting(E_ALL); //debug errori
ini_set('display_errors', 1);
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
    $trama_film = $_POST['trama'];
    $regista= $_POST['regista'];
    $durata = $_POST['durata'];
    $genere_primario = $_POST['genere_primario'];



    $file_name = $_FILES['locandina']['name'];
    $file_tmp = $_FILES['locandina']['tmp_name'];
    $file_size = $_FILES['locandina']['size'];
    $file_error = $_FILES['locandina']['error'];

    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid('locandina_', true) . '.' . $file_ext;
    $locandina_path = "../images/locandine/" . $new_file_name;
    move_uploaded_file($file_tmp, $locandina_path);

    $sql = "INSERT INTO Film (titolo, regista, locandina, durata, trama) VALUES ('$titolo' , '$regista' , '$locandina_path', '$durata' , '$trama_film')";
    $conn->query($sql);

    $id_film = $conn->insert_id;

    $sql2 = "INSERT INTO Classificazione(id_film, nome_genere) VALUES ('$id_film','$genere_primario')";
    $conn->query($sql2);

    if (isset($_POST['genere_secondario']) && !empty($_POST['genere_secondario'])) {
        $genere_secondario = $_POST['genere_secondario'];
        $sql3 = "INSERT INTO Classificazione(id_film, nome_genere) VALUES ('$id_film','$genere_secondario')";
        $conn->query($sql3);
    }




}

$generi = getGeneris($conn);
$conn->close();

$genere_info  = '';
while ($row = $generi->fetch_assoc()) {
    $genere_info .= '<option value="' . $row['nome'] . '">';
    $genere_info  .= $row['nome'];
    $genere_info  .= '</option>';
}


$template = file_get_contents('../html/aggiungi_film.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{GENERE-OPZIONI}', $genere_info, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
echo $template;

?>
