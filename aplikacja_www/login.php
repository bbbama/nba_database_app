<?php
session_start();
require_once 'db.php';

$error = '';

// Jeśli użytkownik jest już zalogowany, przekieruj go na stronę główną
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Login i hasło są wymagane.';
    } else {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE login = ?');
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['hash_hasla'])) {
                // Hasło poprawne, logowanie użytkownika
                $_SESSION['user_id'] = $user['id_uzytkownika'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_role'] = $user['rola'];

                // Regeneracja ID sesji dla bezpieczeństwa
                session_regenerate_id(true);

                header('Location: index.php');
                exit;
            } else {
                // Błędny login lub hasło
                $error = 'Nieprawidłowy login lub hasło.';
            }
        } catch (PDOException $e) {
            $error = 'Błąd bazy danych: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Logowanie';
// Używamy uproszczonego nagłówka, ponieważ nawigacja nie powinna być jeszcze dostępna
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - System Bazy Danych NBA</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>System Bazy Danych NBA - Logowanie</h1>
    </header>

    <main class="login-form">
        <h2>Zaloguj się</h2>
        <?php if (isset($_GET['registration_success'])): ?>
            <p class="success">Rejestracja zakończona sukcesem! Możesz się teraz zalogować.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="errors"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div>
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div>
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">Zaloguj</button>
            </div>
        </form>
        <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>

    <footer>
        <p>&copy; <?= date('Y') ?> System Bazy Danych NBA</p>
    </footer>
</body>
</html>
