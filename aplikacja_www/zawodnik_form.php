<?php
require_once 'db.php';

$zawodnik = [
    'id_zawodnika' => '',
    'imie' => '',
    'nazwisko' => '',
    'pozycja' => '',
    'data_urodzenia' => '',
    'id_zespolu' => null
];
$errors = [];
$is_edit = false;

// Pobranie listy zespołów do dropdowna
try {
    $pdo = getDbConnection();
    $zespoly_stmt = $pdo->query('SELECT id_zespolu, nazwa FROM zespol ORDER BY nazwa');
    $zespoly = $zespoly_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu listy zespołów: " . $e->getMessage());
}

// Tryb edycji - jeśli podano ID
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_zawodnika = $_GET['id'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM zawodnik WHERE id_zawodnika = ?');
        $stmt->execute([$id_zawodnika]);
        $zawodnik = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$zawodnik) {
            die('Nie znaleziono zawodnika o podanym ID.');
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych zawodnika: " . $e->getMessage());
    }
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zawodnik['imie'] = $_POST['imie'] ?? '';
    $zawodnik['nazwisko'] = $_POST['nazwisko'] ?? '';
    $zawodnik['pozycja'] = $_POST['pozycja'] ?? '';
    $zawodnik['data_urodzenia'] = $_POST['data_urodzenia'] ?? '';
    $zawodnik['id_zespolu'] = !empty($_POST['id_zespolu']) ? $_POST['id_zespolu'] : null;

    // Prosta walidacja
    if (empty($zawodnik['imie'])) $errors[] = 'Imię jest wymagane.';
    if (empty($zawodnik['nazwisko'])) $errors[] = 'Nazwisko jest wymagane.';

    if (empty($errors)) {
        try {
            if ($is_edit) {
                // Aktualizacja
                $sql = "UPDATE zawodnik SET imie = ?, nazwisko = ?, pozycja = ?, data_urodzenia = ?, id_zespolu = ? WHERE id_zawodnika = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $zawodnik['imie'],
                    $zawodnik['nazwisko'],
                    $zawodnik['pozycja'],
                    empty($zawodnik['data_urodzenia']) ? null : $zawodnik['data_urodzenia'],
                    $zawodnik['id_zespolu'],
                    $_GET['id']
                ]);
            } else {
                // Dodawanie
                $sql = "INSERT INTO zawodnik (imie, nazwisko, pozycja, data_urodzenia, id_zespolu) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $zawodnik['imie'],
                    $zawodnik['nazwisko'],
                    $zawodnik['pozycja'],
                    empty($zawodnik['data_urodzenia']) ? null : $zawodnik['data_urodzenia'],
                    $zawodnik['id_zespolu']
                ]);
            }
            // Przekierowanie po sukcesie
            header('Location: zawodnicy.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Edytuj' : 'Dodaj' ?> Zawodnika</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?= $is_edit ? 'Edytuj' : 'Dodaj' ?> Zawodnika</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="zawodnicy.php">Zawodnicy</a></li>
            <li><a href="zespoly.php">Zespoły</a></li>
            <li><a href="mecze.php">Mecze</a></li>
            <li><a href="raporty.php">Raporty</a></li>
        </ul>
    </nav>
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
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" value="<?= htmlspecialchars($zawodnik['imie']) ?>" required>
            </div>
            <div>
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" value="<?= htmlspecialchars($zawodnik['nazwisko']) ?>" required>
            </div>
            <div>
                <label for="pozycja">Pozycja:</label>
                <input type="text" id="pozycja" name="pozycja" value="<?= htmlspecialchars($zawodnik['pozycja']) ?>">
            </div>
            <div>
                <label for="data_urodzenia">Data urodzenia:</label>
                <input type="date" id="data_urodzenia" name="data_urodzenia" value="<?= htmlspecialchars($zawodnik['data_urodzenia']) ?>">
            </div>
            <div>
                <label for="id_zespolu">Zespół:</label>
                <select id="id_zespolu" name="id_zespolu">
                    <option value="">-- Brak zespołu --</option>
                    <?php foreach ($zespoly as $zespol): ?>
                        <option value="<?= $zespol['id_zespolu'] ?>" <?= ($zawodnik['id_zespolu'] == $zespol['id_zespolu']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($zespol['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit"><?= $is_edit ? 'Zapisz zmiany' : 'Dodaj zawodnika' ?></button>
                <a href="zawodnicy.php" class="button">Anuluj</a>
            </div>
        </form>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
