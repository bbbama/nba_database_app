<?php
require_once '../db.php';

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
    <p><a href="form.php">Dodaj nowego trenera</a></p>
    <?php if (!empty($trenerzy)): ?>
        <table>
            <thead>
                <tr>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Rola</th>
                    <th>Zespół</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trenerzy as $trener): ?>
                <tr>
                    <td><?= htmlspecialchars($trener['imie']) ?></td>
                    <td><?= htmlspecialchars($trener['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($trener['rola']) ?></td>
                    <td><?= htmlspecialchars($trener['nazwa_zespolu'] ?? 'Brak') ?></td>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($trener['id_trenera']) ?>">Edytuj</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak trenerów w bazie danych.</p>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>