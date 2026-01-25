<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'nazwisko_zawodnika'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'asc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['nazwisko_zawodnika', 'typ_kontuzji', 'data_rozpoczecia', 'data_zakonczenia', 'status'];
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

$kontuzje = [];
try {
    $pdo = getDbConnection();
    $query = "SELECT k.id_kontuzji, z.imie, z.nazwisko, k.typ_kontuzji, k.data_rozpoczecia, k.data_zakonczenia, k.status, z.nazwisko AS nazwisko_zawodnika FROM kontuzja k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika";
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(z.imie ILIKE :search OR z.nazwisko ILIKE :search OR k.typ_kontuzji ILIKE :search OR k.status ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $kontuzje = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu kontuzji: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Kontuzjami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Kontuzji</h2>
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po zawodniku, typie lub statusie" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
    <p><a href="form.php" class="button">Dodaj nową kontuzję</a></p>
    <?php endif; ?>
    <?php if (!empty($kontuzje)): ?>
        <table>
            <thead>
                <tr>
                    <th><?= getSortLink('nazwisko_zawodnika', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('typ_kontuzji', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_rozpoczecia', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_zakonczenia', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('status', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kontuzje as $kontuzja): ?>
                <tr>
                    <td><?= htmlspecialchars($kontuzja['imie'] . ' ' . $kontuzja['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($kontuzja['typ_kontuzji']) ?></td>
                    <td><?= htmlspecialchars($kontuzja['data_rozpoczecia']) ?></td>
                    <td><?= htmlspecialchars($kontuzja['data_zakonczenia']) ?></td>
                    <td><?= htmlspecialchars($kontuzja['status']) ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>" class="button delete">Usuń</a>
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
                    <th><?= getSortLink('typ_kontuzji', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_rozpoczecia', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_zakonczenia', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('status', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $isAdmin ? '6' : '5' ?>">Brak kontuzji w bazie danych.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>