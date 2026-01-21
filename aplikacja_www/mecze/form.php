<?php
require_once '../db.php';

$mecz = [
    'id_meczu' => '',
    'data_meczu' => '',
    'id_gospodarza' => '',
    'id_goscia' => '',
    'wynik_gospodarza' => '',
    'wynik_goscia' => '',
    'id_sezonu' => ''
];
$errors = [];
$is_edit = false;

try {
    $pdo = getDbConnection();
    // Pobranie listy zespołów i sezonów do dropdownów
    $zespoly_stmt = $pdo->query('SELECT id_zespolu, nazwa FROM zespol ORDER BY nazwa');
    $zespoly = $zespoly_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sezony_stmt = $pdo->query('SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC');
    $sezony = $sezony_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych do formularza: " . $e->getMessage());
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_meczu = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM mecz WHERE id_meczu = ?');
        $stmt->execute([$id_meczu]);
        $mecz = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mecz) {
            die('Nie znaleziono meczu o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych meczu: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mecz['data_meczu'] = $_POST['data_meczu'] ?? '';
    $mecz['id_gospodarza'] = $_POST['id_gospodarza'] ?? '';
    $mecz['id_goscia'] = $_POST['id_goscia'] ?? '';
    $mecz['wynik_gospodarza'] = $_POST['wynik_gospodarza'] ?? '';
    $mecz['wynik_goscia'] = $_POST['wynik_goscia'] ?? '';
    $mecz['id_sezonu'] = !empty($_POST['id_sezonu']) ? $_POST['id_sezonu'] : null;

    // Walidacja
    if (empty($mecz['data_meczu'])) $errors[] = 'Data meczu jest wymagana.';
    if (empty($mecz['id_gospodarza'])) $errors[] = 'Zespół gospodarzy jest wymagany.';
    if (empty($mecz['id_goscia'])) $errors[] = 'Zespół gości jest wymagany.';
    if ($mecz['id_gospodarza'] == $mecz['id_goscia']) $errors[] = 'Gospodarz i gość nie mogą być tym samym zespołem.';
    if (!empty($mecz['wynik_gospodarza']) && !is_numeric($mecz['wynik_gospodarza'])) $errors[] = 'Wynik gospodarza musi być liczbą.';
    if (!empty($mecz['wynik_goscia']) && !is_numeric($mecz['wynik_goscia'])) $errors[] = 'Wynik gościa musi być liczbą.';


    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE mecz SET data_meczu = ?, id_gospodarza = ?, id_goscia = ?, wynik_gospodarza = ?, wynik_goscia = ?, id_sezonu = ? WHERE id_meczu = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $mecz['data_meczu'],
                    $mecz['id_gospodarza'],
                    $mecz['id_goscia'],
                    empty($mecz['wynik_gospodarza']) ? null : $mecz['wynik_gospodarza'],
                    empty($mecz['wynik_goscia']) ? null : $mecz['wynik_goscia'],
                    $mecz['id_sezonu'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO mecz (data_meczu, id_gospodarza, id_goscia, wynik_gospodarza, wynik_goscia, id_sezonu) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $mecz['data_meczu'],
                    $mecz['id_gospodarza'],
                    $mecz['id_goscia'],
                    empty($mecz['wynik_gospodarza']) ? null : $mecz['wynik_gospodarza'],
                    empty($mecz['wynik_goscia']) ? null : $mecz['wynik_goscia'],
                    $mecz['id_sezonu']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Mecz';
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
            <label for="data_meczu">Data meczu:</label>
            <input type="date" id="data_meczu" name="data_meczu" value="<?= htmlspecialchars($mecz['data_meczu']) ?>" required>
        </div>
        <div>
            <label for="id_gospodarza">Gospodarz:</label>
            <select id="id_gospodarza" name="id_gospodarza" required>
                <option value="">-- Wybierz zespół --</option>
                <?php foreach ($zespoly as $zespol): ?>
                    <option value="<?= $zespol['id_zespolu'] ?>" <?= ($mecz['id_gospodarza'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zespol['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="id_goscia">Gość:</label>
            <select id="id_goscia" name="id_goscia" required>
                <option value="">-- Wybierz zespół --</option>
                <?php foreach ($zespoly as $zespol): ?>
                    <option value="<?= $zespol['id_zespolu'] ?>" <?= ($mecz['id_goscia'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zespol['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="wynik_gospodarza">Wynik gospodarza:</label>
            <input type="number" id="wynik_gospodarza" name="wynik_gospodarza" value="<?= htmlspecialchars($mecz['wynik_gospodarza']) ?>">
        </div>
        <div>
            <label for="wynik_goscia">Wynik gościa:</label>
            <input type="number" id="wynik_goscia" name="wynik_goscia" value="<?= htmlspecialchars($mecz['wynik_goscia']) ?>">
        </div>
        <div>
            <label for="id_sezonu">Sezon:</label>
            <select id="id_sezonu" name="id_sezonu">
                <option value="">-- Brak sezonu --</option>
                <?php foreach ($sezony as $sezon): ?>
                    <option value="<?= $sezon['id_sezonu'] ?>" <?= ($mecz['id_sezonu'] == $sezon['id_sezonu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sezon['rok_rozpoczecia']) ?>/<?= htmlspecialchars($sezon['rok_zakonczenia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj mecz' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>