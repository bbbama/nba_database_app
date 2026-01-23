<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <ul>
        <li><a href="<?= $basePath ?? '' ?>index.php">Strona główna</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?= $basePath ?? '' ?>zawodnicy/">Zawodnicy</a></li>
            <li><a href="<?= $basePath ?? '' ?>zespoly/">Zespoły</a></li>
            <li><a href="<?= $basePath ?? '' ?>mecze/">Mecze</a></li>
            <li><a href="<?= $basePath ?? '' ?>raporty/">Raporty</a></li>
            <li><a href="<?= $basePath ?? '' ?>areny/">Areny</a></li>
            <li><a href="<?= $basePath ?? '' ?>sezony/">Sezony</a></li>
            <li><a href="<?= $basePath ?? '' ?>trener/">Trenerzy</a></li>
            <li><a href="<?= $basePath ?? '' ?>kontrakt/">Kontrakty</a></li>
            <li><a href="<?= $basePath ?? '' ?>kontuzja/">Kontuzje</a></li>
            <li><a href="<?= $basePath ?? '' ?>nagroda/">Nagrody</a></li>
            <li><a href="<?= $basePath ?? '' ?>tabela_ligowa/">Tabela Ligowa</a></li>
        <?php endif; ?>
    </ul>
    <div class="auth-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Witaj, <?= htmlspecialchars($_SESSION['user_login']) ?>!</span>
            <a href="<?= $basePath ?? '' ?>logout.php">Wyloguj</a>
        <?php else: ?>
            <a href="<?= $basePath ?? '' ?>login.php">Zaloguj</a>
        <?php endif; ?>
    </div>
</nav>
