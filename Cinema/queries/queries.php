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
function absentProiection($conn, $id_film, $ora_film, $data_film) {
    
    $query ="SELECT *
            FROM Proiezione AS P
            WHERE P.id_film= $id_film AND P.ora = '$ora_film' AND P.data = '$data_film'";
    
    if(!($result = $conn->query($query))){
        header('Location: ../html/500.html'); 
        exit(); 
    }

    if ($result->num_rows == 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//Restituisce i posti di una proiezione identificati da FILA | NUMERO | DISPONIBILE
function getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film) {

    $query ="SELECT Po.fila AS fila, 
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

    if(!$result = $conn->query($query)){
        header('Location: ../html/500.html'); 
        exit(); 
    }

    if ($result->num_rows == 0) {
        header('Location: ../html/500.html'); 
        exit();
    }

    return $result;
}

function getUserByMailOrUsername($conn, $user)
{
    $query = "SELECT * FROM Utente WHERE username = '$user' OR mail = '$user'";
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
    $query = "SELECT permessi FROM Utente WHERE username = ?";
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
?>
