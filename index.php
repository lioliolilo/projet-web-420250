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
            <a href="index.html" class="logo">
                <img src="images/CollegeAhuntsic_Logo.png" alt="Logo du Collège Ahuntsic">
                <span class="site-title">Tuto+</span>
            </a>

            <nav class="main-nav">
                <ul>
                    <li><a href="#hero">Accueil</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#fonctionnement">Comment ça fonctionne ?</a></li>
                    <li><a href="#tuteurs">Nos tuteurs</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="espace-etudiant.html">Espace étudiant</a></li>
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
                    <p>
                        Voici les service offert par Tuto+.
                    </p>
                </header>

                <!-- liste des services -->
                <div class="services-liste">
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
                    <li>Choisissez le service dont vous avez besoin.</li>
                    <li>Sélectionnez un créneau disponible avec un tuteur.</li>
                    <li>Recevez la confirmation de votre rendez-vous.</li>
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
                </div>
            </div>
        </section>

        <!-- section contact -->
        <section id="contact" class="section-alt">
            <div class="container">
                <header class="section-header">
                    <h2>Besoin d'informations sur Tuto+ ?</h2>
                </header>

                <p>
                    Pour toute question sur le service Tuto+, veuillez communiquer avec le
                    département responsable du tutorat au Collège Ahuntsic.
                </p>
                <!-- futur formulaire de contact -->
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