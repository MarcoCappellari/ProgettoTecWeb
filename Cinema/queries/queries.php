<?php
require_once 'connessione_database.php';

function getFilms($conn)
{
    $sql = "SELECT id, titolo, locandina FROM Film";
    $result = $conn->query($sql);
    return $result;
}

function getFilmByIdQuery($conn, $idFilm)
{
    // Query per ottenere le informazioni del film dato il suo ID
    $queryFilm = "SELECT * FROM Film WHERE id='$idFilm'";
    $resultFilm = $conn->query($queryFilm);

    // Verifica se la query ha prodotto risultati
    if ($resultFilm && $resultFilm->num_rows > 0) {
        return $resultFilm->fetch_assoc();
    } else {
        header('Location: ../html/404.html');
        exit;
    }
}


function getFilmGenresById($conn, $idFilm)
{

    $queryGeneri = "SELECT Classificazione.nome_genere
                    FROM Classificazione
                    WHERE Classificazione.id_film = '$idFilm'";

    $resultGeneri = $conn->query($queryGeneri);
    $generi = "";

    if ($resultGeneri->num_rows > 0) {
        while ($rowGeneri = $resultGeneri->fetch_assoc()) {
            $generi .= $rowGeneri['nome_genere'] . ', ';
        }
        return rtrim($generi, ', ');  // Rimuove l'ultima virgola e lo spazio
    } else {
        return null;  // Oppure puoi restituire un valore predefinito o vuoto
    }
}

function getOrariByFilmId($conn, $idFilm)
{

    $query = "SELECT Proiezione.data, Proiezione.ora
              FROM Proiezione
              WHERE Proiezione.id_film = $idFilm
              ORDER BY Proiezione.ora";

    $result = $conn->query($query);

    if (!$result) {
        return null;
    }

    $orari = array();

    // Processa i risultati della query
    while ($row = $result->fetch_assoc()) {
        $data = $row['data'];
        $ora = $row['ora'];
        // Aggiungi le informazioni all'array
        $orari[$data][] = $ora;
    }
    return $orari;
}

function getFilmByName($conn, $film_name)
{
    // Sanifica l'input per prevenire SQL injection
    $film_name = $conn->real_escape_string($film_name);

    // Costruisci la query
    $query = "SELECT * FROM film WHERE titolo LIKE '%$film_name%'";

    // Esegui la query
    $result = $conn->query($query);

    // Se la query ha prodotto risultati, restituisci i dati
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null; // Nessun film trovato
    }
}

//Restituisce TRUE se non trova la proiezione identificata da $id_film, $ora_film, $data_film
function absentProiection($conn, $id_film, $ora_film, $data_film)
{

    $query = "SELECT *
            FROM Proiezione AS P
            WHERE P.id_film= $id_film AND P.ora = '$ora_film' AND P.data = '$data_film'";

    if (!($result = $conn->query($query))) {
        header('Location: ../html/500.html');
        exit();
    }

    if ($result->num_rows == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//Restituisce la sala di una proiezione
function getSalaByProiection($conn, $id_film, $ora_film, $data_film)
{

    $query = "SELECT S.nome AS nome
            FROM Proiezione AS P
            JOIN Sala AS S ON P.id_sala = S.id
            WHERE P.id_film= $id_film AND P.ora = '$ora_film' AND P.data = '$data_film'";

    if (!($result = $conn->query($query))) {
        header('Location: ../html/500.html');
        exit();
    }

    if ($result->num_rows == 0) {
        header('Location: ../html/500.html');
        exit();
    }

    return $result->fetch_assoc();
}

//Restituisce i posti di una proiezione identificati da FILA | NUMERO | DISPONIBILE
function getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film)
{

    $query = "SELECT Po.fila AS fila, 
                    Po.numero_posto AS numero, 
                    CASE WHEN B.id IS NOT NULL THEN FALSE ELSE TRUE END AS disponibile
            FROM 
                Proiezione AS P
            JOIN 
                Posto AS Po ON Po.id_sala = P.id_sala
            LEFT JOIN
                Biglietto AS B ON B.id_proiezione = P.id AND B.fila = Po.fila AND B.numero_posto = Po.numero_posto AND B.id_sala = P.id_sala
            WHERE P.id_film= $id_film AND P.ora = '$ora_film' AND P.data = '$data_film'
            ORDER BY Po.fila ASC, Po.numero_posto ASC";

    if (!$result = $conn->query($query)) {
        header('Location: ../html/500.html');
        exit();
    }

    if ($result->num_rows == 0) {
        header('Location: ../html/500.html');
        exit();
    }

    return $result;
}

//restituisce tutta la linea della tabella Utente contentente l'user per l'username OR mail
function getUserByMailOrUsername($conn, $user)
{
    $query = "SELECT * 
                FROM Utente 
                WHERE username = '$user' OR mail = '$user'";

    $result = $conn->query($query);
    $user = $result->fetch_assoc();
    return $user;
}

function getUserByMail($conn, $user)
{
    $query = "SELECT * FROM Utente WHERE mail = '$user'";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
    return $user['username'];
}
function getPermessiByUsername($conn, $user)
{
    $query = "SELECT permessi FROM Utente WHERE mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return (bool) $row['permessi'];
    } else {
        return false;
    }
}
function getSala($conn)
{
    $query = "SELECT * FROM Sala";
    $result = $conn->query($query);
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
 WHERE Biglietto.id_utente = '$user'";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        return $result;
    } else {
        return null;
    }

}

function getIdByUsername($conn, $username)
{

    $query = "SELECT mail FROM Utente WHERE username = '$username'";
    $result = $conn->query($query);

    // Controlla se la query ha restituito dei risultati
    if ($result && $result->num_rows > 0) {
        // Estrai l'email dalla prima riga risultante
        $row = $result->fetch_assoc();
        return $row['mail'];
    } else {
        // Nessun risultato trovato
        return null;
    }
}


//Restituisce tutte le recensioni a partire dalla più recente nella forma content | data | username
function getRecensioni($conn)
{

    $query = "  SELECT R.testo AS content,
                    R.data_creazione AS data,
                    U.username AS username 
                FROM Recensioni AS R
                JOIN Utente AS U
                ON R.id_utente = U.mail
                ORDER BY data DESC;";

    if (!$result = $conn->query($query)) {
        header('Location: ../html/500.html');
        exit();
    }

    if ($result->num_rows == 0) {
        return null;
    }

    return $result;
}

//Scrive la recensione a partire dalla connesione | mail-utente | contenuto-recensione
function writeRecensione($conn, $mail_utente, $content)
{

    $query = "  INSERT INTO Recensioni (testo, data_creazione, id_utente) 
                VALUES 
                ('$content', NOW(), '$mail_utente');";

    if (!$conn->query($query)) {
        header('Location: ../html/500.html');
        exit();
    } else {
        return null;
    }

}

function getGeneris($conn)
{
    $sql = "SELECT nome FROM Genere";
    $result = $conn->query($sql);
    return $result;
}

function getGenereById($conn, $film_id)
{
    $query = "SELECT Film.*, Classificazione.nome_genere
          FROM Film
          LEFT JOIN Classificazione ON Film.id = Classificazione.id_film
          WHERE Film.id = $film_id;";
    $result = mysqli_query($conn, $query);
    if ($result) {
        return $result;
    } else {
        // Se la query ha fallito, restituisci null o gestisci l'errore in modo appropriato
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

function updateGeneri($conn, $film_id, $genere_primario, $genere_secondario)
{
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

        // Verifica che $film_id sia diverso da null prima di eseguire la query
        if ($film_id !== null) {
            $stmtAddGenereSecondario->bind_param("is", $film_id, $genere_secondario);
            $stmtAddGenereSecondario->execute();
            $stmtAddGenereSecondario->close();
        }
    }
}


function deleteFilm($conn, $film_id)
{
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

function getProiezioni($conn)
{
    $result = "SELECT Film.titolo, Proiezione.*
                                FROM Film
                                JOIN Proiezione ON Film.id = Proiezione.id_film
                                ORDER BY Film.titolo, Proiezione.data, Proiezione.ora";
    $result_proiezioni_per_film = $conn->query($result);
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

    // Esegui la query
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

}

//controllo pagina proiezioni
function verificaProiezione($conn, $sala, $data, $ora) {
    $sql_verifica = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = '$sala' AND data = '$data' AND ora = '$ora'";
    $result_verifica = $conn->query($sql_verifica);
    $row_verifica = $result_verifica->fetch_assoc();

    return $row_verifica['count'];
}

function verificaProiezioniPrecedenti($conn, $sala, $data, $ora) {
    $ora_inizio = date('H:i:s', strtotime($ora . ' -3 hours'));
    $sql_verifica_prec = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = '$sala' AND data = '$data' AND ora >= '$ora_inizio' AND ora < '$ora'";
    $result_verifica_prec = $conn->query($sql_verifica_prec);
    $row_verifica_prec = $result_verifica_prec->fetch_assoc();

    return $row_verifica_prec['count'];
}

function verificaProiezioniSuccessive($conn, $sala, $data, $ora) {
    $ora_fine = date('H:i:s', strtotime($ora . ' +3 hours'));
    $sql_verifica_succ = "SELECT COUNT(*) AS count FROM Proiezione WHERE id_sala = '$sala' AND data = '$data' AND ora > '$ora' AND ora <= '$ora_fine'";
    $result_verifica_succ = $conn->query($sql_verifica_succ);
    $row_verifica_succ = $result_verifica_succ->fetch_assoc();

    return $row_verifica_succ['count'];
}

function inserisciProiezione($conn, $film, $sala, $ora, $data) {
    $sql = "INSERT INTO Proiezione (id_film, id_sala, ora, data) VALUES ('$film', '$sala', '$ora', '$data')";
    $conn->query($sql);
}

?>