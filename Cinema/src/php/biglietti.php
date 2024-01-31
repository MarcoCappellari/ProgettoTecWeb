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
        $biglietto .= '<p><span class="header-info-biglietto">ID Biglietto:</span> ' . $row['id'] . '</p>';
        $biglietto .= '<p><span class="header-info-biglietto">Nome Film:</span> ' . $row['titolo'] . '</p>';
        $biglietto .= '<p><span class="header-info-biglietto">Data:</span> ' . $row['data'] . '</p>';
        $oraMinuti = substr($row['ora'], 0, 5);
        $biglietto .= '<p><span class="header-info-biglietto">Ora:</span> ' . $oraMinuti . '</p>';
        $biglietto .= '<hr>';
        $biglietto .= '<p><span class="header-info-biglietto">Sala: </span>' . $row['sala'] . '</p>';
        $biglietto .= '<p><span class="header-info-biglietto">Fila:</span> ' . $row['fila'] . '</p>';
        $biglietto .= '<p><span class="header-info-biglietto">Posto:</span> ' . $row['numero_posto'] . '</p>';
        $biglietto .= '</div>';
    }
}else{
    $biglietto = '<p id="nessun-biglietto">Nessun biglietto è presente!</p>';
}

$template = file_get_contents('../html/biglietti.html');
$footer = file_get_contents('../html/footer.html');

$template = str_replace('{ACCEDI}', $accedi_stringa, $template);
$template = str_replace('{BIGLIETTI}', $biglietto, $template);
$template = str_replace('{FOOTER}', $footer, $template);

echo $template;

?>