<?php

// Plik do obsługi połączenia z bazą danych

require_once 'config.php';

function getDbConnection() {
    global $db_host, $db_port, $db_name, $db_user, $db_pass;

    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass";

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // W produkcyjnym środowisku warto logować błędy, a nie je wyświetlać
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
}

?>
