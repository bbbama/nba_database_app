<?php
// Pobieranie danych dostępowych do bazy danych z zmiennych środowiskowych.
// Używamy wartości domyślnych dla środowiska lokalnego, jeśli zmienne środowiskowe nie są ustawione.

$db_host = getenv('DB_HOST') ?: 'localhost';      // Adres serwera bazy danych
$db_port = getenv('DB_PORT') ?: '5432';           // Port serwera bazy danych
$db_name = getenv('DB_NAME') ?: 'nba_db';         // Nazwa bazy danych
$db_user = getenv('DB_USER') ?: 'bartek';         // Nazwa użytkownika bazy danych
$db_pass = getenv('DB_PASS') ?: 'lebron';         // Hasło użytkownika bazy danych

// Możesz dodać inne zmienne konfiguracyjne tutaj, jeśli są potrzebne.
// Na przykład:
// $app_debug = getenv('APP_DEBUG') ?: false;
?>
