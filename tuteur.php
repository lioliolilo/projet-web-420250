<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=tutoplus_db;charset=utf8mb4",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Connexion simulée 
$tuteurConnecteId = 1;  // Robert Klein

// Charger les demandes du tuteur connecté
$sql = "
    SELECT r.*, 
           e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
           t.nom AS nom_tuteur, t.prenom AS prenom_tuteur
    FROM rendez_vous r
    JOIN etudiants e ON r.id_etudiant = e.id_etudiant
    JOIN tuteurs t ON r.id_tuteur = t.id_tuteur
    WHERE r.statut = 'en_attente'
    AND r.id_tuteur = ?
    ORDER BY r.date_rdv, r.heure
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$tuteurConnecteId]);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Espace Tuteur – Gestion des demandes</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="site-header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <img src="images/CollegeAhuntsic_Logo.png" alt="Logo du Collège Ahuntsic">
                <span class="site-title">Tuto+</span>
            </a>

            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="index.php#fonctionnement">Comment ça fonctionne ?</a></li>
                    <li><a href="index.php#tuteurs">Nos tuteurs</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                    <li><a href="etudiant.php">Espace étudiant</a></li>
                    <li><a href="tuteur.php" class="active">Espace tuteur</a></li>
                    <li><a href="admin.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="section">
        <div class="container">
            <header class="section-header">
                <h2>Demandes en attente</h2>
                <p>Répondez aux demandes de rendez-vous des étudiants.</p>
            </header>

            <?php if (count($demandes) > 0): ?>
                <?php foreach ($demandes as $d): ?>
                    <div class="demande-box">
                        <p><strong>Étudiant :</strong> <?= $d['prenom_etudiant'] . " " . $d['nom_etudiant'] ?></p>
                        <p><strong>Date :</strong> <?= $d['date_rdv'] ?></p>
                        <p><strong>Heure :</strong> <?= substr($d['heure'], 0, 5) ?></p>
                        <p><strong>Salle :</strong> <?= $d['salle'] ?></p>

                        <div class="demande-actions">
                            <a class="btn accept" href="update_demande.php?id=<?= $d['id_rdv'] ?>&action=accept">Accepter</a>
                            <a class="btn refuse" href="update_demande.php?id=<?= $d['id_rdv'] ?>&action=refuse">Refuser</a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p>Aucune demande en attente.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?= date("Y") ?> Collège Ahuntsic – Service Tuto+</p>
        </div>
    </footer>

</body>

</html>