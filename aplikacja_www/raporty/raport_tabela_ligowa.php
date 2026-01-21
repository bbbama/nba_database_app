<?php
require_once '../db.php';

$pdo = getDbConnection();

$seasons = [];
try {
    $stmt = $pdo->query("SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC");
    $seasons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Błąd odczytu sezonów: " . $e->getMessage();
}

$selected_season_id = $_GET['sezon_id'] ?? null;
$league_table = [];

if ($selected_season_id) {
    try {
        $sql = "SELECT
                    wt.miejsce_w_tabeli,
                    wt.nazwa_zespolu,
                    wt.liczba_zwyciestw,
                    wt.liczba_porazek,
                    wt.procent_zwyciestw
                FROM widok_tabeli_ligowej wt
                WHERE wt.id_sezonu = :sezon_id
                ORDER BY wt.miejsce_w_tabeli ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':sezon_id', $selected_season_id, PDO::PARAM_INT);
        $stmt->execute();
        $league_table = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Błąd odczytu tabeli ligowej: " . $e->getMessage();
    }
}

$pageTitle = 'Tabela ligowa dla sezonu';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Wybierz sezon:</h2>
    <form action="" method="get">
        <label for="sezon_id">Sezon:</label>
        <select name="sezon_id" id="sezon_id" onchange="this.form.submit()">
            <option value="">-- Wybierz sezon --</option>
            <?php foreach ($seasons as $season): ?>
                <option value="<?= htmlspecialchars($season['id_sezonu']) ?>"
                    <?= ($selected_season_id == $season['id_sezonu']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($season['rok_rozpoczecia']) ?>/<?= htmlspecialchars($season['rok_zakonczenia']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selected_season_id && !empty($league_table)): ?>
        <h3>Tabela ligowa dla sezonu <?= htmlspecialchars($seasons[array_search($selected_season_id, array_column($seasons, 'id_sezonu'))]['rok_rozpoczecia']) ?>/<?= htmlspecialchars($seasons[array_search($selected_season_id, array_column($seasons, 'id_sezonu'))]['rok_zakonczenia']) ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Miejsce</th>
                    <th>Zespół</th>
                    <th>Zwycięstwa</th>
                    <th>Porażki</th>
                    <th>Procent Zwycięstw</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($league_table as $team): ?>
                <tr>
                    <td><?= htmlspecialchars($team['miejsce_w_tabeli']) ?></td>
                    <td><?= htmlspecialchars($team['nazwa_zespolu']) ?></td>
                    <td><?= htmlspecialchars($team['liczba_zwyciestw']) ?></td>
                    <td><?= htmlspecialchars($team['liczba_porazek']) ?></td>
                    <td><?= htmlspecialchars(sprintf("%.3f", $team['procent_zwyciestw'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($selected_season_id && empty($league_table)): ?>
        <p>Brak danych tabeli ligowej dla wybranego sezonu.</p>
    <?php else: ?>
        <p>Wybierz sezon, aby zobaczyć tabelę ligową.</p>
    <?php endif; ?>

    <a href="index.php">Powrót do listy raportów</a>
<?php require_once $basePath . 'layout/footer.php'; ?>