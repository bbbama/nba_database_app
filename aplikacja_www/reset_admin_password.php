<?php
require_once 'db.php';

// Nowe hasło dla administratora
$new_password = 'admin';

// Generowanie hasha dla nowego hasła
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

if ($new_password_hash === false) {
    die('Błąd podczas hashowania hasła. Sprawdź konfigurację PHP.');
}

try {
    $pdo = getDbConnection();

    // Sprawdzenie, czy użytkownik 'admin' istnieje
    $stmt = $pdo->prepare("SELECT id_uzytkownika FROM uzytkownicy WHERE login = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch();

    if ($user) {
        // Użytkownik istnieje, aktualizuj hasło
        $update_stmt = $pdo->prepare("UPDATE uzytkownicy SET hash_hasla = ? WHERE login = ?");
        $update_stmt->execute([$new_password_hash, 'admin']);
        
        echo "<h1>Hasło dla użytkownika 'admin' zostało zresetowane!</h1>";
        echo "<p>Twoje nowe hasło to: <strong>" . htmlspecialchars($new_password) . "</strong></p>";
        echo "<p><a href='login.php'>Przejdź do strony logowania</a></p>";
    } else {
        // Użytkownik nie istnieje, utwórz go
        $insert_stmt = $pdo->prepare("INSERT INTO uzytkownicy (login, hash_hasla, rola) VALUES (?, ?, ?)");
        $insert_stmt->execute(['admin', $new_password_hash, 'admin']);
        
        echo "<h1>Użytkownik 'admin' nie istniał, więc został utworzony!</h1>";
        echo "<p>Twoje hasło to: <strong>" . htmlspecialchars($new_password) . "</strong></p>";
        echo "<p><a href='login.php'>Przejdź do strony logowania</a></p>";
    }

    echo "<p style='color: red; font-weight: bold;'>WAŻNE: Po zalogowaniu usuń ten plik (reset_admin_password.php) ze względów bezpieczeństwa!</p>";

} catch (PDOException $e) {
    die("Błąd bazy danych: " . $e->getMessage());
}
?>
