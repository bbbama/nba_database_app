<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'nazwisko_zawodnika'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'asc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['nazwisko_zawodnika', 'nazwa_nagrody', 'rok'];
if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'nazwisko_zawodnika';
}
if (!in_array($sort_order, ['asc', 'desc'])) {
    $sort_order = 'asc';
}

function getSortLink($column, $current_sort_by, $current_sort_order, $search_param) {
    $order = ($current_sort_by === $column && $current_sort_order === 'asc') ? 'desc' : 'asc';
    $arrow = ($current_sort_by === $column) ? ($current_sort_order === 'asc' ? ' &#9650;' : ' &#9660;') : '';
    $search_query = !empty($search_param) ? '&search=' . urlencode($search_param) : '';
    return '<a href="?sort_by=' . $column . '&sort_order=' . $order . $search_query . '">' . ucfirst(str_replace('_', ' ', $column)) . $arrow . '</a>';
}

$nagrody = [];
try {
    $pdo = getDbConnection();
    $query = "SELECT n.id_nagrody, z.imie, z.nazwisko, n.nazwa_nagrody, n.rok, z.nazwisko AS nazwisko_zawodnika FROM nagroda n JOIN zawodnik z ON n.id_zawodnika = z.id_zawodnika";
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(z.imie ILIKE :search OR z.nazwisko ILIKE :search OR n.nazwa_nagrody ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po zawodniku lub nagrodzie" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
    <p><a href="form.php" class="button">Dodaj nową nagrodę</a></p>
    <?php endif; ?>
    <?php if (!empty($nagrody)): ?>
        <table>
            <thead>
                <tr>
                    <th><?= getSortLink('nazwisko_zawodnika', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('nazwa_nagrody', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('rok', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nagrody as $nagroda): ?>
                <tr>
                    <td><?= htmlspecialchars($nagroda['imie'] . ' ' . $nagroda['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($nagroda['nazwa_nagrody']) ?></td>
                    <td><?= htmlspecialchars($nagroda['rok']) ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>" class="button delete">Usuń</a>
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
                    <th><?= getSortLink('nazwisko_zawodnika', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('nazwa_nagrody', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('rok', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $isAdmin ? '4' : '3' ?>">Brak nagród w bazie danych.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>