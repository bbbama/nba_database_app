<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once $basePath . 'db.php';

$id = $_GET['id'] ?? null;
$userData = ['login' => '', 'rola' => 'user'];
$pageTitle = 'Dodaj Użytkownika';
$errors = [];

if ($id) {
    $pageTitle = 'Edytuj Użytkownika';
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT login, rola FROM uzytkownicy WHERE id_uzytkownika = ?");
        $stmt->execute([$id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userData) {
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Błąd przy pobieraniu danych użytkownika: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $rola = $_POST['rola'] ?? 'user';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($login)) {
        $errors[] = 'Login jest wymagany.';
    }
    if (!in_array($rola, ['admin', 'user'])) {
        $errors[] = 'Nieprawidłowa rola.';
    }

    if ($id) { // Edycja
        if (!empty($password) && $password !== $password_confirm) {
            $errors[] = 'Hasła nie są zgodne.';
        }
    } else { // Dodawanie
        if (empty($password) || $password !== $password_confirm) {
            $errors[] = 'Hasło jest wymagane i musi być zgodne.';
        }
    }

    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            if ($id) { // UPDATE
                $sql = "UPDATE uzytkownicy SET login = ?, rola = ?";
                $params = [$login, $rola];
                if (!empty($password)) {
                    $sql .= ", hash_hasla = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id_uzytkownika = ?";
                $params[] = $id;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            } else { // INSERT
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO uzytkownicy (login, rola, hash_hasla) VALUES (?, ?, ?)");
                $stmt->execute([$login, $rola, $hash]);
            }
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            // Obsługa błędu duplikatu loginu
            if ($e->getCode() == 23505) {
                $errors[] = "Użytkownik o podanym loginie już istnieje.";
            } else {
                die("Błąd zapisu do bazy danych: " . $e->getMessage());
            }
        }
    }
}

require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2><?= $pageTitle ?></h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($userData['login']) ?>" required>
        </div>
        <div>
            <label for="rola">Rola:</label>
            <select id="rola" name="rola">
                <option value="user" <?= $userData['rola'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $userData['rola'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div>
            <label for="password">Nowe hasło (pozostaw puste, aby nie zmieniać):</label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <label for="password_confirm">Potwierdź nowe hasło:</label>
            <input type="password" id="password_confirm" name="password_confirm">
        </div>
        <div>
            <button type="submit"><?= $id ? 'Zapisz zmiany' : 'Dodaj użytkownika' ?></button>
            <a href="index.php" class="button">Anuluj</a>
        </div>
    </form>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
