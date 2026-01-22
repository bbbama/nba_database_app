<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id_arena, nazwa, miasto, pojemnosc, rok_otwarcia FROM arena ORDER BY nazwa');
    $areny = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Arenami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Aren</h2>
    <a href="form.php" class="button">Dodaj nową arenę</a>
    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Miasto</th>
                <th>Pojemność</th>
                <th>Rok otwarcia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($areny) > 0): ?>
                <?php foreach ($areny as $arena): ?>
                <tr>
                    <td><?= htmlspecialchars($arena['nazwa']) ?></td>
                    <td><?= htmlspecialchars($arena['miasto']) ?></td>
                    <td><?= htmlspecialchars($arena['pojemnosc']) ?></td>
                    <td><?= htmlspecialchars($arena['rok_otwarcia']) ?></td>
                    <td>
                        <a href="form.php?id=<?= $arena['id_arena'] ?>" class="button edit">Edytuj</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Brak danych o arenach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>