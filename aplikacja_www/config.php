<?php
// Pobieranie danych dostępowych do bazy danych z zmiennych środowiskowych.
// Preferowane jest użycie DATABASE_URL, które jest standardem na platformach takich jak Render.
// W przypadku braku DATABASE_URL, używane są indywidualne zmienne lub wartości domyślne.

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $url = parse_url($database_url);
    $db_host = $url['host'];
    $db_port = $url['port'] ?? '5432'; // Domyślny port PostgreSQL
    $db_name = ltrim($url['path'], '/');
    $db_user = $url['user'];
    $db_pass = $url['pass'];
} else {
    // Fallback dla środowiska lokalnego lub gdy DATABASE_URL nie jest dostępne
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_port = getenv('DB_PORT') ?: '5432';
    $db_name = getenv('DB_NAME') ?: 'nba_db';
    $db_user = getenv('DB_USER') ?: 'bartek';
    $db_pass = getenv('DB_PASS') ?: 'lebron';
}

// Możesz dodać inne zmienne konfiguracyjne tutaj, jeśli są potrzebne.
// Na przykład:
// $app_debug = getenv('APP_DEBUG') ?: false;
?>