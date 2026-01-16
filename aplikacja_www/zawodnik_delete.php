<?php
require_once 'db.php';

// Sprawdzenie, czy podano ID
if (!isset($_GET['id'])) {
    header('Location: zawodnicy.php');
    exit;
}

$id_zawodnika = $_GET['id'];

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM zawodnik WHERE id_zawodnika = ?');
    $stmt->execute([$id_zawodnika]);
    
    // Przekierowanie z powrotem na listę
    header('Location: zawodnicy.php');
    exit;

} catch (PDOException $e) {
    // W przypadku błędu (np. naruszenia więzów integralności, choć ON DELETE CASCADE/SET NULL powinno pomóc)
    // warto wyświetlić komunikat błędu.
    die("Błąd podczas usuwania zawodnika: " . $e->getMessage());
}
?>
