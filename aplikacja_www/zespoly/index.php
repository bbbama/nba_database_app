<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    // Zapytanie łączące zespół z areną, aby wyświetlić nazwę areny
    $stmt = $pdo->query('
        SELECT z.id_zespolu, z.nazwa, z.miasto, z.rok_zalozenia, z.trener_glowny, a.nazwa AS nazwa_areny
        FROM zespol z
        LEFT JOIN arena a ON z.id_arena = a.id_arena
        ORDER BY z.nazwa
    ');
    $zespoly = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Zespołami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Zespołów</h2>
    <a href="form.php" class="button">Dodaj nowy zespół</a>
    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Miasto</th>
                <th>Rok Założenia</th>
                <th>Główny Trener</th>
                <th>Arena</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($zespoly) > 0): ?>
                <?php foreach ($zespoly as $zespol): ?>
                <tr>
                    <td><?= htmlspecialchars($zespol['nazwa']) ?></td>
                    <td><?= htmlspecialchars($zespol['miasto']) ?></td>
                    <td><?= htmlspecialchars($zespol['rok_zalozenia']) ?></td>
                    <td><?= htmlspecialchars($zespol['trener_glowny']) ?></td>
                    <td><?= htmlspecialchars($zespol['nazwa_areny'] ?? 'Brak danych') ?></td>
                    <td>
                        <a href="form.php?id=<?= $zespol['id_zespolu'] ?>" class="button edit">Edytuj</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Brak danych o zespołach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>