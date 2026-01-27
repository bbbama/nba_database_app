<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';

// Tylko administratorzy mogą przeglądać tę stronę
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once $basePath . 'db.php';

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id_uzytkownika, login, rola FROM uzytkownicy ORDER BY id_uzytkownika');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu listy użytkowników: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Użytkownikami';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Zarządzanie Użytkownikami</h2>
    <p><a href="form.php" class="button">Dodaj nowego użytkownika</a></p>

    <?php if (!empty($users)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Rola</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id_uzytkownika']) ?></td>
                        <td><?= htmlspecialchars($user['login']) ?></td>
                        <td><?= htmlspecialchars($user['rola']) ?></td>
                        <td>
                            <a href="form.php?id=<?= $user['id_uzytkownika'] ?>" class="button edit">Edytuj</a>
                            <?php if ($_SESSION['user_id'] !== $user['id_uzytkownika']): ?>
                                <a href="delete.php?id=<?= $user['id_uzytkownika'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">Usuń</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak zarejestrowanych użytkowników.</p>
    <?php endif; ?>

</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
