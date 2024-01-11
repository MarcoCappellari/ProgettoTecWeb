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

function getSeatByFilmOraData($conn, $id_film, $ora_film, $data_film)
{

    // Crea la query SQL
    $query = "SELECT A.id_riproduzione, R.id_film, R.ora, R.data, P.fila, P.numero_posto, A.disponibile
              FROM Assegnazione A
              JOIN Riproduzione R ON A.id_riproduzione = R.id
              JOIN Posto P ON A.fila = P.fila AND A.numero_posto = P.numero_posto AND A.id_sala = P.id_sala
              WHERE R.id_film = $id_film AND R.ora = '$ora_film' AND R.data = '$data_film'";

    // Esegue la query e restituisce il risultato
    $result = $conn->query($query);

    if ($result->num_rows == 0) {
        header('Location: ../html/500.html');  // NON so se errore 404 o 500
        exit;
    }

    // Restituisce il risultato
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

?>