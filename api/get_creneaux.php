<?php
$host = 'localhost';
$dbname = 'tutoplus_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
    echo json_encode(["success"=>false, "message"=>"Connexion impossible"]);
    exit;
}

$serviceId = $_GET['service'] ?? null;
if (!$serviceId) {
    echo json_encode([]);
    exit;
}

// Obtenir les tuteurs du service
$stmt = $pdo->prepare("
    SELECT t.id_tuteur, t.prenom, t.nom
    FROM service_tuteurs st
    JOIN tuteurs t ON t.id_tuteur = st.id_tuteur
    WHERE st.id_service = ?
");
$stmt->execute([$serviceId]);
$tuteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$creneauxParTuteur = [];

foreach ($tuteurs as $t) {
    $tuteurId = $t["id_tuteur"];

    $baseHours = range(8, 18);

    // rendez-vous pris
    $stmt2 = $pdo->prepare("
        SELECT heure
        FROM rendez_vous
        WHERE id_tuteur = ?
    ");
    $stmt2->execute([$tuteurId]);
    $rdv = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // convertir RDV en heures entières
    $occupied = array_map(fn($h) => intval(substr($h, 0, 2)), $rdv);

    // créneaux libres
    $free = array_diff($baseHours, $occupied);

    $creneauxParTuteur[] = [
        "tuteur" => $t["prenom"] . " " . $t["nom"],
        "id_tuteur" => $tuteurId,
        "creneaux" => array_values($free)
    ];
}

header("Content-Type: application/json");
echo json_encode($creneauxParTuteur);
