<?php
// Ten plik powinien być dołączany na początku każdej strony wymagającej logowania.
// Zakłada, że zmienna $basePath jest zdefiniowana przed jego dołączeniem,
// aby poprawnie określić ścieżkę do strony logowania.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sprawdzenie, czy użytkownik jest zalogowany.
if (!isset($_SESSION['user_id'])) {
    // Jeśli nie, przekierowanie do strony logowania.
    $login_path = ($basePath ?? '') . 'login.php';
    header("Location: $login_path");
    exit;
}

/**
 * Sprawdza, czy zalogowany użytkownik ma rolę 'admin'.
 * Jeśli nie, przerywa wykonywanie skryptu i wyświetla komunikat o braku uprawnień.
 */
function require_admin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // Można tu przekierować do strony "brak dostępu" lub po prostu zakończyć działanie
        die('Brak uprawnień. Ta strona wymaga roli administratora.');
    }
}