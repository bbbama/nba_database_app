<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id_tabeli = $_GET['id'];
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM tabela_ligowa WHERE id_tabeli = :id_tabeli");
        $stmt->bindParam(':id_tabeli', $id_tabeli, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd przy usuwaniu wpisu z tabeli ligowej: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
?>