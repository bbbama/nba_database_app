<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$id_zawodnika = $_GET['id'] ?? null;

if (!$id_zawodnika) {
    header('Location: index.php'); // Przekieruj na listę zawodników, jeśli brak ID
    exit;
}

try {
    $pdo = getDbConnection();

    // Pobierz podstawowe dane zawodnika i zespołu
    $stmt_zawodnik = $pdo->prepare('SELECT z.imie, z.nazwisko, z.pozycja, z.data_urodzenia, zes.nazwa AS nazwa_zespolu
                                FROM zawodnik z
                                LEFT JOIN zespol zes ON z.id_zespolu = zes.id_zespolu
                                WHERE z.id_zawodnika = ?');
    $stmt_zawodnik->execute([$id_zawodnika]);
    $zawodnik = $stmt_zawodnik->fetch(PDO::FETCH_ASSOC);

    if (!$zawodnik) {
        header('Location: index.php'); // Przekieruj, jeśli zawodnik nie istnieje
        exit;
    }

    // Pobierz kontrakty zawodnika
    $stmt_kontrakty = $pdo->prepare('SELECT k.data_poczatek, k.data_koniec, k.wynagrodzenie_roczne, zes.nazwa AS nazwa_zespolu_kontrakt
                                    FROM kontrakt k
                                    JOIN zespol zes ON k.id_zespolu = zes.id_zespolu
                                    WHERE k.id_zawodnika = ? ORDER BY k.data_poczatek DESC');
    $stmt_kontrakty->execute([$id_zawodnika]);
    $kontrakty = $stmt_kontrakty->fetchAll(PDO::FETCH_ASSOC);

    // Pobierz kontuzje zawodnika
    $stmt_kontuzje = $pdo->prepare('SELECT typ_kontuzji, data_rozpoczecia, data_zakonczenia, status
                                   FROM kontuzja WHERE id_zawodnika = ? ORDER BY data_rozpoczecia DESC');
    $stmt_kontuzje->execute([$id_zawodnika]);
    $kontuzje = $stmt_kontuzje->fetchAll(PDO::FETCH_ASSOC);

    // Pobierz nagrody zawodnika
    $stmt_nagrody = $pdo->prepare('SELECT nazwa_nagrody, rok FROM nagroda WHERE id_zawodnika = ? ORDER BY rok DESC');
    $stmt_nagrody->execute([$id_zawodnika]);
    $nagrody = $stmt_nagrody->fetchAll(PDO::FETCH_ASSOC);

    // Pobierz statystyki zawodnika (podsumowanie lub średnie) - tutaj uproszczone
    // Możesz dodać bardziej szczegółowe statystyki z widoków lub tabeli statystyki
    $stmt_statystyki = $pdo->prepare('SELECT id_meczu, minuty, punkty, asysty, zbiorki FROM statystyki_meczu WHERE id_zawodnika = ? ORDER BY id_meczu DESC LIMIT 5'); // Ostatnie 5 meczów
    $stmt_statystyki->execute([$id_zawodnika]);
    $statystyki = $stmt_statystyki->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych zawodnika: " . $e->getMessage());
}

$pageTitle = 'Szczegóły Zawodnika: ' . htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']);
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2><?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?></h2>

    <section class="player-details">
        <h3>Informacje podstawowe</h3>
        <p><strong>Zespół:</strong> <?= htmlspecialchars($zawodnik['nazwa_zespolu'] ?? 'Brak') ?></p>
        <p><strong>Pozycja:</strong> <?= htmlspecialchars($zawodnik['pozycja']) ?></p>
        <p><strong>Data urodzenia:</strong> <?= htmlspecialchars($zawodnik['data_urodzenia']) ?></p>
    </section>

    <section class="player-contracts">
        <h3>Historia Kontraktów</h3>
        <?php if (!empty($kontrakty)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zespół</th>
                        <th>Początek</th>
                        <th>Koniec</th>
                        <th>Wynagrodzenie roczne</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kontrakty as $kontrakt): ?>
                        <tr>
                            <td><?= htmlspecialchars($kontrakt['nazwa_zespolu_kontrakt']) ?></td>
                            <td><?= htmlspecialchars($kontrakt['data_poczatek']) ?></td>
                            <td><?= htmlspecialchars($kontrakt['data_koniec']) ?></td>
                            <td><?= htmlspecialchars(number_format($kontrakt['wynagrodzenie_roczne'], 2, ',', ' ')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak danych o kontraktach.</p>
        <?php endif; ?>
    </section>

    <section class="player-injuries">
        <h3>Historia Kontuzji</h3>
        <?php if (!empty($kontuzje)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Typ</th>
                        <th>Początek</th>
                        <th>Koniec</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kontuzje as $kontuzja): ?>
                        <tr>
                            <td><?= htmlspecialchars($kontuzja['typ_kontuzji']) ?></td>
                            <td><?= htmlspecialchars($kontuzja['data_rozpoczecia']) ?></td>
                            <td><?= htmlspecialchars($kontuzja['data_zakonczenia']) ?></td>
                            <td><?= htmlspecialchars($kontuzja['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak danych o kontuzjach.</p>
        <?php endif; ?>
    </section>

    <section class="player-awards">
        <h3>Nagrody</h3>
        <?php if (!empty($nagrody)): ?>
            <ul>
                <?php foreach ($nagrody as $nagroda): ?>
                    <li><?= htmlspecialchars($nagroda['nazwa_nagrody']) ?> (<?= htmlspecialchars($nagroda['rok']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Brak nagród.</p>
        <?php endif; ?>
    </section>

    <section class="player-stats">
        <h3>Ostatnie Statystyki (wybrane mecze)</h3>
        <?php if (!empty($statystyki)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mecz ID</th>
                        <th>Minuty</th>
                        <th>Punkty</th>
                        <th>Zbiórki</th>
                        <th>Asysty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statystyki as $stat): ?>
                        <tr>
                            <td><?= htmlspecialchars($stat['id_meczu'] ?? '') ?></td>
                            <td><?= htmlspecialchars($stat['minuty'] ?? '') ?></td>
                            <td><?= htmlspecialchars($stat['punkty'] ?? '') ?></td>
                            <td><?= htmlspecialchars($stat['zbiorki'] ?? '') ?></td>
                            <td><?= htmlspecialchars($stat['asysty'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak statystyk dla ostatnich meczów.</p>
        <?php endif; ?>
    </section>

    <p><a href="index.php" class="button">Powrót do listy zawodników</a></p>

</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
