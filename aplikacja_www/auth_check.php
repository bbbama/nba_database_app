<?php

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

function require_admin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        die('Brak uprawnień. Ta strona wymaga roli administratora.');
    }
}