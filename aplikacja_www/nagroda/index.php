<?php
require_once '../db.php';

$nagrody = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT n.id_nagrody, z.imie, z.nazwisko, n.nazwa_nagrody, n.rok FROM nagroda n JOIN zawodnik z ON n.id_zawodnika = z.id_zawodnika ORDER BY n.rok DESC, n.nazwa_nagrody ASC");
    $nagrody = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu nagród: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Nagrodami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Nagród</h2>
    <p><a href="form.php">Dodaj nową nagrodę</a></p>
    <?php if (!empty($nagrody)): ?>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Nazwa nagrody</th>
                    <th>Rok</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nagrody as $nagroda): ?>
                <tr>
                    <td><?= htmlspecialchars($nagroda['imie'] . ' ' . $nagroda['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($nagroda['nazwa_nagrody']) ?></td>
                    <td><?= htmlspecialchars($nagroda['rok']) ?></td>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę nagrodę?');">Usuń</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak nagród w bazie danych.</p>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>