<?php
// Connessione al database (sostituisci con le tue credenziali)
require_once 'connessione_database.php';

// Recupera il termine di ricerca dalla query string
$film_name = $_GET['film_name'];

// Esegui la query per cercare i film nel database in base al nome fornito
$query = "SELECT * FROM film WHERE nome LIKE '%$film_name%'";
$result = $conn->query($query);

// Chiudi la connessione al database
$conn->close();

// Elabora i risultati
if ($result) {
    if ($result->num_rows > 0) {
        // Se ci sono risultati, reindirizza all pagina "carica film.php" con i dettagli del primo film trovato
        $row = $result->fetch_assoc();
        $film_id = $row['id'];
        header("Location: visualizza_film.php?film=$film_id");
        exit();
    } else {
        // Nessun risultato trovato, puoi gestire questa situazione come desideri
        //echo "Nessun risultato trovato.";
        header("Location: carica_film.php?film_name=$film_name");
        exit();
    }
}


?>
