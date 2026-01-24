<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$tabela_ligowa_entries = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT tl.id_tabeli, s.rok_rozpoczecia, s.rok_zakonczenia, z.nazwa AS nazwa_zespolu, tl.liczba_zwyciestw, tl.liczba_porazek, vtl.miejsce_w_tabeli FROM tabela_ligowa tl JOIN sezon s ON tl.id_sezonu = s.id_sezonu JOIN zespol z ON tl.id_zespolu = z.id_zespolu JOIN widok_tabeli_ligowej vtl ON tl.id_sezonu = vtl.id_sezonu AND tl.id_zespolu = vtl.id_zespolu ORDER BY s.rok_rozpoczecia DESC, vtl.miejsce_w_tabeli ASC");
    $tabela_ligowa_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu wpisów tabeli ligowej: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Tabelą Ligową';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Wpisów Tabeli Ligowej</h2>
    <?php if ($isAdmin): ?>
    <p><a href="form.php" class="button">Dodaj nowy wpis do tabeli ligowej</a></p>
    <?php endif; ?>
    <?php if (!empty($tabela_ligowa_entries)): ?>
        <table>
            <thead>
                <tr>
                    <th>Sezon</th>
                    <th>Zespół</th>
                    <th>Zwycięstwa</th>
                    <th>Porażki</th>
                    <th>Miejsce</th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tabela_ligowa_entries as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['rok_rozpoczecia'] . '/' . $entry['rok_zakonczenia']) ?></td>
                    <td><?= htmlspecialchars($entry['nazwa_zespolu']) ?></td>
                    <td><?= htmlspecialchars($entry['liczba_zwyciestw']) ?></td>
                    <td><?= htmlspecialchars($entry['liczba_porazek']) ?></td>
                    <td><?= htmlspecialchars($entry['miejsce_w_tabeli']) ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($entry['id_tabeli']) ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($entry['id_tabeli']) ?>" class="button delete">Usuń</a>
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
                    <th>Sezon</th>
                    <th>Zespół</th>
                    <th>Zwycięstwa</th>
                    <th>Porażki</th>
                    <th>Miejsce</th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $isAdmin ? '6' : '5' ?>">Brak wpisów w tabeli ligowej w bazie danych.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>