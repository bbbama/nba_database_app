<?php
require_once '../db.php';

$pdo = getDbConnection();

$entry = [
    'id_tabeli' => '',
    'id_sezonu' => '',
    'id_zespolu' => '',
    'liczba_zwyciestw' => '0',
    'liczba_porazek' => '0',
];
$errors = [];
$is_edit = false;

// Pobranie listy sezonów i zespołów do dropdownów
try {
    $sezony_stmt = $pdo->query('SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC');
    $sezony = $sezony_stmt->fetchAll(PDO::FETCH_ASSOC);

    $zespoly_stmt = $pdo->query('SELECT id_zespolu, nazwa FROM zespol ORDER BY nazwa');
    $zespoly = $zespoly_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Błąd przy pobieraniu danych do formularza: " . $e->getMessage();
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_tabeli = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM tabela_ligowa WHERE id_tabeli = ?');
        $stmt->execute([$id_tabeli]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$entry) {
            die('Nie znaleziono wpisu tabeli ligowej o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych wpisu: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry['id_sezonu'] = $_POST['id_sezonu'] ?? '';
    $entry['id_zespolu'] = $_POST['id_zespolu'] ?? '';
    $entry['liczba_zwyciestw'] = $_POST['liczba_zwyciestw'] ?? 0;
    $entry['liczba_porazek'] = $_POST['liczba_porazek'] ?? 0;

    // Walidacja
    if (empty($entry['id_sezonu'])) $errors[] = 'Sezon jest wymagany.';
    if (empty($entry['id_zespolu'])) $errors[] = 'Zespół jest wymagany.';
    if (!is_numeric($entry['liczba_zwyciestw']) || $entry['liczba_zwyciestw'] < 0) $errors[] = 'Liczba zwycięstw musi być nieujemną liczbą.';
    if (!is_numeric($entry['liczba_porazek']) || $entry['liczba_porazek'] < 0) $errors[] = 'Liczba porażek musi być nieujemną liczbą.';

    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE tabela_ligowa SET id_sezonu = ?, id_zespolu = ?, liczba_zwyciestw = ?, liczba_porazek = ? WHERE id_tabeli = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $entry['id_sezonu'],
                    $entry['id_zespolu'],
                    $entry['liczba_zwyciestw'],
                    $entry['liczba_porazek'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO tabela_ligowa (id_sezonu, id_zespolu, liczba_zwyciestw, liczba_porazek) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $entry['id_sezonu'],
                    $entry['id_zespolu'],
                    $entry['liczba_zwyciestw'],
                    $entry['liczba_porazek']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == '23505') { // Unique violation
                $errors[] = "Wpis dla tego sezonu i zespołu już istnieje.";
            } else {
                $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Wpis Tabeli Ligowej';
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
            <label for="id_sezonu">Sezon:</label>
            <select id="id_sezonu" name="id_sezonu" required>
                <option value="">-- Wybierz sezon --</option>
                <?php foreach ($sezony as $sezon): ?>
                    <option value="<?= $sezon['id_sezonu'] ?>" <?= ($entry['id_sezonu'] == $sezon['id_sezonu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sezon['rok_rozpoczecia'] . '/' . $sezon['rok_zakonczenia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="id_zespolu">Zespół:</label>
            <select id="id_zespolu" name="id_zespolu" required>
                <option value="">-- Wybierz zespół --</option>
                <?php foreach ($zespoly as $zespol): ?>
                    <option value="<?= $zespol['id_zespolu'] ?>" <?= ($entry['id_zespolu'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zespol['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="liczba_zwyciestw">Liczba zwycięstw:</label>
            <input type="number" id="liczba_zwyciestw" name="liczba_zwyciestw" value="<?= htmlspecialchars($entry['liczba_zwyciestw']) ?>" required min="0">
        </div>
        <div>
            <label for="liczba_porazek">Liczba porażek:</label>
            <input type="number" id="liczba_porazek" name="liczba_porazek" value="<?= htmlspecialchars($entry['liczba_porazek']) ?>" required min="0">
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj wpis' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>