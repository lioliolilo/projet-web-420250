<?php
try {
    $conn = new PDO(
        "mysql:host=localhost;dbname=tutoplus_db;charset=utf8mb4",
        "root",
        ""
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
    exit;
}

// Charger les services + tous les tuteurs associés
try {
    $stmt = $conn->query("
        SELECT 
            s.id_service,
            s.nom_service,
            GROUP_CONCAT(CONCAT(t.prenom, ' ', t.nom) SEPARATOR ' | ') AS tuteurs,
            GROUP_CONCAT(t.specialite SEPARATOR ' | ') AS specialites
        FROM services s
        LEFT JOIN service_tuteurs st ON st.id_service = s.id_service
        LEFT JOIN tuteurs t ON t.id_tuteur = st.id_tuteur
        GROUP BY s.id_service, s.nom_service
        ORDER BY s.nom_service ASC
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors du chargement des services: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant – Tuto+</title>
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
                <li><a href="#hero">Accueil</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#fonctionnement">Comment ça fonctionne ?</a></li>
                <li><a href="#tuteurs">Nos tuteurs</a></li>
                <li><a href="index.php#contact">Contact</a></li>
                <li><a href="demande.php">Demande</a></li>
                <li><a href="etudiant.php">Espace étudiant</a></li>
                <li><a href="tuteur.php">Espace tuteur</a></li>
                <li><a href="admin.php">Admin</a></li>
                <li><a href="calendrier.php">Calendrier</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
<section class="section">
    <div class="container">
        <header class="section-header">
            <h2>Faire une demande de rendez-vous</h2>
            <p>Sélectionnez un service pour voir les tuteurs disponibles.</p>
        </header>

        <div class="container-box">

            <label for="service"><strong>Service :</strong></label>
            <select id="service" class="service-select">
                <option value="">-- Choisir un service --</option>

                <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id_service'] ?>"
                        data-tuteurs="<?= htmlspecialchars($s['tuteurs']) ?>"
                        data-specialites="<?= htmlspecialchars($s['specialites']) ?>">
                        <?= htmlspecialchars($s['nom_service']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="tuteur-info" class="tuteur-info" style="display:none;">
                <p><strong>Tuteurs :</strong> <span id="nomTuteur">—</span></p>
                <p><strong>Spécialités :</strong> <span id="specTuteur">—</span></p>
            </div>

        </div>
    </div>
</section>
</main>

<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?= date("Y") ?> Collège Ahuntsic – Service Tuto+</p>
    </div>
</footer>

<script src="etudiant.js"></script>
</body>
</html>
