<?php


$host = "localhost";
$user = "root";
$password = "";
$db = "tutoplus_db";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}


$sql = "
    SELECT r.*, 
           e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
           t.nom AS nom_tuteur, t.prenom AS prenom_tuteur
    FROM rendez_vous r
    JOIN etudiants e ON r.id_etudiant = e.id_etudiant
    JOIN tuteurs t ON r.id_tuteur = t.id_tuteur
    WHERE r.statut = 'en_attente'
    ORDER BY r.date_rdv, r.heure
";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des demandes</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        .container {
            width: 70%;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .demande {
            border-bottom: 1px solid #ddd;
            padding: 15px 10px;
        }

        .demande:last-child {
            border-bottom: none;
        }

        .btn {
            padding: 6px 14px;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .accept { background: #28a745; }
        .refuse { background: #dc3545; }
    </style>
</head>

<body>
<div class="container">

    <h2 style="color:#d10000;">Demandes en attente</h2>

    <?php
    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            echo "<div class='demande'>";
            echo "<b>Étudiant :</b> {$row['prenom_etudiant']} {$row['nom_etudiant']}<br>";
            echo "<b>Tuteur :</b> {$row['prenom_tuteur']} {$row['nom_tuteur']}<br>";
            echo "<b>Date :</b> {$row['date_rdv']}<br>";
            echo "<b>Heure :</b> {$row['heure']}<br>";
            echo "<b>Salle :</b> {$row['salle']}<br><br>";

            echo "<a class='btn accept' href='update_demande.php?id={$row['id_rdv']}&action=accept'>Accepter</a>";
            echo "<a class='btn refuse' href='update_demande.php?id={$row['id_rdv']}&action=refuse'>Refuser</a>";

            echo "</div>";
        }

    } else {
        echo "<p>Aucune demande en attente.</p>";
    }
    ?>

</div>
</body>
</html>
