<?php
require_once '../db.php';

// Sprawdzenie, czy podano ID
if (!isset($_GET['id'])) {
    header('Location: zespoly.php');
    exit;
}

$id_zespolu = $_GET['id'];

try {
    $pdo = getDbConnection();
    // Używamy transakcji, aby upewnić się, że powiązane rekordy są odpowiednio obsługiwane
    // ON DELETE SET NULL dla zawodnik.id_zespolu i trener.id_zespolu
    // ON DELETE CASCADE dla kontrakt.id_zespolu i tabela_ligowa.id_zespolu
    // Ale w przypadku meczu, jeśli zespół jest usuwany, mecze z tym zespołem powinny zostać usunięte
    // lub ich id_gospodarza/id_goscia ustawione na NULL, w zależności od zdefiniowanych ograniczeń
    // w tabele.sql: id_gospodarza i id_goscia mają ON DELETE CASCADE, więc mecze zostaną usunięte.

    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM zespol WHERE id_zespolu = ?');
    $stmt->execute([$id_zespolu]);
    
    $pdo->commit();

header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    // W przypadku błędu wyświetlamy komunikat
    die("Błąd podczas usuwania zespołu: " . $e->getMessage());
}
?>
