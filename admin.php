<?php
$host = 'localhost';
$dbname = 'tutoplus_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("faile connection database: " . $e->getMessage());
}

$message = '';
if (isset($_GET['done'])) {
    if ($_GET['done'] === 'accept') {
        $message = '<div style="color: green;">Accepté</div>';
    } elseif ($_GET['done'] === 'refuse') {
        $message = '<div style="color: red;">Refusé</div>';
    }
}

$sql = "SELECT 
            r.id_rdv, 
            r.date_rdv, 
            r.heure, 
            r.duree,
            e.nom AS etudiant_nom, 
            e.prenom AS etudiant_prenom,
            t.nom AS tuteur_nom, 
            t.prenom AS tuteur_prenom,
            s.nom_service
        FROM rendez_vous r
        INNER JOIN etudiants e ON r.id_etudiant = e.id_etudiant
        INNER JOIN tuteurs t ON r.id_tuteur = t.id_tuteur
        INNER JOIN services s ON r.id_service = s.id_service
        WHERE r.statut = 'en_attente' 
        ORDER BY r.date_rdv ASC, r.heure ASC";

$stmt = $pdo->query($sql);
$pending_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface Administrateur - Gestion des Rendez-vous</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { width: 80%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-accept { background-color: #4CAF50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-refuse { background-color: #f44336; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>

<h1>Demandes en attente</h1>

<?= $message ?>

<?php if (empty($pending_appointments)): ?>
    <p>Pas de demande</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID Demande</th>
            <th>Étudiant</th>
            <th>Tuteur</th>
            <th>Service</th>
            <th>Date/Heure</th>
            <th>Durée</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pending_appointments as $rdv): ?>
            <tr>
                <td><?= htmlspecialchars($rdv['id_rdv']) ?></td>
                <td><?= htmlspecialchars($rdv['etudiant_prenom'] . ' ' . $rdv['etudiant_nom']) ?></td>
                <td><?= htmlspecialchars($rdv['tuteur_prenom'] . ' ' . $rdv['tuteur_nom']) ?></td>
                <td><?= htmlspecialchars($rdv['nom_service']) ?></td>
                <td><?= htmlspecialchars($rdv['date_rdv']) ?> / <?= htmlspecialchars($rdv['heure']) ?></td>
                <td><?= htmlspecialchars($rdv['duree']) ?> h</td>
                <td>
                    <a href="update_demande.php?id=<?= $rdv['id_rdv'] ?>&action=accept" class="btn-accept">Accepter</a>
                    <a href="update_demande.php?id=<?= $rdv['id_rdv'] ?>&action=refuse" class="btn-refuse">Refuser</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
