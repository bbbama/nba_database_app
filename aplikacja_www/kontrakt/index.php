<?php
require_once '../db.php';

$kontrakty = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT k.id_kontraktu, z.imie, z.nazwisko, zes.nazwa AS nazwa_zespolu, k.data_poczatek, k.data_koniec, k.wynagrodzenie_roczne FROM kontrakt k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika JOIN zespol zes ON k.id_zespolu = zes.id_zespolu ORDER BY k.data_koniec DESC");
    $kontrakty = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu kontraktów: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Kontraktami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Kontraktami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li>
            <li><a href="../raporty/">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
            <li><a href="../trener/">Trenerzy</a></li>
            <li><a href="index.php">Kontrakty</a></li>
            <li><a href="../kontuzja/">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Kontraktów</h2>
        <p><a href="form.php">Dodaj nowy kontrakt</a></p>
        <?php if (!empty($kontrakty)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th>Zespół</th>
                        <th>Data początek</th>
                        <th>Data koniec</th>
                        <th>Wynagrodzenie roczne</th>
                        <th>Akcje</th>
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
                        <td>
                            <a href="form.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>">Edytuj</a>
                            <a href="delete.php?id=<?= htmlspecialchars($kontrakt['id_kontraktu']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten kontrakt?');">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak kontraktów w bazie danych.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
