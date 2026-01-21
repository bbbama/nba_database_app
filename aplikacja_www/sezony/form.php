<?php
require_once '../db.php';

$pdo = getDbConnection();

$sezon = [
    'id_sezonu' => '',
    'rok_rozpoczecia' => '',
    'rok_zakonczenia' => ''
];
$errors = [];
$is_edit = false;

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_sezonu = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM sezon WHERE id_sezonu = ?');
        $stmt->execute([$id_sezonu]);
        $sezon = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sezon) {
            die('Nie znaleziono sezonu o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych sezonu: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    $sezon['rok_rozpoczecia'] = $_POST['rok_rozpoczecia'] ?? '';
    $sezon['rok_zakonczenia'] = $_POST['rok_zakonczenia'] ?? '';

    // Prosta walidacja
    if (empty($sezon['rok_rozpoczecia']) || !is_numeric($sezon['rok_rozpoczecia'])) {
        $errors[] = 'Rok rozpoczęcia jest wymagany i musi być liczbą.';
    }
    if (empty($sezon['rok_zakonczenia']) || !is_numeric($sezon['rok_zakonczenia'])) {
        $errors[] = 'Rok zakończenia jest wymagany i musi być liczbą.';
    }
    if (!empty($sezon['rok_rozpoczecia']) && !empty($sezon['rok_zakonczenia']) && $sezon['rok_zakonczenia'] < $sezon['rok_rozpoczecia']) {
        $errors[] = 'Rok zakończenia nie może być wcześniejszy niż rok rozpoczęcia.';
    }


    if (empty($errors)) {
        try {
            if ($is_edit) {
                // Aktualizacja
                $sql = "UPDATE sezon SET rok_rozpoczecia = ?, rok_zakonczenia = ? WHERE id_sezonu = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $sezon['rok_rozpoczecia'],
                    $sezon['rok_zakonczenia'],
                    $_GET['id']
                ]);
            } else {
                // Dodawanie
                $sql = "INSERT INTO sezon (rok_rozpoczecia, rok_zakonczenia) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $sezon['rok_rozpoczecia'],
                    $sezon['rok_zakonczenia']
                ]);
            }
            // Przekierowanie po sukcesie
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Sezon';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <p>Wystąpiły błędy:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div>
            <label for="rok_rozpoczecia">Rok rozpoczęcia:</label>
            <input type="number" id="rok_rozpoczecia" name="rok_rozpoczecia" value="<?= htmlspecialchars($sezon['rok_rozpoczecia']) ?>" required min="1900" max="<?= date('Y') + 1 ?>">
        </div>
        <div>
            <label for="rok_zakonczenia">Rok zakończenia:</label>
            <input type="number" id="rok_zakonczenia" name="rok_zakonczenia" value="<?= htmlspecialchars($sezon['rok_zakonczenia']) ?>" required min="1900" max="<?= date('Y') + 2 ?>">
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj sezon' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>