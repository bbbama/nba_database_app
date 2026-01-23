<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_admin();

require_once '../db.php';

$arena = [
    'id_arena' => '',
    'nazwa' => '',
    'miasto' => '',
    'pojemnosc' => '',
    'rok_otwarcia' => ''
];
$errors = [];
$is_edit = false;

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_arena = $_GET['id'];

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM arena WHERE id_arena = ?');
        $stmt->execute([$id_arena]);
        $arena = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$arena) {
            die('Nie znaleziono areny o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych areny: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    $arena['nazwa'] = $_POST['nazwa'] ?? '';
    $arena['miasto'] = $_POST['miasto'] ?? '';
    $arena['pojemnosc'] = $_POST['pojemnosc'] ?? '';
    $arena['rok_otwarcia'] = $_POST['rok_otwarcia'] ?? '';

    // Prosta walidacja
    if (empty($arena['nazwa'])) $errors[] = 'Nazwa areny jest wymagana.';
    if (empty($arena['pojemnosc']) || !is_numeric($arena['pojemnosc']) || $arena['pojemnosc'] <= 0) $errors[] = 'Pojemność musi być liczbą większą od 0.';
    if (!empty($arena['rok_otwarcia']) && (!is_numeric($arena['rok_otwarcia']) || $arena['rok_otwarcia'] <= 1850 || $arena['rok_otwarcia'] > date('Y'))) $errors[] = 'Rok otwarcia musi być prawidłową wartością (np. > 1850 i <= bieżący rok).';


    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            if ($is_edit) {
                // Aktualizacja
                $sql = "UPDATE arena SET nazwa = ?, miasto = ?, pojemnosc = ?, rok_otwarcia = ? WHERE id_arena = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $arena['nazwa'],
                    $arena['miasto'],
                    $arena['pojemnosc'],
                    empty($arena['rok_otwarcia']) ? null : $arena['rok_otwarcia'],
                    $_GET['id']
                ]);
            } else {
                // Dodawanie
                $sql = "INSERT INTO arena (nazwa, miasto, pojemnosc, rok_otwarcia) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $arena['nazwa'],
                    $arena['miasto'],
                    $arena['pojemnosc'],
                    empty($arena['rok_otwarcia']) ? null : $arena['rok_otwarcia']
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

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Arenę';
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
            <label for="nazwa">Nazwa:</label>
            <input type="text" id="nazwa" name="nazwa" value="<?= htmlspecialchars($arena['nazwa']) ?>" required>
        </div>
        <div>
            <label for="miasto">Miasto:</label>
            <input type="text" id="miasto" name="miasto" value="<?= htmlspecialchars($arena['miasto']) ?>">
        </div>
        <div>
            <label for="pojemnosc">Pojemność:</label>
            <input type="number" id="pojemnosc" name="pojemnosc" value="<?= htmlspecialchars($arena['pojemnosc']) ?>" required min="1">
        </div>
        <div>
            <label for="rok_otwarcia">Rok otwarcia:</label>
            <input type="number" id="rok_otwarcia" name="rok_otwarcia" value="<?= htmlspecialchars($arena['rok_otwarcia']) ?>" min="1851" max="<?= date('Y') ?>">
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj arenę' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>