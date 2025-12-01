<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tutoplus_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

// Récupération JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "No data received"]);
    exit;
}

$id_tuteur = $data["id_tuteur"] ?? null;
$nom       = trim($data["nom"] ?? "");
$email     = trim($data["email"] ?? "");
$sujet     = trim($data["sujet"] ?? "");
$message   = trim($data["message"] ?? "");

if (!$id_tuteur || !$nom || !$email || !$sujet || !$message) {
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO messages (id_tuteur, nom, email, sujet, message)
    VALUES (?, ?, ?, ?, ?)
");

$ok = $stmt->execute([$id_tuteur, $nom, $email, $sujet, $message]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Message enregistré." : "Erreur lors de l'enregistrement."
]);
