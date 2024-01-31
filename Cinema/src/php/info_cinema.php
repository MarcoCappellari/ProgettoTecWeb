<?php

require_once '../../queries/queries.php';
session_start();
include 'user_session.php';
$accedi_stringa = gestisciAccesso($conn);

$result=getSalaAndSeats($conn);

$conn->close();

$stringa_info_sale = '';

if ($result==null) {
    $stringa_info_sale .= '<div class="div-non-esiste">';
    $stringa_info_sale .= '<div class="immagine-indisponibilità"></div>';
    $stringa_info_sale .= '<p>Ci dispiace, al momento le nostre sale non sono disponibili alle programmazioni.</p>
                           <p> Torna presto per scoprire le ultime novità!</p>';
    $stringa_info_sale .= '</div>';
} else {
    $stringa_info_sale .= ' <div class="table-container">
                                <table aria-describedby="desc-sale">
                                <caption>Sale PopCorn Cinema</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Sala</th>
                                        <th scope="col" abbr="n posti">Numero Posti</th>
                                        <th scope="col">Tecnologia video</th>
                                        <th scope="col">Tecnologia audio</th>
                                    </tr>
                                <tbody>';

    foreach ($result as $row) {
        $stringa_info_sale .=  '<tr>
                                    <th scope="row">' . $row['NomeSala'] . '</th>
                                    <td>' . $row['NumPosti'] .'</td>
                                    <td>' .$row['TecVideo'] .'</td>
                                    <td><span lang="en">' .$row['TecAudio'] .'</span></td>
                                </tr>'; 
    }
    $stringa_info_sale .=      '</tbody>
                                </table>
                            </div>';
}

    $template_info_cinema = file_get_contents('../html/info_cinema.html');
    $template_info_cinema = str_replace('{SALE}', $stringa_info_sale, $template_info_cinema);
    $template_info_cinema = str_replace('{ACCEDI}', $accedi_stringa, $template_info_cinema);
    $stringa_footer= file_get_contents('../html/footer.html');
    $template_info_cinema = str_replace('{FOOTER}', $stringa_footer, $template_info_cinema);

    echo $template_info_cinema;
?>