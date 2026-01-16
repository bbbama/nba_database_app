<?php
require_once '../db.php';

// Sprawdzenie, czy podano ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_sezonu = $_GET['id'];

try {
    $pdo = getDbConnection();
    // Używamy transakcji, aby upewnić się, że powiązane rekordy są odpowiednio obsługiwane
    // ON DELETE SET NULL w tabeli mecz, jeśli tak jest zdefiniowane
    // ON DELETE CASCADE w tabeli tabela_ligowa
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM sezon WHERE id_sezonu = ?');
    $stmt->execute([$id_sezonu]);
    
    $pdo->commit();

    // Przekierowanie z powrotem na listę
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // W przypadku błędu wyświetlamy komunikat
    die("Błąd podczas usuwania sezonu: " . $e->getMessage());
}
?>
