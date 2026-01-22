<?php
require_once '../db.php';
session_start();

$pdo = getDbConnection();

// Obsługa żądania POST (potwierdzenie usunięcia)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    if (!isset($_POST['id'])) {
        header('Location: index.php');
        exit;
    }

    $id_meczu = $_POST['id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM mecz WHERE id_meczu = ?');
        $stmt->execute([$id_meczu]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd podczas usuwania meczu: " . $e->getMessage());
    }
}

// Obsługa żądania GET (wyświetlenie formularza potwierdzenia)
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_meczu = $_GET['id'];

try {
    $sql = "SELECT m.data_meczu, gosp.nazwa AS nazwa_gospodarza, gosc.nazwa AS nazwa_goscia
            FROM mecz m
            JOIN zespol gosp ON m.id_gospodarza = gosp.id_zespolu
            JOIN zespol gosc ON m.id_goscia = gosc.id_zespolu
            WHERE m.id_meczu = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_meczu]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit;
    }
    $itemName = "Mecz: " . htmlspecialchars($item['nazwa_gospodarza']) . " vs " . htmlspecialchars($item['nazwa_goscia']) . " z dnia " . htmlspecialchars(date('Y-m-d', strtotime($item['data_meczu']))) ;
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Potwierdź usunięcie';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Potwierdź usunięcie</h2>
    <p>Czy na pewno chcesz usunąć następujący mecz?</p>
    <p><strong><?= $itemName ?></strong></p>
    
    <form action="delete.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_meczu) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="button-delete">Tak, usuń</button>
    </form>
    <a href="index.php" class="button">Anuluj</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
