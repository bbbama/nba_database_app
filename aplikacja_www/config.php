<?php

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Render / produkcja
    $url = parse_url($database_url);

    $db_host = $url['host'];
    $db_port = $url['port'] ?? 5432;
    $db_name = ltrim($url['path'], '/');
    $db_user = $url['user'];
    $db_pass = $url['pass'];
} else {
    // Lokalnie
    $db_host = 'localhost';
    $db_port = 5432;
    $db_name = 'nba_db';
    $db_user = 'bartek';
    $db_pass = 'lebron';
}

// 🔑 TUTAJ TEGO BRAKOWAŁO
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Błąd połączenia z bazą danych: ' . $e->getMessage());
}
