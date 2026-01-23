<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

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
    <?php if ($isAdmin): ?>
    <a href="form.php" class="button">Dodaj nową arenę</a>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Miasto</th>
                <th>Pojemność</th>
                <th>Rok otwarcia</th>
                <?php if ($isAdmin): ?>
                <th>Akcje</th>
                <?php endif; ?>
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
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= $arena['id_arena'] ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= $arena['id_arena'] ?>" class="button-delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isAdmin ? '5' : '4' ?>">Brak danych o arenach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>