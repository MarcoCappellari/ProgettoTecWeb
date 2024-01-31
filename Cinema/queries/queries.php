<?php

require_once 'connessione_database.php';

function clearINput($value){
    $value = trim($value);
    $value = strip_tags($value);
    return htmlentities($value);
}

function getFilms($conn){
    $sql = "SELECT id, titolo, locandina FROM Film";
    $result = $conn->query($sql);

    if (!$result) {
        header('Location: ../php/500.php');
        exit();
    }

    return $result;
}

function getFilmByIdQuery($conn, $idFilm)
{
    $queryFilm = "SELECT * FROM Film WHERE id=?";

    $stmt = $conn->prepare($queryFilm);
    $stmt->bind_param("i", $idFilm);
    $stmt->execute();

    $resultFilm = $stmt->get_result();
    $stmt->close();
    if ($resultFilm && $resultFilm->num_rows > 0) {
        return $resultFilm->fetch_assoc();
    } else {
        header('Location: ../php/404.php');
        exit;
    }
}


function getFilmGenresById($conn, $idFilm){

    $queryGeneri = "SELECT Classificazione.nome_genere
                    FROM Classificazione
                    WHERE Classificazione.id_film = ?";


    $stmt = $conn->prepare($queryGeneri);
    $stmt->bind_param("i", $idFilm);
    $stmt->execute();

    $resultGeneri = $stmt->get_result();
    $stmt->close();
    $generi = "";

    if ($resultGeneri->num_rows > 0) {
        while ($rowGeneri = $resultGeneri->fetch_assoc()) {
            $generi .= $rowGeneri['nome_genere'] . ', ';
        }
        return rtrim($generi, ', ');
    } else {
        return null;
    }
}

function getOrariByFilmId($conn, $idFilm){

    $query = "SELECT Proiezione.data, Proiezione.ora
              FROM Proiezione
              WHERE Proiezione.id_film = ?
              ORDER BY Proiezione.ora";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idFilm);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    if (!$result) {
        return null;
    }

    $orari = array();

    while ($row = $result->fetch_assoc()) {
        $data = $row['data'];
        $ora = $row['ora'];
        $orari[$data][] = $ora;
    }
    return $orari;
}

function getFilmByName($conn, $film_name)
{
    $film_name = $conn->real_escape_string($film_name);

    $query = "SELECT * FROM Film WHERE titolo LIKE '%$film_name%'";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function absentProiection($conn, $id_film, $ora_film, $data_film){

    $query = "SELECT *
            FROM Proiezione AS P
            WHERE P.id_film= ? AND P.ora = ? AND P.data = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $id_film , $ora_film , $data_film);
    $stmt->execute();



    if (!($result = $stmt->get_result())) {
        $stmt->close();
        header('Location: ../php/500.php');
        exit();
    }
    $stmt->close();
    if ($result->num_rows == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//Restituisce la sala di una proiezione
function getSalaByProiection($conn, $id_film, $ora_film, $data_film){

    $query = "SELECT S.nome AS nome
            FROM Proiezione AS P
            JOIN Sala AS S ON P.id_sala = S.id
            WHERE P.id_film= ? AND P.ora = ? AND P.data = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $id_film , $ora_film , $data_film);
    $stmt->execute();

    if (!($result = $stmt->get_result())) {
        header('Location: ../php/500.php');
        $stmt->close();
        exit();
    }

    if ($result->num_rows == 0) {
        header('Location: ../php/500.php');
        $stmt->close();
        exit();
    }
    $stmt->close();
    return $result->fetch_assoc();
}

//Restituisce i posti di una proiezione identificati da FILA | NUMERO | DISPONIBILE
function getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film){

    $query = "SELECT Po.fila AS fila, 
                    Po.numero_posto AS numero, 
                    CASE WHEN B.id IS NOT NULL THEN FALSE ELSE TRUE END AS disponibile
            FROM 
                Proiezione AS P
            JOIN 
                Posto AS Po ON Po.id_sala = P.id_sala
            LEFT JOIN
                Biglietto AS B ON B.id_proiezione = P.id AND B.fila = Po.fila AND B.numero_posto = Po.numero_posto AND B.id_sala = P.id_sala
            WHERE P.id_film= ? AND P.ora = ? AND P.data = ?
            ORDER BY Po.fila ASC, Po.numero_posto ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $id_film , $ora_film , $data_film);
    $stmt->execute();


    if (!$result = $stmt->get_result()) {
        header('Location: ../php/500.php');
        $stmt->close();
        exit();
    }

    if ($result->num_rows == 0) {
        header('Location: ../php/500.php');
        $stmt->close();
        exit();
    }
    $stmt->close();
    return $result;
}

//restituisce tutta la linea della tabella Utente contentente l'user per l'username OR mail
function getUserByMailOrUsername($conn, $user){
    $query = "SELECT * 
                FROM Utente 
                WHERE username = ? OR mail = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $user , $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        header('Location: ../php/500.php');
        exit();
    }

    if($result->num_rows == 0){
        return null;
    }
    $stmt->close();
    return $result->fetch_assoc();

}

function getUserByMail($conn, $user)
{
    $query = "SELECT * FROM Utente WHERE mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    return $user['username'];
}

//restituisce il permesso dell'utente a partire dalla mail dell'username
function getPermessiByUsername($conn, $user){
    $query = "SELECT U.permessi 
            FROM Utente AS U 
            WHERE U.mail = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: ../php/500.php');
        exit();
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    return (bool) $row['permessi'];
}

function getSala($conn){
    $query = "SELECT * FROM Sala";
    $result = $conn->query($query);

    if (!$result) {
        header('Location: ../php/500.php');
        exit();
    }

    return $result;
}

function getSalaAndSeats($conn)
{
    $query = "SELECT S.nome AS NomeSala, COUNT(P.numero_posto) AS NumPosti, S.TecVideo, S.TecAudio
            FROM Sala AS S JOIN Posto AS P ON S.id=P.id_sala
            GROUP BY S.nome";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return null;
    }
}

function getBigliettoByUser($conn, $user)
{
    $query = "SELECT Biglietto.id, Film.titolo, Proiezione.ora, Proiezione.data, Sala.nome AS sala, Posto.fila, Posto.numero_posto
 FROM Biglietto
 JOIN Proiezione ON Biglietto.id_proiezione = Proiezione.id
 JOIN Film ON Proiezione.id_film = Film.id
 JOIN Sala ON Proiezione.id_sala = Sala.id
 JOIN Posto ON Biglietto.fila = Posto.fila AND Biglietto.numero_posto = Posto.numero_posto AND Biglietto.id_sala = Posto.id_sala
 WHERE Biglietto.id_utente = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result;
    } else {
        return null;
    }

}

//Restituisce tutte le recensioni a partire dalla più recente nella forma content | data | username
function getRecensioni($conn){

    $query = "  SELECT R.testo AS content,
                    R.data_creazione AS data,
                    U.username AS username 
                FROM Recensioni AS R
                JOIN Utente AS U
                ON R.id_utente = U.mail
                ORDER BY data DESC;";

    if (!$result = $conn->query($query)) {
        header('Location: ../php/500.php');
        exit();
    }

    if ($result->num_rows == 0) {
        return null;
    }

    return $result;
}

//Scrive la recensione a partire dalla connesione | mail-utente | contenuto-recensione
function writeRecensione($conn, $mail_utente, $content){

    $query = "  INSERT INTO Recensioni (testo, data_creazione, id_utente) 
                VALUES 
                (?, NOW(), ?);";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $content , $mail_utente);
    $stmt->execute();
    $stmt->get_result();

    if ($stmt->errno) {
        header('Location: ../php/500.php');
        exit();
    } else {
        return null;
    }
}

function getGeneris($conn)
{
    $sql = "SELECT nome FROM Genere";
    $result = $conn->query($sql);

    if (!$result) {
        header('Location: ../php/500.php');
        exit();
    }

    return $result;
}

function getGenereById($conn, $film_id)
{
    $query = "SELECT Film.*, Classificazione.nome_genere
          FROM Film
          LEFT JOIN Classificazione ON Film.id = Classificazione.id_film
          WHERE Film.id = ? ;";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $film_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        return $result;
    } else {
        return null;
    }

}

function updateFilm($conn, $titolo, $locandina, $trama, $regista, $durata, $film_id)
{
    $updateFilmQuery = "UPDATE Film 
    SET titolo = ?, 
        locandina = ?, 
        trama = ?, 
        regista = ?, 
        durata = ? 
    WHERE id = ?";

    $stmt = $conn->prepare($updateFilmQuery);
    $stmt->bind_param("ssssii", $titolo, $locandina, $trama, $regista, $durata, $film_id);
    $stmt->execute();

    $stmt->close();
}

function updateGeneri($conn, $film_id, $genere_primario, $genere_secondario){
    $deleteGeneriQuery = "DELETE FROM Classificazione WHERE id_film = ?";
    $stmtDeleteGeneri = $conn->prepare($deleteGeneriQuery);
    $stmtDeleteGeneri->bind_param("i", $film_id);
    $stmtDeleteGeneri->execute();
    $stmtDeleteGeneri->close();

    $addGenerePrimarioQuery = "INSERT INTO Classificazione (id_film, nome_genere) VALUES (?, ?)";
    $stmtAddGenerePrimario = $conn->prepare($addGenerePrimarioQuery);
    $stmtAddGenerePrimario->bind_param("is", $film_id, $genere_primario);
    $stmtAddGenerePrimario->execute();
    $stmtAddGenerePrimario->close();

    if (!empty($genere_secondario)) {
        $addGenereSecondarioQuery = "INSERT INTO Classificazione (id_film, nome_genere) VALUES (?, ?)";
        $stmtAddGenereSecondario = $conn->prepare($addGenereSecondarioQuery);

        if ($film_id !== null) {
            $stmtAddGenereSecondario->bind_param("is", $film_id, $genere_secondario);
            $stmtAddGenereSecondario->execute();
            $stmtAddGenereSecondario->close();
        }
    }
}


function deleteFilm($conn, $film_id){
    $deleteProiezioniQuery = "DELETE FROM Proiezione WHERE id_film = ?";
    $stmtProiezioni = $conn->prepare($deleteProiezioniQuery);
    $stmtProiezioni->bind_param("i", $film_id);
    $stmtProiezioni->execute();

    $stmtProiezioni->close();

    // Elimina le classificazioni correlate al film
    $deleteClassificazioniQuery = "DELETE FROM Classificazione WHERE id_film = ?";
    $stmtClassificazioni = $conn->prepare($deleteClassificazioniQuery);
    $stmtClassificazioni->bind_param("i", $film_id);
    $stmtClassificazioni->execute();

    $stmtClassificazioni->close();

    // Infine, elimina il film dalla tabella Film
    $deleteFilmQuery = "DELETE FROM Film WHERE id = ?";
    $stmtFilm = $conn->prepare($deleteFilmQuery);
    $stmtFilm->bind_param("i", $film_id);
    $stmtFilm->execute();

    $stmtFilm->close();
}

function getProiezioniFilm($conn, $film)
{
    $result = "SELECT Film.titolo, Proiezione.*
                                FROM Film
                                JOIN Proiezione ON Film.id = Proiezione.id_film
                                WHERE Film.id = ?
                                ORDER BY Film.titolo, Proiezione.data, Proiezione.ora";
    $stmt = $conn->prepare($result);
    $stmt->bind_param("i", $film);
    $stmt->execute();
    $result_proiezioni_per_film = $stmt->get_result();

    if ($result_proiezioni_per_film->num_rows > 0) {
        return $result_proiezioni_per_film;
    } else {
        return null;
    }
}

function updateUserInfo($conn, $mail, $username, $nome, $cognome, $password) {
    $sql_update_user = "UPDATE Utente SET  username=?, nome=?, cognome=?, password=? WHERE mail=?";
    $stmt = $conn->prepare($sql_update_user);
    $stmt->bind_param("sssss", $username, $nome, $cognome, $password, $mail);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

}

//controllo pagina proiezioni
function verificaProiezione($conn, $sala, $data, $ora) {
    $sql_verifica = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = ? AND data = ? AND ora = ?";
    $stmt = $conn->prepare($sql_verifica);
    $stmt->bind_param("sss", $sala, $data, $ora);
    $stmt->execute();
    $result_verifica = $stmt->get_result();
    $row_verifica = $result_verifica->fetch_assoc();

    return $row_verifica['count'];
}

function verificaProiezioniPrecedenti($conn, $sala, $data, $ora) {
    $ora_inizio = date('H:i:s', strtotime($ora . ' -3 hours'));
    $sql_verifica_prec = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = ? AND data = ? AND ora >= ? AND ora < ?";
    $stmt = $conn->prepare($sql_verifica_prec);
    $stmt->bind_param("ssss", $sala, $data, $ora_inizio, $ora);
    $stmt->execute();
    $result_verifica_prec = $stmt->get_result();
    $row_verifica_prec = $result_verifica_prec->fetch_assoc();

    return $row_verifica_prec['count'];
}

function verificaProiezioniSuccessive($conn, $sala, $data, $ora) {
    $ora_fine = date('H:i:s', strtotime($ora . ' +3 hours'));
    $sql_verifica_succ = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = ? AND data = ? AND ora > ? AND ora <= ?";
    $stmt = $conn->prepare($sql_verifica_succ);
    $stmt->bind_param("ssss", $sala, $data, $ora, $ora_fine);
    $stmt->execute();
    $result_verifica_succ = $stmt->get_result();
    $row_verifica_succ = $result_verifica_succ->fetch_assoc();
    return $row_verifica_succ['count'];
}

function inserisciProiezione($conn, $film, $sala, $ora, $data) {
    $sql = "INSERT INTO Proiezione (id_film, id_sala, ora, data) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $film, $sala, $ora, $data);
    $stmt->execute();

}

function inserisciBiglietto($conn, $id_proiezione, $mail, $fila, $num_posto, $id_sala){
    $sql = "INSERT INTO Biglietto (id_proiezione, id_utente, fila, numero_posto, id_sala)
    VALUES (?, ?, ?, ?, ?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $id_proiezione, $mail, $fila, $num_posto, $id_sala);
    $stmt->execute();

}

function getIdProiezione($conn, $id_film, $ora, $id_sala){
    $sql = "SELECT P.id FROM Proiezione AS P WHERE (P.id_film = ? && P.ora = ? && P.id_sala = ?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $id_film, $ora, $id_sala);
    $stmt->execute();
    $result_verifica_succ = $stmt->get_result();
    $row_verifica_succ = $result_verifica_succ->fetch_assoc();
    return $row_verifica_succ['id'];
}

function getIdSala($conn, $sala){
    $sql = "SELECT S.id FROM Sala AS S WHERE S.nome=?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sala);
    $stmt->execute();
    $result_verifica_succ = $stmt->get_result();
    $row_verifica_succ = $result_verifica_succ->fetch_assoc();
    return $row_verifica_succ['id'];
}

function effettuaRegistrazione($conn, $email, $username, $nome, $cognome, $password){
    $sql = "INSERT INTO Utente (mail, username, nome, cognome, permessi, password) VALUES (?, ?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $email, $username, $nome, $cognome, $password);
    $stmt->execute();
}

?>