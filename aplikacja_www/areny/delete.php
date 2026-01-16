<?php
require_once '../db.php';

// Sprawdzenie, czy podano ID
if (!isset($_GET['id'])) {
    header('Location: arena.php');
    exit;
}

$id_arena = $_GET['id'];

try {
    $pdo = getDbConnection();
    // Używamy transakcji, aby upewnić się, że powiązane rekordy są odpowiednio obsługiwane
    // ON DELETE SET NULL dla zespol.id_arena
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM arena WHERE id_arena = ?');
    $stmt->execute([$id_arena]);
    
    $pdo->commit();

    // Przekierowanie z powrotem na listę
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // W przypadku błędu wyświetlamy komunikat
    die("Błąd podczas usuwania areny: " . $e->getMessage());
}
?>
