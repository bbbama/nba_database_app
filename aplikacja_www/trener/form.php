<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_admin();
require_once $basePath . 'db.php';

$pdo = getDbConnection();

$trener = [
    'id_trenera' => '',
    'imie' => '',
    'nazwisko' => '',
    'rola' => '',
    'id_zespolu' => null
];
$errors = [];
$is_edit = false;

// Pobranie listy zespołów do dropdowna
try {
    $zespoly_stmt = $pdo->query('SELECT id_zespolu, nazwa FROM zespol ORDER BY nazwa');
    $zespoly = $zespoly_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Błąd przy pobieraniu listy zespołów: " . $e->getMessage();
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_trenera = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM trener WHERE id_trenera = ?');
        $stmt->execute([$id_trenera]);
        $trener = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$trener) {
            die('Nie znaleziono trenera o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych trenera: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    $trener['imie'] = $_POST['imie'] ?? '';
    $trener['nazwisko'] = $_POST['nazwisko'] ?? '';
    $trener['rola'] = $_POST['rola'] ?? '';
    $trener['id_zespolu'] = !empty($_POST['id_zespolu']) ? $_POST['id_zespolu'] : null;

    // Walidacja
    if (empty($trener['imie'])) $errors[] = 'Imię jest wymagane.';
    if (empty($trener['nazwisko'])) $errors[] = 'Nazwisko jest wymagane.';
    if (empty($trener['rola'])) $errors[] = 'Rola jest wymagana.';
    
    // Lista dozwolonych ról
    $dostepne_role = ['glowny', 'asystent', 'przygotowanie fizyczne'];
    if (!in_array($trener['rola'], $dostepne_role)) {
        $errors[] = 'Nieprawidłowa rola. Dozwolone role to: ' . implode(', ', $dostepne_role) . '.';
    }


    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE trener SET imie = ?, nazwisko = ?, rola = ?, id_zespolu = ? WHERE id_trenera = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $trener['imie'],
                    $trener['nazwisko'],
                    $trener['rola'],
                    $trener['id_zespolu'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO trener (imie, nazwisko, rola, id_zespolu) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $trener['imie'],
                    $trener['nazwisko'],
                    $trener['rola'],
                    $trener['id_zespolu']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Trenera';
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
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" value="<?= htmlspecialchars($trener['imie']) ?>" required>
        </div>
        <div>
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" value="<?= htmlspecialchars($trener['nazwisko']) ?>" required>
        </div>
        <div>
            <label for="rola">Rola:</label>
            <select id="rola" name="rola" required>
                <option value="">-- Wybierz rolę --</option>
                <option value="glowny" <?= ($trener['rola'] == 'glowny') ? 'selected' : '' ?>>Główny</option>
                <option value="asystent" <?= ($trener['rola'] == 'asystent') ? 'selected' : '' ?>>Asystent</option>
                <option value="przygotowanie fizyczne" <?= ($trener['rola'] == 'przygotowanie fizyczne') ? 'selected' : '' ?>>Przygotowanie fizyczne</option>
            </select>
        </div>
        <div>
            <label for="id_zespolu">Zespół:</label>
            <select id="id_zespolu" name="id_zespolu">
                <option value="">-- Brak zespołu --</option>
                <?php foreach ($zespoly as $zespol): ?>
                    <option value="<?= $zespol['id_zespolu'] ?>" <?= ($trener['id_zespolu'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($zespol['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj trenera' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>