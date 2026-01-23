<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$kontrakty = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT k.id_kontraktu, z.imie, z.nazwisko, zes.nazwa AS nazwa_zespolu, k.data_poczatek, k.data_koniec, k.wynagrodzenie_roczne FROM kontrakt k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika JOIN zespol zes ON k.id_zespolu = zes.id_zespolu ORDER BY k.data_koniec DESC");
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
    <?php if ($isAdmin): ?>
    <p><a href="form.php">Dodaj nowy kontrakt</a></p>
    <?php endif; ?>
    <?php if (!empty($kontrakty)): ?>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Zespół</th>
                    <th>Data początek</th>
                    <th>Data koniec</th>
                    <th>Wynagrodzenie roczne</th>
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
                        <a href="form.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>">Edytuj</a>
                        <a href="delete.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>" class="button-delete">Usuń</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak kontraktów w bazie danych.</p>
    <?php endif; ?>

<?php require_once $basePath . 'layout/footer.php'; ?>