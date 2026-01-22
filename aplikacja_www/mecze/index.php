<?php
require_once '../db.php';

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
    <a href="form.php" class="button">Dodaj nowy mecz</a>
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
                    <td><?= htmlspecialchars($mecz['data_meczu']) ?></td>
                    <td><?= htmlspecialchars($mecz['sezon']) ?></td>
                    <td><?= htmlspecialchars($mecz['gospodarz']) ?></td>
                    <td><?= htmlspecialchars($mecz['wynik_gospodarza']) ?> : <?= htmlspecialchars($mecz['wynik_goscia']) ?></td>
                    <td><?= htmlspecialchars($mecz['gosc']) ?></td>
                    <td>
                        <a href="../statystyki/index.php?id_meczu=<?= $mecz['id_meczu'] ?>" class="button">Statystyki</a>
                        <a href="form.php?id=<?= $mecz['id_meczu'] ?>" class="button edit">Edytuj</a>
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