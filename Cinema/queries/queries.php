<?php
require_once 'connessione_database.php';

function getFilms($conn)
{
    $sql = "SELECT id, nome, locandina FROM Film";
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

function getFirstDateOfFilm($conn, $idFilm)
{
    // Query per ottenere la data minima di riproduzione del film dato il suo ID
    $queryDataMinima = "SELECT MIN(data) as min_data FROM Riproduzione WHERE id_film = '$idFilm'";
    $resultDataMinima = $conn->query($queryDataMinima);


    // Estrae e restituisce la data minima di riproduzione
    if ($resultDataMinima && $resultDataMinima->num_rows > 0) {
        $row = $resultDataMinima->fetch_assoc();
        return $row['min_data'];  // Restituisce la data minima di riproduzione
    } else {
        return null;  // Restituisce false se non viene trovata la data
    }
}

function getTimesByFilmIdAndDate($conn, $idFilm, $dataMinima)
{
    // Query per ottenere gli orari minimi di riproduzione del film dato l'ID e la data minima
    $queryOrariMinimi = "SELECT Riproduzione.ora, Riproduzione.data
                         FROM Riproduzione
                         WHERE Riproduzione.id_film = '$idFilm' AND Riproduzione.data = '$dataMinima'
                         ORDER BY Riproduzione.ora";
    $resultOrariMinimi = $conn->query($queryOrariMinimi);


    // Restituisce il risultato della query o false se non viene trovato alcun orario
    if ($resultOrariMinimi && $resultOrariMinimi->num_rows > 0) {
        return $resultOrariMinimi;
    } else {
        return null;
    }
}

function getFilmActorsById($conn, $idFilm)
{
    // Sanificazione dell'input per prevenire SQL injection
    $idFilm = intval($idFilm);

    $queryAttori = "SELECT Attori.nome, Attori.cognome 
                    FROM Attori
                    LEFT JOIN Partecipano ON Attori.id = Partecipano.id_attore
                    WHERE Partecipano.id_film = '$idFilm'";

    $resultAttori = $conn->query($queryAttori);
    $attori = "";
    if ($resultAttori->num_rows > 0) {

        while ($row = $resultAttori->fetch_assoc()) {
            $attori .= $row['nome'] . ' ' . $row['cognome'] . ', ';
        }
        return rtrim($attori, ', ');  // Rimuove l'ultima virgola e lo spazio
    } else {
        return null;  // Oppure puoi restituire un valore predefinito o vuoto
    }
}

function getFilmGenresById($conn, $idFilm)
{

    $queryGeneri = "SELECT Conforme.nome_genere 
                    FROM Conforme
                    WHERE Conforme.id_film = '$idFilm'";

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

    $query = "SELECT Riproduzione.data, Riproduzione.ora
              FROM Riproduzione
              WHERE Riproduzione.id_film = $idFilm
              ORDER BY Riproduzione.ora";

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
    $query = "SELECT * FROM film WHERE nome LIKE '%$film_name%'";

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

function getBigliettoByUser($conn, $user)
{
    $query = "SELECT B.id, F.nome AS nome_film, R.data, R.ora, P.fila, P.numero_posto, S.nome AS nome_sala
        FROM Biglietto B
        JOIN Riproduzione R ON B.id_riproduzione = R.id
        JOIN Film F ON R.id_film = F.id
        JOIN Assegnazione A ON R.id = A.id_riproduzione
        JOIN Posto P ON A.fila = P.fila AND A.numero_posto = P.numero_posto AND A.id_sala = P.id_sala
        JOIN Sala S ON P.id_sala = S.id
        WHERE B.id_utente = '$user'";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        return $result;
    } else {
        return null; // Nessun risultato trovato
    }
}

function getIdByUsername($conn, $username) {
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
