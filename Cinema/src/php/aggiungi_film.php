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
    $accedi_stringa = '<a href="src/html/accedi.html">Accedi</a>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film = $_POST['film'];
    $data = $_POST['data'];
    $ora = $_POST['ora'];
    $sala = $_POST['sala'];

    // Inserimento della nuova programmazione nel database
    $sql = "INSERT INTO Riproduzione (id_film, ora, data) VALUES ('$film', '$ora', '$data')";
    $conn->query($sql);

    // Ottieni l'ID auto-incremento dell'ultimo inserimento
    $idRiproduzione = $conn->insert_id;

    // Itera sui posti della sala e inserisci nella tabella Assegnazione
    $sql = "SELECT fila, numero_posto FROM Posto WHERE id_sala = $sala";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $fila = $row['fila'];
            $numero_posto = $row['numero_posto'];

            $insertQuery = "INSERT INTO Assegnazione (id_riproduzione, fila, numero_posto, disponibile, id_sala)
                            VALUES ($idRiproduzione, '$fila', $numero_posto, 1, $sala)";
            $conn->query($insertQuery);
            $risultato_info='<p>Proiezione inserita con successo!</p>';
        }
    }
}

$films = getFilms($conn);
$sale = getSala($conn);
$conn->close();

$film_info = '';
while ($row = $films->fetch_assoc()) {
    $film_info .= '<option value="' . $row['id'] . '">';
    $film_info .= $row['nome'];
    $film_info .= '</option>';
}

$sale_info = '';
while ($row = $sale->fetch_assoc()) {
    $sale_info .= '<option value="' . $row['id'] . '">';
    $sale_info .= $row['nome'];
    $sale_info .= '</option>';
}

$template = file_get_contents('../html/aggiungi_film.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{SALA-OPZIONI}', $sale_info, $template);
$template = str_replace('{FILM-OPZIONI}', $film_info, $template);
$template = str_replace('{RISULTATO}', $risultato_info, $template);
echo $template;

?>
