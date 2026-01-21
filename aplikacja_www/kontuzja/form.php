<?php
require_once '../db.php';

$pdo = getDbConnection();

$kontuzja = [
    'id_kontuzji' => '',
    'id_zawodnika' => '',
    'typ_kontuzji' => '',
    'data_rozpoczecia' => '',
    'data_zakonczenia' => '',
    'status' => ''
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
    $id_kontuzji = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM kontuzja WHERE id_kontuzji = ?');
        $stmt->execute([$id_kontuzji]);
        $kontuzja = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$kontuzja) {
            die('Nie znaleziono kontuzji o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych kontuzji: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    $kontuzja['id_zawodnika'] = $_POST['id_zawodnika'] ?? '';
    $kontuzja['typ_kontuzji'] = $_POST['typ_kontuzji'] ?? '';
    $kontuzja['data_rozpoczecia'] = $_POST['data_rozpoczecia'] ?? '';
    $kontuzja['data_zakonczenia'] = $_POST['data_zakonczenia'] ?? '';
    $kontuzja['status'] = $_POST['status'] ?? '';

    // Walidacja
    if (empty($kontuzja['id_zawodnika'])) $errors[] = 'Zawodnik jest wymagany.';
    if (empty($kontuzja['typ_kontuzji'])) $errors[] = 'Typ kontuzji jest wymagany.';
    if (empty($kontuzja['data_rozpoczecia'])) $errors[] = 'Data rozpoczęcia jest wymagana.';
    
    // Status walidacja
    $dostepne_statusy = ['aktywna', 'wyleczona', 'nieznany'];
    if (!in_array($kontuzja['status'], $dostepne_statusy)) {
        $errors[] = 'Nieprawidłowy status. Dozwolone statusy to: ' . implode(', ', $dostepne_statusy) . '.';
    }

    if (!empty($kontuzja['data_rozpoczecia']) && !empty($kontuzja['data_zakonczenia']) && $kontuzja['data_rozpoczecia'] > $kontuzja['data_zakonczenia']) {
        $errors[] = 'Data zakończenia kontuzji nie może być wcześniejsza niż data rozpoczęcia.';
    }

    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE kontuzja SET id_zawodnika = ?, typ_kontuzji = ?, data_rozpoczecia = ?, data_zakonczenia = ?, status = ? WHERE id_kontuzji = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $kontuzja['id_zawodnika'],
                    $kontuzja['typ_kontuzji'],
                    $kontuzja['data_rozpoczecia'],
                    empty($kontuzja['data_zakonczenia']) ? null : $kontuzja['data_zakonczenia'],
                    $kontuzja['status'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO kontuzja (id_zawodnika, typ_kontuzji, data_rozpoczecia, data_zakonczenia, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $kontuzja['id_zawodnika'],
                    $kontuzja['typ_kontuzji'],
                    $kontuzja['data_rozpoczecia'],
                    empty($kontuzja['data_zakonczenia']) ? null : $kontuzja['data_zakonczenia'],
                    $kontuzja['status']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Kontuzję';
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
            <label for="id_zawodnika">Zawodnik:</label>
            <select id="id_zawodnika" name="id_zawodnika" required>
                <option value="">-- Wybierz zawodnika --</option>
                <?php foreach ($zawodnicy as $zawodnik): ?>
                    <option value="<?= $zawodnik['id_zawodnika'] ?>" <?= ($kontuzja['id_zawodnika'] == $zawodnik['id_zawodnika']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="typ_kontuzji">Typ kontuzji:</label>
            <input type="text" id="typ_kontuzji" name="typ_kontuzji" value="<?= htmlspecialchars($kontuzja['typ_kontuzji']) ?>" required>
        </div>
        <div>
            <label for="data_rozpoczecia">Data rozpoczęcia:</label>
            <input type="date" id="data_rozpoczecia" name="data_rozpoczecia" value="<?= htmlspecialchars($kontuzja['data_rozpoczecia']) ?>" required>
        </div>
        <div>
            <label for="data_zakonczenia">Data zakończenia:</label>
            <input type="date" id="data_zakonczenia" name="data_zakonczenia" value="<?= htmlspecialchars($kontuzja['data_zakonczenia']) ?>">
        </div>
        <div>
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">-- Wybierz status --</option>
                <option value="aktywna" <?= ($kontuzja['status'] == 'aktywna') ? 'selected' : '' ?>>Aktywna</option>
                <option value="wyleczona" <?= ($kontuzja['status'] == 'wyleczona') ? 'selected' : '' ?>>Wyleczona</option>
                <option value="nieznany" <?= ($kontuzja['status'] == 'nieznany') ? 'selected' : '' ?>>Nieznany</option>
            </select>
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj kontuzję' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>