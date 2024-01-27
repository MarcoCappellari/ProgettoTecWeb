<?php

require_once '../../queries/queries.php';
session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$risultato_info='';


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
    if (!$conn->query($sql)) {
        header('Location: ../html/500.html');
        exit();
    }

    $id_film = $conn->insert_id;

    $sql2 = "INSERT INTO Classificazione(id_film, nome_genere) VALUES ('$id_film','$genere_primario')";
    if (!$conn->query($sql2)) {
        header('Location: ../html/500.html');
        exit();
    }

    if (isset($_POST['genere_secondario']) && !empty($_POST['genere_secondario'])) {
        $genere_secondario = $_POST['genere_secondario'];
        $sql3 = "INSERT INTO Classificazione(id_film, nome_genere) VALUES ('$id_film','$genere_secondario')";
        if (!$conn->query($sql3)) {
            header('Location: ../html/500.html');
            exit();
        }
    }

    $risultato_info='<p>Film inserito con SUCCESSO!</p>';




}

$generi = getGeneris($conn);
$conn->close();

$genere_info  = '';
while ($row = $generi->fetch_assoc()) {
    $genere_info .= '<option value="' . $row['nome'] . '">';
    $genere_info  .= $row['nome'];
    $genere_info  .= '</option>';
}

$generi->free();


$template = file_get_contents('../html/aggiungi_film.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{GENERE-OPZIONI}', $genere_info, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
echo $template;

?>
