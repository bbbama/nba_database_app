<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'nazwa'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'asc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['nazwa', 'miasto', 'pojemnosc', 'rok_otwarcia'];
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
    $query = 'SELECT id_arena, nazwa, miasto, pojemnosc, rok_otwarcia FROM arena';
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = '(nazwa ILIKE :search OR miasto ILIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $areny = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Arenami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Aren</h2>
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po nazwie lub mieście" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
    <a href="form.php" class="button">Dodaj nową arenę</a>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th><?= getSortLink('nazwa', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('miasto', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('pojemnosc', $sort_by, $sort_order, $search) ?></th>
                <th><?= getSortLink('rok_otwarcia', $sort_by, $sort_order, $search) ?></th>
                <?php if ($isAdmin): ?>
                <th>Akcje</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($areny) > 0): ?>
                <?php foreach ($areny as $arena): ?>
                <tr>
                    <td><?= htmlspecialchars($arena['nazwa']) ?></td>
                    <td><?= htmlspecialchars($arena['miasto']) ?></td>
                    <td><?= htmlspecialchars($arena['pojemnosc']) ?></td>
                    <td><?= htmlspecialchars($arena['rok_otwarcia']) ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= $arena['id_arena'] ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= $arena['id_arena'] ?>" class="button delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isAdmin ? '5' : '4' ?>">Brak danych o arenach w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>