<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'rok_rozpoczecia'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'desc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['id_sezonu', 'rok_rozpoczecia', 'rok_zakonczenia'];
if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'rok_rozpoczecia';
}
if (!in_array($sort_order, ['asc', 'desc'])) {
    $sort_order = 'desc';
}

function getSortLink($column, $current_sort_by, $current_sort_order, $search_param) {
    $order = ($current_sort_by === $column && $current_sort_order === 'asc') ? 'desc' : 'asc';
    $arrow = ($current_sort_by === $column) ? ($current_sort_order === 'asc' ? ' &#9650;' : ' &#9660;') : '';
    $search_query = !empty($search_param) ? '&search=' . urlencode($search_param) : '';
    return '<a href="?sort_by=' . $column . '&sort_order=' . $order . $search_query . '">' . ucfirst(str_replace('_', ' ', $column)) . $arrow . '</a>';
}

$pdo = getDbConnection();

try {
    $query = 'SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon';
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(CAST(rok_rozpoczecia AS TEXT) ILIKE :search OR CAST(rok_zakonczenia AS TEXT) ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po roku" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
    <a href="form.php" class="button">Dodaj nowy sezon</a>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th><?= getSortLink('id_sezonu', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('rok_rozpoczecia', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('rok_zakonczenia', $sort_by, $sort_order, $search) ?></th>
                <?php if ($isAdmin): ?>
                <th>Akcje</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($sezony) > 0): ?>
                <?php foreach ($sezony as $sezon): ?>
                <tr>
                    <td><?= htmlspecialchars($sezon['id_sezonu']) ?></td>
                    <td>
                        <a href="../tabela_ligowa/index.php?sezon_id=<?= htmlspecialchars($sezon['id_sezonu']) ?>">
                            <?= htmlspecialchars($sezon['rok_rozpoczecia']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="../tabela_ligowa/index.php?sezon_id=<?= htmlspecialchars($sezon['id_sezonu']) ?>">
                            <?= htmlspecialchars($sezon['rok_zakonczenia']) ?>
                        </a>
                    </td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= $sezon['id_sezonu'] ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= $sezon['id_sezonu'] ?>" class="button delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isAdmin ? '4' : '3' ?>">Brak danych o sezonach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>