<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$sezony_list = [];
$selected_sezon_id = null;
try {
    $pdo = getDbConnection();
    $stmt_sezony = $pdo->query('SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC');
    $sezony_list = $stmt_sezony->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['sezon_id']) && !empty($_GET['sezon_id'])) {
        $selected_sezon_id = $_GET['sezon_id'];
    } else {
        if (!empty($sezony_list)) {
            $selected_sezon_id = $sezony_list[0]['id_sezonu'];
        }
    }
} catch (PDOException $e) {
    die("Błąd przy pobieraniu sezonów: " . $e->getMessage());
}

$query = "SELECT tl.id_tabeli, s.rok_rozpoczecia, s.rok_zakonczenia, z.nazwa AS nazwa_zespolu, tl.liczba_zwyciestw, tl.liczba_porazek, vtl.miejsce_w_tabeli FROM tabela_ligowa tl JOIN sezon s ON tl.id_sezonu = s.id_sezonu JOIN zespol z ON tl.id_zespolu = z.id_zespolu JOIN widok_tabeli_ligowej vtl ON tl.id_sezonu = vtl.id_sezonu AND tl.id_zespolu = vtl.id_zespolu";
$params = [];

if ($selected_sezon_id) {
    $query .= " WHERE tl.id_sezonu = :selected_sezon_id";
    $params[':selected_sezon_id'] = $selected_sezon_id;
}

$query .= " ORDER BY s.rok_rozpoczecia DESC, vtl.miejsce_w_tabeli ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <form method="GET" action="index.php" class="form-inline">
        <label for="sezon_select">Wybierz sezon:</label>
        <select name="sezon_id" id="sezon_select" onchange="this.form.submit()">
            <?php if (empty($sezony_list)): ?>
                <option value="">Brak dostępnych sezonów</option>
            <?php else: ?>
                <?php foreach ($sezony_list as $sezon): ?>
                    <option value="<?= htmlspecialchars($sezon['id_sezonu']) ?>"
                        <?= ($selected_sezon_id == $sezon['id_sezonu']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sezon['rok_rozpoczecia'] . '/' . $sezon['rok_zakonczenia']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </form>
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