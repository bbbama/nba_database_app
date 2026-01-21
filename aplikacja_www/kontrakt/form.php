<?php
require_once '../db.php';

$pdo = getDbConnection();

$kontrakt = [
    'id_kontraktu' => '',
    'id_zawodnika' => '',
    'id_zespolu' => '',
    'data_poczatek' => '',
    'data_koniec' => '',
    'wynagrodzenie_roczne' => ''
];
$errors = [];
$is_edit = false;

// Pobranie listy zawodników i zespołów do dropdownów
try {
    $zawodnicy_stmt = $pdo->query('SELECT id_zawodnika, imie, nazwisko FROM zawodnik ORDER BY nazwisko, imie');
    $zawodnicy = $zawodnicy_stmt->fetchAll(PDO::FETCH_ASSOC);

    $zespoly_stmt = $pdo->query('SELECT id_zespolu, nazwa FROM zespol ORDER BY nazwa');
    $zespoly = $zespoly_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Błąd przy pobieraniu danych do formularza: " . $e->getMessage();
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_kontraktu = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM kontrakt WHERE id_kontraktu = ?');
        $stmt->execute([$id_kontraktu]);
        $kontrakt = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$kontrakt) {
            die('Nie znaleziono kontraktu o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych kontraktu: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kontrakt['id_zawodnika'] = $_POST['id_zawodnika'] ?? '';
    $kontrakt['id_zespolu'] = $_POST['id_zespolu'] ?? '';
    $kontrakt['data_poczatek'] = $_POST['data_poczatek'] ?? '';
    $kontrakt['data_koniec'] = $_POST['data_koniec'] ?? '';
    $kontrakt['wynagrodzenie_roczne'] = $_POST['wynagrodzenie_roczne'] ?? '';

    // Walidacja
    if (empty($kontrakt['id_zawodnika'])) $errors[] = 'Zawodnik jest wymagany.';
    if (empty($kontrakt['id_zespolu'])) $errors[] = 'Zespół jest wymagany.';
    if (empty($kontrakt['data_poczatek'])) $errors[] = 'Data początku kontraktu jest wymagana.';
    if (empty($kontrakt['data_koniec'])) $errors[] = 'Data końca kontraktu jest wymagana.';
    if ($kontrakt['data_poczatek'] > $kontrakt['data_koniec']) $errors[] = 'Data początku kontraktu nie może być późniejsza niż data końca.';
    if (empty($kontrakt['wynagrodzenie_roczne']) || !is_numeric($kontrakt['wynagrodzenie_roczne']) || $kontrakt['wynagrodzenie_roczne'] < 0) $errors[] = 'Wynagrodzenie roczne jest wymagane i musi być nieujemną liczbą.';

    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE kontrakt SET id_zawodnika = ?, id_zespolu = ?, data_poczatek = ?, data_koniec = ?, wynagrodzenie_roczne = ? WHERE id_kontraktu = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $kontrakt['id_zawodnika'],
                    $kontrakt['id_zespolu'],
                    $kontrakt['data_poczatek'],
                    $kontrakt['data_koniec'],
                    $kontrakt['wynagrodzenie_roczne'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO kontrakt (id_zawodnika, id_zespolu, data_poczatek, data_koniec, wynagrodzenie_roczne) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $kontrakt['id_zawodnika'],
                    $kontrakt['id_zespolu'],
                    $kontrakt['data_poczatek'],
                    $kontrakt['data_koniec'],
                    $kontrakt['wynagrodzenie_roczne']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Kontrakt';
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
                    <option value="<?= $zawodnik['id_zawodnika'] ?>" <?= ($kontrakt['id_zawodnika'] == $zawodnik['id_zawodnika']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="id_zespolu">Zespół:</label>
            <select id="id_zespolu" name="id_zespolu" required>
                <option value="">-- Wybierz zespół --</option>
                <?php foreach ($zespoly as $zespol): ?>
                    <option value="<?= $zespol['id_zespolu'] ?>" <?= ($kontrakt['id_zespolu'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zespol['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="data_poczatek">Data początku:</label>
            <input type="date" id="data_poczatek" name="data_poczatek" value="<?= htmlspecialchars($kontrakt['data_poczatek']) ?>" required>
        </div>
        <div>
            <label for="data_koniec">Data końca:</label>
            <input type="date" id="data_koniec" name="data_koniec" value="<?= htmlspecialchars($kontrakt['data_koniec']) ?>" required>
        </div>
        <div>
            <label for="wynagrodzenie_roczne">Wynagrodzenie roczne:</label>
            <input type="number" step="0.01" id="wynagrodzenie_roczne" name="wynagrodzenie_roczne" value="<?= htmlspecialchars($kontrakt['wynagrodzenie_roczne']) ?>" required min="0">
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj kontrakt' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>