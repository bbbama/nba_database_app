<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id_kontraktu = $_GET['id'];
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM kontrakt WHERE id_kontraktu = :id_kontraktu");
        $stmt->bindParam(':id_kontraktu', $id_kontraktu, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd przy usuwaniu kontraktu: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
?>