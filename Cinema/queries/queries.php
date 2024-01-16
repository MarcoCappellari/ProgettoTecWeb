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

function getOrariByFilmId($conn, $idFilm) {

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

function getFilmByName($conn, $film_name) {
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


?>
