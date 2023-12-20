<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinema3";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

?>