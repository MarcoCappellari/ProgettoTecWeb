    <?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinema3";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Verifica la connessione
        if ($conn->connect_error) {
            throw new Exception("Errore di connessione al database: " . $conn->connect_error);
        }
        // Imposta la codifica dei caratteri
        mysqli_set_charset($conn, 'utf8mb4');
    } catch (Exception $e) {

        // Reindirizza l'utente alla pagina di errore 500
        header('Location: ../html/500.html');
        exit(); // Termina lo script PHP dopo il reindirizzamento

    }

    ?>
    <!--


    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinema3";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }


    -->