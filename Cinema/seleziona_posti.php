<?php
// Connessione al database
require_once 'connessione_database.php';

$id_film = $_GET['idFilm']; 
$ora_film = $_GET['ora'];
$data_film = $_GET['data'];

$queryTitoloeLocandina = "SELECT Film.nome, Film.locandina 
                          FROM Film 
                          WHERE id = $id_film";
$resultTitoloeLocandina = $conn->query($queryTitoloeLocandina);

$row = $resultTitoloeLocandina->fetch_assoc();
if ($row) {
    $titolo = $row['nome'];
    $locandina = base64_encode($row['locandina']);
} else {
    echo "Nessun risultato trovato per l'ID del film: " . $idFilm;
    exit;  // Esci dallo script se non ci sono risultati
}

$query = "SELECT A.id_riproduzione,R.id_film,R.ora,R.data,P.fila,P.numero_posto,A.disponibile
          FROM 
          Assegnazione A
          JOIN 
          Riproduzione R ON A.id_riproduzione = R.id
          JOIN 
            Posto P ON A.fila = P.fila AND A.numero_posto = P.numero_posto AND A.id_sala = P.id_sala
        WHERE 
        R.id_film = $id_film AND
        R.ora = '$ora_film' AND
        R.data = '$data_film'";
$result = $conn->query($query);

$current_fila = ""; 
$output = '';

while ($row = $result->fetch_assoc()) {
    if ($current_fila != $row['fila']) {
        if (!empty($current_fila)) {
            $output .= "<br>";
        }
        $current_fila = $row['fila'];
        $output .= "<strong>Fila " . $current_fila . "</strong>: ";
    }

    $stato_posto = $row['disponibile'] ? "selezionabile" : "non-selezionabile";
    $output .= "<label class='$stato_posto' for='posto_" . $row['fila'] . $row['numero_posto'] . "'>";
    $output .= "<input type='checkbox' id='posto_" . $row['fila'] . $row['numero_posto'] . "' name='posti_selezionati[]' value='" . $row['fila'] . $row['numero_posto'] . "' " . ($row['disponibile'] ? '' : 'disabled') . ">";
    $output .= "</label> ";

    
}

$conn->close(); 


//$output .= "<input type='submit' value='Prenota'>";

// Combina il contenuto del file HTML e del file PHP
$ora_formattata = date('H:i', strtotime($ora_film));

$html_content = file_get_contents('posti.html');

$html_content = str_replace('{IDFILM}', $id_film, $html_content);
$html_content = str_replace('{TITOLO}', $titolo, $html_content);
$html_content = str_replace('{LOCANDINA}', $locandina, $html_content);
$html_content = str_replace('{DATA}', $data_film, $html_content);
$html_content = str_replace('{ORA}', $ora_formattata, $html_content);
$html_content = str_replace('{POSTI}', $output, $html_content);

echo $html_content;
?>
