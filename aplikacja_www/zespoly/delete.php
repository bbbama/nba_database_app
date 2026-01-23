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

    $id_zespolu = $_POST['id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM zespol WHERE id_zespolu = ?');
        $stmt->execute([$id_zespolu]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd podczas usuwania zespołu: " . $e->getMessage());
    }
}

// Obsługa żądania GET (wyświetlenie formularza potwierdzenia)
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_zespolu = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT nazwa FROM zespol WHERE id_zespolu = ?");
    $stmt->execute([$id_zespolu]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit;
    }
    $itemName = htmlspecialchars($item['nazwa']);
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
    <p>Czy na pewno chcesz usunąć zespół: <strong><?= $itemName ?></strong>?</p>
    <p class="warning">Uwaga: Usunięcie zespołu spowoduje usunięcie wszystkich powiązanych z nim meczów, kontraktów i wpisów w tabeli ligowej.</p>
    
    <form action="delete.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_zespolu) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="button-delete">Tak, usuń</button>
    </form>
    <a href="index.php" class="button">Anuluj</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>