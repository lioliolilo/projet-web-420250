<?php
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("Requête invalide.");
}

$id = intval($_GET['id']);
$action = $_GET['action'];

if (!in_array($action, ['accept', 'refuse'])) {
    die("Action invalide.");
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tutoplus_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur DB : " . $e->getMessage());
}

$newStatus = ($action === "accept") ? "confirmé" : "annulé";

$stmt = $pdo->prepare("UPDATE rendez_vous SET statut = ? WHERE id_rdv = ?");
$stmt->execute([$newStatus, $id]);

header("Location: tuteur.php?done=" . $action);
exit;
