<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$trenerzy = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT t.id_trenera, t.imie, t.nazwisko, t.rola, z.nazwa AS nazwa_zespolu FROM trener t LEFT JOIN zespol z ON t.id_zespolu = z.id_zespolu ORDER BY t.nazwisko, t.imie ASC");
    $trenerzy = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu trenerów: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Trenerami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Trenerów</h2>
    <?php if ($isAdmin): ?>
    <p><a href="form.php" class="button">Dodaj nowego trenera</a></p>
    <?php endif; ?>
    <?php if (!empty($trenerzy)): ?>
        <table>
            <thead>
                <tr>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Rola</th>
                    <th>Zespół</th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trenerzy as $trener): ?>
                <tr>
                    <td><?= htmlspecialchars($trener['imie']) ?></td>
                    <td><?= htmlspecialchars($trener['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($trener['rola']) ?></td>
                    <td><?= htmlspecialchars($trener['nazwa_zespolu'] ?? 'Brak') ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($trener['id_trenera']) ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($trener['id_trenera']) ?>" class="button delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Rola</th>
                    <th>Zespół</th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $isAdmin ? '5' : '4' ?>">Brak trenerów w bazie danych.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>