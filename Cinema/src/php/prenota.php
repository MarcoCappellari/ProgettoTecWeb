<?php

require_once '../../queries/queries.php';
session_start();

if (isset($_POST['posti'])) {
    $posti_selezionati = $_POST['posti'];
    foreach ($posti_selezionati as $posto) {
        echo $posto;
    }
} else {
    header('Location: ../html/404.html');
    exit;
}



$risultato_info='';
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $permessi = getPermessiByUsername($conn, $_SESSION['username']);
    if ($permessi == true) {
        $accedi_stringa = "<a href='admin.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    } else {
        $accedi_stringa = "<a href='profilo.php'>Benvenuto " . $_SESSION['username'] . "</a>";
    }
} else {
    $accedi_stringa = '<a href="src/html/accedi.html">Accedi</a>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film = $_POST['film'];
    $data = $_POST['data'];
    $ora = $_POST['ora'];
    $sala = $_POST['sala'];

    // Inserimento della nuova programmazione nel database
    $sql = "INSERT INTO Proiezione (id_film, id_sala, ora, data) VALUES ('$film', '$sala', '$ora', '$data')";
    $conn->query($sql);
    $risultato_info='<p>Proiezione inserita con SUCCESSO!</p>';
}

$films = getFilms($conn);
$sale = getSala($conn);
$conn->close();

$film_info = '';
while ($row = $films->fetch_assoc()) {
    $film_info .= '<option value="' . $row['id'] . '">';
    $film_info .= $row['titolo'];
    $film_info .= '</option>';
}

$sale_info = '';
while ($row = $sale->fetch_assoc()) {
    $sale_info .= '<option value="' . $row['id'] . '">';
    $sale_info .= $row['nome'];
    $sale_info .= '</option>';
}

$template = file_get_contents('../html/prenota.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
$template = str_replace('{POSTI}', "asdasd", $template);
echo $template;

?>
