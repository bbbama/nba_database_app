<?php
require_once '../db.php';

$pdo = getDbConnection();

try {
    $stmt = $pdo->query('SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC');
    $sezony = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Sezonami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Sezonów</h2>
    <a href="form.php" class="button">Dodaj nowy sezon</a>
    <table>
        <thead>
            <tr>
                <th>ID Sezonu</th>
                <th>Rok rozpoczęcia</th>
                <th>Rok zakończenia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($sezony) > 0): ?>
                <?php foreach ($sezony as $sezon): ?>
                <tr>
                    <td><?= htmlspecialchars($sezon['id_sezonu']) ?></td>
                    <td><?= htmlspecialchars($sezon['rok_rozpoczecia']) ?></td>
                    <td><?= htmlspecialchars($sezon['rok_zakonczenia']) ?></td>
                    <td>
                        <a href="form.php?id=<?= $sezon['id_sezonu'] ?>" class="button edit">Edytuj</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Brak danych o sezonach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>