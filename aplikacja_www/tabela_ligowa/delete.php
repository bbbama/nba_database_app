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

    $id_tabeli = $_POST['id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM tabela_ligowa WHERE id_tabeli = ?');
        $stmt->execute([$id_tabeli]);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd podczas usuwania wpisu z tabeli ligowej: " . $e->getMessage());
    }
}

// Obsługa żądania GET (wyświetlenie formularza potwierdzenia)
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_tabeli = $_GET['id'];

try {
    $sql = "SELECT s.rok_rozpoczecia, s.rok_zakonczenia, z.nazwa AS nazwa_zespolu
            FROM tabela_ligowa tl
            JOIN sezon s ON tl.id_sezonu = s.id_sezonu
            JOIN zespol z ON tl.id_zespolu = z.id_zespolu
            WHERE tl.id_tabeli = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_tabeli]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit;
    }
    $itemName = "Wpis dla zespołu " . htmlspecialchars($item['nazwa_zespolu']) . " w sezonie " . htmlspecialchars($item['rok_rozpoczecia']) . "/" . htmlspecialchars($item['rok_zakonczenia']);
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
    <p>Czy na pewno chcesz usunąć następujący wpis z tabeli ligowej?</p>
    <p><strong><?= $itemName ?></strong></p>
    
    <form action="delete.php" method="POST" style="display: inline-block;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_tabeli) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="button-delete">Tak, usuń</button>
    </form>
    <a href="index.php" class="button">Anuluj</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
