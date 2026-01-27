<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';

// Tylko administratorzy mogą usuwać użytkowników
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once $basePath . 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Nie można usunąć samego siebie
if ($id == $_SESSION['user_id']) {
    // Można by tu ustawić jakiś komunikat błędu w sesji
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM uzytkownicy WHERE id_uzytkownika = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    die("Błąd podczas usuwania użytkownika: " . $e->getMessage());
}

header('Location: index.php');
exit;
