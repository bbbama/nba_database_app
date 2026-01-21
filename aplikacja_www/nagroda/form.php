<?php
require_once '../db.php';

$pdo = getDbConnection();

$nagroda = [
    'id_nagrody' => '',
    'id_zawodnika' => '',
    'nazwa_nagrody' => '',
    'rok' => ''
];
$errors = [];
$is_edit = false;

// Pobranie listy zawodników do dropdowna
try {
    $zawodnicy_stmt = $pdo->query('SELECT id_zawodnika, imie, nazwisko FROM zawodnik ORDER BY nazwisko, imie');
    $zawodnicy = $zawodnicy_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Błąd przy pobieraniu listy zawodników: " . $e->getMessage();
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_nagrody = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM nagroda WHERE id_nagrody = ?');
        $stmt->execute([$id_nagrody]);
        $nagroda = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$nagroda) {
            die('Nie znaleziono nagrody o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych nagrody: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nagroda['id_zawodnika'] = $_POST['id_zawodnika'] ?? '';
    $nagroda['nazwa_nagrody'] = $_POST['nazwa_nagrody'] ?? '';
    $nagroda['rok'] = $_POST['rok'] ?? '';

    // Walidacja
    if (empty($nagroda['id_zawodnika'])) $errors[] = 'Zawodnik jest wymagany.';
    if (empty($nagroda['nazwa_nagrody'])) $errors[] = 'Nazwa nagrody jest wymagana.';
    if (empty($nagroda['rok'])) {
        $errors[] = 'Rok jest wymagany.';
    } elseif (!is_numeric($nagroda['rok']) || $nagroda['rok'] < 1900 || $nagroda['rok'] > date('Y')) {
        $errors[] = 'Rok musi być liczbą między 1900 a bieżącym rokiem.';
    }

    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE nagroda SET id_zawodnika = ?, nazwa_nagrody = ?, rok = ? WHERE id_nagrody = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nagroda['id_zawodnika'],
                    $nagroda['nazwa_nagrody'],
                    $nagroda['rok'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO nagroda (id_zawodnika, nazwa_nagrody, rok) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $nagroda['id_zawodnika'],
                    $nagroda['nazwa_nagrody'],
                    $nagroda['rok']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Nagrodę';
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
        <div>
            <label for="id_zawodnika">Zawodnik:</label>
            <select id="id_zawodnika" name="id_zawodnika" required>
                <option value="">-- Wybierz zawodnika --</option>
                <?php foreach ($zawodnicy as $zawodnik): ?>
                    <option value="<?= $zawodnik['id_zawodnika'] ?>" <?= ($nagroda['id_zawodnika'] == $zawodnik['id_zawodnika']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="nazwa_nagrody">Nazwa nagrody:</label>
            <input type="text" id="nazwa_nagrody" name="nazwa_nagrody" value="<?= htmlspecialchars($nagroda['nazwa_nagrody']) ?>" required>
        </div>
        <div>
            <label for="rok">Rok:</label>
            <input type="number" id="rok" name="rok" value="<?= htmlspecialchars($nagroda['rok']) ?>" required min="1900" max="<?= date('Y') ?>">
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj nagrodę' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>