<?php

require_once '../../queries/queries.php';
session_start();

include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$result = getBigliettoByUser($conn, $_SESSION['mail']);
$conn->close();

$biglietto = '';
if ($result!=null){
    while ($row = $result->fetch_assoc()) {
        $biglietto .= '<div class="biglietto">';
        $biglietto .= '<p><span class="bold-text">ID Biglietto:</span> ' . $row['id'] . '</p>';
        $biglietto .= '<p><span class="bold-text">Nome Film:</span> ' . $row['titolo'] . '</p>';
        $biglietto .= '<p><span class="bold-text">Data:</span> ' . $row['data'] . '</p>';
        $oraMinuti = substr($row['ora'], 0, 5);
        $biglietto .= '<p><span class="bold-text">Ora:</span> ' . $oraMinuti . '</p>';
        $biglietto .= '<hr>';
        $biglietto .= '<p><span class="bold-text">Sala: </span>' . $row['sala'] . '</p>';
        $biglietto .= '<p><span class="bold-text">Fila:</span> ' . $row['fila'] . '</p>';
        $biglietto .= '<p><span class="bold-text">Posto:</span> ' . $row['numero_posto'] . '</p>';
        $biglietto .= '</div>';
    }
}else{
    $biglietto = '<p id="nessun-biglietto">Nessun biglietto è presente!</p>';
}
$template = file_get_contents('../html/biglietti.html');
$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{BIGLIETTI}', $biglietto, $template);

echo $template;

?>