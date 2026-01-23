<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$kontuzje = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT k.id_kontuzji, z.imie, z.nazwisko, k.typ_kontuzji, k.data_rozpoczecia, k.data_zakonczenia, k.status FROM kontuzja k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika ORDER BY k.data_rozpoczecia DESC");
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
    <?php if ($isAdmin): ?>
    <p><a href="form.php">Dodaj nową kontuzję</a></p>
    <?php endif; ?>
    <?php if (!empty($kontuzje)): ?>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Typ kontuzji</th>
                    <th>Data rozpoczęcia</th>
                    <th>Data zakończenia</th>
                    <th>Status</th>
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
                        <a href="form.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>" class="button-delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak kontuzji w bazie danych.</p>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>