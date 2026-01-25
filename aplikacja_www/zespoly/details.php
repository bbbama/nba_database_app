<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$id_zespolu = $_GET['id'] ?? null;

if (!$id_zespolu) {
    header('Location: index.php'); // Przekieruj na listę zespołów, jeśli brak ID
    exit;
}

try {
    $pdo = getDbConnection();

    // Pobierz podstawowe dane zespołu i areny
    $stmt_zespol = $pdo->prepare('SELECT z.nazwa, z.miasto, z.rok_zalozenia, z.trener_glowny, a.nazwa AS nazwa_areny, a.miasto AS miasto_areny, a.pojemnosc
                               FROM zespol z
                               LEFT JOIN arena a ON z.id_arena = a.id_arena
                               WHERE z.id_zespolu = ?');
    $stmt_zespol->execute([$id_zespolu]);
    $zespol = $stmt_zespol->fetch(PDO::FETCH_ASSOC);

    if (!$zespol) {
        header('Location: index.php'); // Przekieruj, jeśli zespół nie istnieje
        exit;
    }

    // Pobierz zawodników zespołu
    $stmt_zawodnicy = $pdo->prepare('SELECT id_zawodnika, imie, nazwisko, pozycja FROM zawodnik WHERE id_zespolu = ? ORDER BY nazwisko, imie');
    $stmt_zawodnicy->execute([$id_zespolu]);
    $zawodnicy = $stmt_zawodnicy->fetchAll(PDO::FETCH_ASSOC);

    // Pobierz trenerów zespołu (oprócz głównego trenera, który jest już w danych zespołu)
    $stmt_trenerzy = $pdo->prepare('SELECT id_trenera, imie, nazwisko, rola FROM trener WHERE id_zespolu = ? AND rola != ? ORDER BY nazwisko, imie');
    $stmt_trenerzy->execute([$id_zespolu, $zespol['trener_glowny']]); // Wyklucz głównego trenera
    $trenerzy = $stmt_trenerzy->fetchAll(PDO::FETCH_ASSOC);

    // Pobierz historię sezonów w tabeli ligowej dla tego zespołu
    $stmt_tabela_ligowa = $pdo->prepare('SELECT s.rok_rozpoczecia, s.rok_zakonczenia, tl.liczba_zwyciestw, tl.liczba_porazek, vtl.miejsce_w_tabeli
                                        FROM tabela_ligowa tl
                                        JOIN sezon s ON tl.id_sezonu = s.id_sezonu
                                        JOIN widok_tabeli_ligowej vtl ON tl.id_sezonu = vtl.id_sezonu AND tl.id_zespolu = vtl.id_zespolu
                                        WHERE tl.id_zespolu = ? ORDER BY s.rok_rozpoczecia DESC');
    $stmt_tabela_ligowa->execute([$id_zespolu]);
    $historia_tabeli_ligowej = $stmt_tabela_ligowa->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych zespołu: " . $e->getMessage());
}

$pageTitle = 'Szczegóły Zespołu: ' . htmlspecialchars($zespol['nazwa']);
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2><?= htmlspecialchars($zespol['nazwa']) ?></h2>

    <section class="team-details">
        <h3>Informacje podstawowe</h3>
        <p><strong>Miasto:</strong> <?= htmlspecialchars($zespol['miasto']) ?></p>
        <p><strong>Rok założenia:</strong> <?= htmlspecialchars($zespol['rok_zalozenia']) ?></p>
        <p><strong>Główny trener:</strong> <?= htmlspecialchars($zespol['trener_glowny']) ?></p>
        <p><strong>Arena:</strong> <?= htmlspecialchars($zespol['nazwa_areny'] ?? 'Brak') ?> (<?= htmlspecialchars($zespol['miasto_areny'] ?? 'Brak') ?>, Pojemność: <?= htmlspecialchars($zespol['pojemnosc'] ?? 'Brak') ?>)</p>
    </section>

    <section class="team-players">
        <h3>Zawodnicy</h3>
        <?php if (!empty($zawodnicy)): ?>
            <ul>
                <?php foreach ($zawodnicy as $zawodnik): ?>
                    <li><a href="../zawodnicy/details.php?id=<?= htmlspecialchars($zawodnik['id_zawodnika']) ?>"><?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?></a> (<?= htmlspecialchars($zawodnik['pozycja']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Brak zawodników w zespole.</p>
        <?php endif; ?>
    </section>

    <section class="team-coaches">
        <h3>Pozostali trenerzy</h3>
        <?php if (!empty($trenerzy)): ?>
            <ul>
                <?php foreach ($trenerzy as $trener): ?>
                    <li><?= htmlspecialchars($trener['imie'] . ' ' . $trener['nazwisko']) ?> (<?= htmlspecialchars($trener['rola']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Brak pozostałych trenerów w zespole.</p>
        <?php endif; ?>
    </section>

    <section class="team-league-history">
        <h3>Historia w Tabeli Ligowej</h3>
        <?php if (!empty($historia_tabeli_ligowej)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sezon</th>
                        <th>Zwycięstwa</th>
                        <th>Porażki</th>
                        <th>Miejsce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historia_tabeli_ligowej as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['rok_rozpoczecia'] . '/' . $entry['rok_zakonczenia']) ?></td>
                            <td><?= htmlspecialchars($entry['liczba_zwyciestw']) ?></td>
                            <td><?= htmlspecialchars($entry['liczba_porazek']) ?></td>
                            <td><?= htmlspecialchars($entry['miejsce_w_tabeli']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak danych o historii w tabeli ligowej.</p>
        <?php endif; ?>
    </section>

    <p><a href="index.php" class="button">Powrót do listy zespołów</a></p>

</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
