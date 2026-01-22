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

    $id_zawodnika = $_POST['id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM zawodnik WHERE id_zawodnika = ?');
        $stmt->execute([$id_zawodnika]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd podczas usuwania zawodnika: " . $e->getMessage());
    }
}

// Obsługa żądania GET (wyświetlenie formularza potwierdzenia)
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_zawodnika = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT imie, nazwisko FROM zawodnik WHERE id_zawodnika = ?");
    $stmt->execute([$id_zawodnika]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit;
    }
    $itemName = htmlspecialchars($item['imie'] . ' ' . $item['nazwisko']);
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
    <p>Czy na pewno chcesz usunąć zawodnika: <strong><?= $itemName ?></strong>?</p>
    
    <form action="delete.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_zawodnika) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="button-delete">Tak, usuń</button>
    </form>
    <a href="index.php" class="button">Anuluj</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>