<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_admin();
require_once $basePath . 'db.php';

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

    $id_kontuzji = $_POST['id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM kontuzja WHERE id_kontuzji = ?');
        $stmt->execute([$id_kontuzji]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd podczas usuwania kontuzji: " . $e->getMessage());
    }
}

// Obsługa żądania GET (wyświetlenie formularza potwierdzenia)
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_kontuzji = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT typ_kontuzji FROM kontuzja WHERE id_kontuzji = ?");
    $stmt->execute([$id_kontuzji]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit;
    }
    
    $itemName = htmlspecialchars($item['typ_kontuzji']);

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
    <p>Czy na pewno chcesz usunąć kontuzję: <strong><?= $itemName ?></strong>?</p>
    
    <form action="delete.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_kontuzji) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="button-delete">Tak, usuń</button>
    </form>
    <a href="index.php" class="button">Anuluj</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>