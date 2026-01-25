<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'nazwa'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'asc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['nazwa', 'miasto', 'rok_zalozenia', 'trener_glowny', 'nazwa_areny'];
if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'nazwa';
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

try {
    $pdo = getDbConnection();
    // Zapytanie łączące zespół z areną, aby wyświetlić nazwę areny
    $query = '
        SELECT z.id_zespolu, z.nazwa, z.miasto, z.rok_zalozenia, z.trener_glowny, a.nazwa AS nazwa_areny
        FROM zespol z
        LEFT JOIN arena a ON z.id_arena = a.id_arena
    ';
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = '(z.nazwa ILIKE :search OR z.miasto ILIKE :search OR z.trener_glowny ILIKE :search OR a.nazwa ILIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po nazwie, mieście, trenerze lub arenie" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
        <a href="form.php" class="button">Dodaj nowy zespół</a>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th><?= getSortLink('nazwa', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('miasto', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('rok_zalozenia', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('trener_glowny', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('nazwa_areny', $sort_by, $sort_order, $search) ?></th>
                <?php if ($isAdmin): ?>
                <th>Akcje</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($zespoly) > 0): ?>
                <?php foreach ($zespoly as $zespol): ?>
                <tr>
                    <td>
                        <a href="details.php?id=<?= $zespol['id_zespolu'] ?>">
                            <?= htmlspecialchars($zespol['nazwa']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($zespol['miasto']) ?></td>
                    <td><?= htmlspecialchars($zespol['rok_zalozenia']) ?></td>
                    <td><?= htmlspecialchars($zespol['trener_glowny']) ?></td>
                    <td><?= htmlspecialchars($zespol['nazwa_areny'] ?? 'Brak danych') ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                            <a href="form.php?id=<?= $zespol['id_zespolu'] ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= $zespol['id_zespolu'] ?>" class="button delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isAdmin ? '6' : '5' ?>">Brak danych o zespołach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>