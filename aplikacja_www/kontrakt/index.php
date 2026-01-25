<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'nazwisko_zawodnika'; // Domyślne sortowanie
$sort_order = $_GET['sort_order'] ?? 'asc'; // Domyślna kolejność

// Walidacja parametrów sortowania
$allowed_sort_by = ['nazwisko_zawodnika', 'nazwa_zespolu', 'data_poczatek', 'data_koniec', 'wynagrodzenie_roczne'];
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

$kontrakty = [];
try {
    $pdo = getDbConnection();
    $query = "SELECT k.id_kontraktu, z.imie, z.nazwisko, zes.nazwa AS nazwa_zespolu, k.data_poczatek, k.data_koniec, k.wynagrodzenie_roczne, z.nazwisko AS nazwisko_zawodnika FROM kontrakt k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika JOIN zespol zes ON k.id_zespolu = zes.id_zespolu";
    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "(z.imie ILIKE :search OR z.nazwisko ILIKE :search OR zes.nazwa ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY ' . $sort_by . ' ' . $sort_order;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $kontrakty = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu kontraktów: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Kontraktami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Kontraktów</h2>
    <form method="GET" action="index.php" class="form-inline">
        <input type="text" name="search" placeholder="Szukaj po zawodniku lub zespole" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="button">Szukaj</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="button">Resetuj</a>
        <?php endif; ?>
    </form>
    <?php if ($isAdmin): ?>
    <p><a href="form.php" class="button">Dodaj nowy kontrakt</a></p>
    <?php endif; ?>
    <?php if (!empty($kontrakty)): ?>
        <table>
            <thead>
                <tr>
                    <th><?= getSortLink('nazwisko_zawodnika', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('nazwa_zespolu', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_poczatek', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_koniec', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('wynagrodzenie_roczne', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kontrakty as $kontrakt): ?>
                <tr>
                    <td><?= htmlspecialchars($kontrakt['imie'] . ' ' . $kontrakt['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($kontrakt['nazwa_zespolu']) ?></td>
                    <td><?= htmlspecialchars($kontrakt['data_poczatek']) ?></td>
                    <td><?= htmlspecialchars($kontrakt['data_koniec']) ?></td>
                    <td><?= htmlspecialchars(number_format($kontrakt['wynagrodzenie_roczne'], 2, ',', ' ')) ?></td>
                    <?php if ($isAdmin): ?>
                    <td>
                        <a href="form.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>" class="button edit">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>" class="button delete">Usuń</a>
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
                    <th><?= getSortLink('nazwa_zespolu', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_poczatek', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('data_koniec', $sort_by, $sort_order, $search) ?></th>
                    <th><?= getSortLink('wynagrodzenie_roczne', $sort_by, $sort_order, $search) ?></th>
                    <?php if ($isAdmin): ?>
                    <th>Akcje</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="<?= $isAdmin ? '6' : '5' ?>">Brak kontraktów w bazie danych.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>