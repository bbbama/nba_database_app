<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

try {
    $pdo = getDbConnection();
    // Używamy widoku, aby pobrać dane meczów wraz z nazwami zespołów
    $stmt = $pdo->query('SELECT id_meczu, data_meczu, sezon, gospodarz, wynik_gospodarza, gosc, wynik_goscia FROM widok_wyniki_meczy ORDER BY data_meczu DESC');
    $mecze = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Meczami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Meczów</h2>
    <?php if ($isAdmin): ?>
    <a href="form.php" class="button">Dodaj nowy mecz</a>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Sezon</th>
                <th>Gospodarz</th>
                <th>Wynik</th>
                <th>Gość</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($mecze) > 0): ?>
                <?php foreach ($mecze as $mecz): ?>
                <tr>
                    <td>
                        <a href="details.php?id=<?= $mecz['id_meczu'] ?>">
                            <?= htmlspecialchars($mecz['data_meczu']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($mecz['sezon']) ?></td>
                    <td><?= htmlspecialchars($mecz['gospodarz']) ?></td>
                    <td><?= htmlspecialchars($mecz['wynik_gospodarza']) ?> : <?= htmlspecialchars($mecz['wynik_goscia']) ?></td>
                    <td><?= htmlspecialchars($mecz['gosc']) ?></td>
                    <td>
                        <a href="../statystyki/index.php?id_meczu=<?= $mecz['id_meczu'] ?>" class="button">Statystyki</a>
                        <?php if ($isAdmin): ?>
                        <a href="form.php?id=<?= $mecz['id_meczu'] ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= $mecz['id_meczu'] ?>" class="button delete">Usuń</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Brak danych o meczach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>