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

// Charger les tuteurs
try {
    $stmt = $conn->query("SELECT nom, prenom, specialite FROM tuteurs ORDER BY nom ASC");
    $tuteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors du chargement des tuteurs: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuto+</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
<!-- header -->
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

<!-- section introduction -->
<main>
    <section id="hero" class="hero">
        <div class="hero-container">
            <div class="hero-text">
                <h1>Service de tutorat Tuto+</h1>
                <p>
                    Bonjour au service Tuto+ offert par le Collège Ahuntsic.
                </p>
                <a href="#services" class="btn">Découvrir les services</a>
            </div>
        </div>
    </section>

    <!-- section services -->
    <section id="services" class="section">
        <div class="container">
            <header class="section-header">
                <h2>Services de tutorat offerts</h2>
            </header>

            <!-- liste des services -->
            <div class="services-liste">

                <article class="service-card">
                    <h3>Programmation orientée objet</h3>
                    <p>Accompagnement pour mieux comprendre la logique de programmation Java</p>
                </article>

                <article class="service-card">
                    <h3>Mathématique</h3>
                    <p>Soutien au cours de mathématiques en cadre du programme d'informatique</p>
                </article>

                <article class="service-card">
                    <h3>Développement Web</h3>
                    <p>Assistance en HTML, CSS, JavaScript et les bases d'un site web moderne</p>
                </article>

                <article class="service-card">
                    <h3>Bases de données (SQL)</h3>
                    <p>Aide pour comprendre les requêtes SQL, les bases de données, les jointures et la gestion des
                        données</p>
                </article>

                <article class="service-card">
                    <h3>Réseautique</h3>
                    <p>Support pour la configuration réseau et Linux
                    </p>
                </article>

                <article class="service-card">
                    <h3>Accompagnement sur les TP / Devoirs</h3>
                    <p>Encadrement pour les travaux pratiques, les projets d’équipe et la préparation aux examens
                    </p>
                </article>

            </div>
        </div>
    </section>

    <!-- section info -->
    <section id="fonctionnement" class="section-alt">
        <div class="container">
            <header class="section-header">
                <h2>Comment fonctionne Tuto+ ?</h2>
            </header>

            <ol class="etapes-fonctionnement">
                <li>Choisissez le service dont vous avez besoin</li>
                <li>Sélectionnez un créneau disponible avec un tuteur</li>
                <li>Recevez la confirmation de votre rendez-vous</li>
            </ol>
        </div>
    </section>

    <!-- section tuteur -->
    <section id="tuteurs" class="section">
        <div class="container">
            <header class="section-header">
                <h2>Des tuteurs qualifiés pour vous aider</h2>
            </header>

            <div class="tuteurs-liste">
                <?php foreach ($tuteurs as $t): ?>
                    <div class="tuteur-card">
                        <h3><?= htmlspecialchars($t['prenom'] . " " . $t['nom']) ?></h3>
                        <p><?= htmlspecialchars($t['specialite']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- section contact -->
    <section id="contact" class="section-alt">
        <div class="container">
            <header class="section-header">
                <h2>Contactez un tuteur</h2>
                <p>Remplissez ce formulaire et un tuteur vous répondra rapidement.</p>
            </header>

            <form id="contactForm" class="contact-form">
                <div class="form-group">
                    <label>Tuteur à contacter</label>
                    <select id="tuteur" required>
                        <option value="">Choisir un tuteur...</option>

                        <?php foreach ($tuteurs as $t): ?>
                            <option value="<?= htmlspecialchars($t['prenom'] . ' ' . $t['nom']) ?>">
                                <?= htmlspecialchars($t['prenom'] . ' ' . $t['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Votre nom</label>
                    <input type="text" id="nom" required>
                </div>

                <div class="form-group">
                    <label>Votre email</label>
                    <input type="email" id="email" required>
                </div>

                <div class="form-group">
                    <label>Sujet</label>
                    <input type="text" id="sujet" required>
                </div>

                <div class="form-group">
                    <label>Message (max 500 caractères)</label>
                    <textarea id="message" maxlength="500" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Envoyer le message</button>

                <p id="formResult" class="form-result"></p>
            </form>
        </div>
    </section>
</main>

<!-- footer -->
<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Collège Ahuntsic – Service Tuto+</p>
    </div>
</footer>
</body>

</html>