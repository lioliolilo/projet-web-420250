<?php
$host = 'localhost';
$dbname = 'tutoplus_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("faile connection database" . $e->getMessage());
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("info introuvable");
}

$rdv_id = intval($_GET['id']);

$sql = "SELECT 
            r.date_rdv, 
            r.heure, 
            r.duree,
            r.statut,
            r.salle,
            r.commentaire,
            e.prenom AS etudiant_prenom, 
            e.nom AS etudiant_nom,
            t.prenom AS tuteur_prenom, 
            t.nom AS tuteur_nom,
            s.nom_service
        FROM rendez_vous r
        INNER JOIN etudiants e ON r.id_etudiant = e.id_etudiant
        INNER JOIN tuteurs t ON r.id_tuteur = t.id_tuteur
        INNER JOIN services s ON r.id_service = s.id_service
        WHERE r.id_rdv = :id_rdv";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_rdv' => $rdv_id]);
$rdv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rdv) {
    die("aucun id: {$rdv_id} rendez vous");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Rendez-vous</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-width: 600px; margin: auto; }
        h1 { color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        p { line-height: 1.6; }
        strong { display: inline-block; width: 150px; }
        .status-attente { color: #ff9800; font-weight: bold; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Demande de rendez-vous envoyée</h1>

    <p>
        On a reçu votre demande, veuillez attendre avec patient.
    </p>

    <h2>Détaille de demande :</h2>

    <p><strong>ID Demande: </strong><?= htmlspecialchars($rdv_id) ?></p>
    <p><strong>Tuteur: </strong><?= htmlspecialchars($rdv['tuteur_prenom'] . ' ' . $rdv['tuteur_nom']) ?></p>
    <p><strong>Service: </strong><?= htmlspecialchars($rdv['nom_service']) ?></p>
    <p><strong>Date: </strong><?= htmlspecialchars($rdv['date_rdv']) ?></p>
    <p><strong>Temps: </strong><?= htmlspecialchars($rdv['heure']) ?></p>
    <p><strong>Durée: </strong><?= htmlspecialchars($rdv['duree']) ?> Heurs</p>
    <p><strong>Lieu: </strong><?= htmlspecialchars($rdv['salle']) ?></p>

    <?php if (!empty($rdv['commentaire'])): ?>
        <p><strong>Commentaire：</strong><?= nl2br(htmlspecialchars($rdv['commentaire'])) ?></p>
    <?php endif; ?>

    <hr>

    <p><strong>Statut actuel：</strong>
        <span class="status-attente"><?= htmlspecialchars($rdv['statut']) ?></span>
    </p>

    <a href="demande.php" class="btn-back"><- retourner</a>
</div>

</body>
</html>
