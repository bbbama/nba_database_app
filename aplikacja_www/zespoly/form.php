<?php
require_once '../db.php';
session_start();

$pdo = getDbConnection();

$zespol = [
    'id_zespolu' => '',
    'nazwa' => '',
    'miasto' => '',
    'rok_zalozenia' => '',
    'trener_glowny' => '',
    'id_arena' => null
];
$errors = [];
$is_edit = false;

// Pobranie listy aren do dropdowna
try {
    $areny_stmt = $pdo->query('SELECT id_arena, nazwa FROM arena ORDER BY nazwa');
    $areny = $areny_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Jeśli tabela arena nie istnieje, traktujemy listę jako pustą.
    // To pozwala na pracę nad zespołami, nawet jeśli moduł aren nie jest jeszcze gotowy.
    $areny = [];
}

// Tryb edycji
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_zespolu = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM zespol WHERE id_zespolu = ?');
        $stmt->execute([$id_zespolu]);
        $zespol = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$zespol) {
            die('Nie znaleziono zespołu o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych zespołu: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Weryfikacja tokenu CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Błąd CSRF: Nieprawidłowy token.');
    }

    $zespol['nazwa'] = $_POST['nazwa'] ?? '';
    $zespol['miasto'] = $_POST['miasto'] ?? '';
    $zespol['rok_zalozenia'] = $_POST['rok_zalozenia'] ?? '';
    $zespol['trener_glowny'] = $_POST['trener_glowny'] ?? '';
    $zespol['id_arena'] = !empty($_POST['id_arena']) ? $_POST['id_arena'] : null;

    if (empty($zespol['nazwa'])) $errors[] = 'Nazwa zespołu jest wymagana.';

    if (empty($errors)) {
        try {
            if ($is_edit) {
                $sql = "UPDATE zespol SET nazwa = ?, miasto = ?, rok_zalozenia = ?, trener_glowny = ?, id_arena = ? WHERE id_zespolu = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $zespol['nazwa'],
                    $zespol['miasto'],
                    empty($zespol['rok_zalozenia']) ? null : $zespol['rok_zalozenia'],
                    $zespol['trener_glowny'],
                    $zespol['id_arena'],
                    $_GET['id']
                ]);
            } else {
                $sql = "INSERT INTO zespol (nazwa, miasto, rok_zalozenia, trener_glowny, id_arena) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $zespol['nazwa'],
                    $zespol['miasto'],
                    empty($zespol['rok_zalozenia']) ? null : $zespol['rok_zalozenia'],
                    $zespol['trener_glowny'],
                    $zespol['id_arena']
                ]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}

$pageTitle = ($is_edit ? 'Edytuj' : 'Dodaj') . ' Zespół';
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
            <input type="text" id="nazwa" name="nazwa" value="<?= htmlspecialchars($zespol['nazwa']) ?>" required>
        </div>
        <div>
            <label for="miasto">Miasto:</label>
            <input type="text" id="miasto" name="miasto" value="<?= htmlspecialchars($zespol['miasto']) ?>">
        </div>
        <div>
            <label for="rok_zalozenia">Rok założenia:</label>
            <input type="number" id="rok_zalozenia" name="rok_zalozenia" value="<?= htmlspecialchars($zespol['rok_zalozenia']) ?>">
        </div>
        <div>
            <label for="trener_glowny">Główny trener:</label>
            <input type="text" id="trener_glowny" name="trener_glowny" value="<?= htmlspecialchars($zespol['trener_glowny']) ?>">
        </div>
        <div>
            <label for="id_arena">Arena:</label>
            <select id="id_arena" name="id_arena">
                <option value="">-- Brak areny --</option>
                <?php foreach ($areny as $arena): ?>
                    <option value="<?= $arena['id_arena'] ?>" <?= ($zespol['id_arena'] == $arena['id_arena']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($arena['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj zespół' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>