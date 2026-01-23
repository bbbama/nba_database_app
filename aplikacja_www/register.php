<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

// Jeśli użytkownik jest już zalogowany, przekieruj go na stronę główną
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($login) || empty($password) || empty($password_confirm)) {
        $error = 'Wszystkie pola są wymagane.';
    } elseif ($password !== $password_confirm) {
        $error = 'Hasła nie pasują do siebie.';
    } elseif (strlen($password) < 6) {
        $error = 'Hasło musi zawierać co najmniej 6 znaków.';
    } else {
        try {
            $pdo = getDbConnection();
            
            // Sprawdzenie, czy login jest już zajęty
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM uzytkownicy WHERE login = ?');
            $stmt->execute([$login]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Wybrany login jest już zajęty.';
            } else {
                // Hashowanie hasła
                $hash_hasla = password_hash($password, PASSWORD_DEFAULT);

                // Domyślna rola 'user'
                $rola = 'user';

                $stmt = $pdo->prepare('INSERT INTO uzytkownicy (login, hash_hasla, rola) VALUES (?, ?, ?)');
                $stmt->execute([$login, $hash_hasla, $rola]);

                $success = 'Rejestracja zakończona sukcesem! Możesz się teraz zalogować.';
                // Można opcjonalnie przekierować od razu do strony logowania
                header('Location: login.php?registration_success=true');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Błąd bazy danych: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Rejestracja';
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
        <h1>System Bazy Danych NBA - Rejestracja</h1>
    </header>

    <main class="login-form"> <!-- Używamy tej samej klasy CSS dla spójności -->
        <h2>Zarejestruj się</h2>
        <?php if ($error): ?>
            <p class="errors"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div>
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required value="<?= htmlspecialchars($login ?? '') ?>">
            </div>
            <div>
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="password_confirm">Potwierdź hasło:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <div>
                <button type="submit">Zarejestruj się</button>
            </div>
        </form>
        <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> System Bazy Danych NBA</p>
    </footer>
</body>
</html>
