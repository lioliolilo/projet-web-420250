<?php
header("Content-Type: application/json");

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

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['date']) || !isset($data['heure']) || !isset($data['duree'])) {
    echo json_encode(["success"=>false, "message"=>"param missing"]);
    exit;
}

$id = $data["id"];
$date = $data["date"];
$heure = $data["heure"];
$duree = $data["duree"];

$sql = "UPDATE rendez_vous 
        SET date_rdv = :d, heure = :h, duree = :du 
        WHERE id_rdv = :id";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([
    ":d"=>$date,
    ":h"=>$heure,
    ":du"=>$duree,
    ":id"=>$id
])) {
    echo json_encode(["success"=>true, "message"=>"Rendez-vous mis à jour !"]);
} else {
    echo json_encode(["success"=>false, "message"=>"Erreur durant la mise à jour"]);
}
