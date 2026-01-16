<?php
require_once '../db.php';

// Sprawdzenie, czy podano ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_meczu = $_GET['id'];

try {
    $pdo = getDbConnection();
    // ON DELETE CASCADE w tabeli statystyki_meczu usunie powiązane statystyki
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM mecz WHERE id_meczu = ?');
    $stmt->execute([$id_meczu]);
    
    $pdo->commit();

    // Przekierowanie z powrotem na listę
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // W przypadku błędu wyświetlamy komunikat
    die("Błąd podczas usuwania meczu: " . $e->getMessage());
}
?>
