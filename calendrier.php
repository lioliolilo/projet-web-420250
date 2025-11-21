<?php
// ==================================================================
// init
// ==================================================================
session_start();

$host = 'localhost';
$dbname = 'tutoplus_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$currentUserId = $_SESSION['user_id'];

// ==================================================================
// DATE & TEMPS
// ==================================================================
date_default_timezone_set('America/Montreal');

$dateParam = $_GET['date'] ?? date('Y-m-d');
try {
    $currentDate = new DateTime($dateParam);
} catch (Exception $e) {
    $currentDate = new DateTime();
}

$monday = clone $currentDate;
$monday->modify('monday this week');
$sunday = clone $monday;
$sunday->modify('+6 days');

$prevWeek = clone $monday;
$prevWeek->modify('-1 week');
$nextWeek = clone $monday;
$nextWeek->modify('+1 week');

$weekDates = [];
$tempDate = clone $monday;
for ($i = 0; $i < 5; $i++) {
    $weekDates[] = [
            'obj' => clone $tempDate,
            'formatted' => strftime_fr($tempDate, '%a. %d %b.'),
            'iso' => $tempDate->format('Y-m-d')
    ];
    $tempDate->modify('+1 day');
}

// ==================================================================
// ANALYSE DE DATA
// ==================================================================

$events = [];

$roleCondition = "";
if ($_SESSION['role'] == 'etudiant') {
    $roleCondition = "r.id_etudiant = :myId";
} else {
    $roleCondition = "r.id_tuteur = :myId";
}

$sql = "
    SELECT 
        r.id_rdv, r.date_rdv, r.heure, r.duree, r.statut, r.salle,
        e.nom AS etudiant_nom, e.prenom AS etudiant_prenom,
        t.nom AS tuteur_nom, t.prenom AS tuteur_prenom
    FROM rendez_vous r
    LEFT JOIN etudiants e ON r.id_etudiant = e.id_etudiant
    LEFT JOIN tuteurs t ON r.id_tuteur = t.id_tuteur
    WHERE ($roleCondition)
    AND r.date_rdv BETWEEN :startDate AND :endDate
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
            ':myId'      => $currentUserId,
            ':startDate' => $monday->format('Y-m-d'),
            ':endDate'   => $sunday->format('Y-m-d')
    ]);
    $rawRendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawRendezvous as $rv) {
        $rvDate = $rv['date_rdv'];

        $timeParts = explode(':', $rv['heure']);
        $hour = intval($timeParts[0]);
        $minute = intval($timeParts[1]);
        $startTimeDecimal = $hour + ($minute / 60);

        if ($_SESSION['role'] == 'etudiant') {
            $title = $rv['tuteur_prenom'] . " " . $rv['tuteur_nom'];
            $sub = "Tutorat";
        } else {
            $title = $rv['etudiant_prenom'] . " " . $rv['etudiant_nom'];
            $sub = "Cours";
        }

        if (!isset($events[$rvDate])) {
            $events[$rvDate] = [];
        }
        $events[$rvDate][] = [
                'id'       => $rv['id_rdv'],
                'title'    => $title,
                'subtitle' => $sub,
                'room'     => $rv['salle'] ?? 'Biblio',
                'start'    => $startTimeDecimal,
                'duration' => floatval($rv['duree'])
        ];
    }

} catch (Exception $e) {
    die("SQL Error: " . $e->getMessage());
}

// ==================================================================
// FONCTION ASSISTANT
// ==================================================================
function strftime_fr($dateObj, $format) {
    $days = ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'];
    $months = ['', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];

    $w = $dateObj->format('w');
    $n = $dateObj->format('n');
    $d = $dateObj->format('d');
    $Y = $dateObj->format('Y');

    $str = $format;
    $str = str_replace('%a', ucfirst($days[$w]), $str);
    $str = str_replace('%b', $months[$n], $str);
    $str = str_replace('%d', $d, $str);
    $str = str_replace('%Y', $Y, $str);
    return $str;
}

// le mini calendrier
$miniCalMonth = clone $currentDate;
$firstDayOfMonth = clone $miniCalMonth;
$firstDayOfMonth->modify('first day of this month');
$daysInMonth = $miniCalMonth->format('t');
$startDayOfWeek = $firstDayOfMonth->format('w');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier Tuto+</title>

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
                <li><a href="index.php#hero">Accueil</a></li>
                <li><a href="index.php#services">Services</a></li>
                <li><a href="index.php#fonctionnement">Comment ça fonctionne ?</a></li>
                <li><a href="index.php#tuteurs">Nos tuteurs</a></li>
                <li><a href="index.php#contact">Contact</a></li>
                <li><a href="espace-etudiant.html">Espace étudiant</a></li>
                <li><a href="calendrier.php">Calendrier</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="calendar-content-wrapper">

    <header class="cal-nav-bar">
        <a href="?date=<?php echo $prevWeek->format('Y-m-d'); ?>" class="cal-nav-btn">&lt;&lt; Précédent</a>

        <div class="current-week-display" onclick="togglePopup()">
            <span>📅 semaine du <?php echo strftime_fr($monday, '%d %b %Y'); ?></span>

            <div class="mini-calendar-popup" id="miniCalendar" onclick="event.stopPropagation()">
                <div class="popup-header">
                    <span><?php echo strftime_fr($miniCalMonth, '%b %Y'); ?></span>
                    <button class="popup-close" onclick="togglePopup()">×</button>
                </div>
                <div class="popup-grid">
                    <div class="popup-head-cell">D</div><div class="popup-head-cell">L</div><div class="popup-head-cell">M</div>
                    <div class="popup-head-cell">M</div><div class="popup-head-cell">J</div><div class="popup-head-cell">V</div>
                    <div class="popup-head-cell">S</div>
                    <?php
                    for ($k = 0; $k < $startDayOfWeek; $k++) echo '<div></div>';
                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $dStr = $miniCalMonth->format('Y-m-') . sprintf('%02d', $d);
                        $isCurrent = ($dStr == $currentDate->format('Y-m-d')) ? 'current' : '';
                        echo "<a href='?date={$dStr}' class='popup-cell {$isCurrent}'>{$d}</a>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <a href="?date=<?php echo $nextWeek->format('Y-m-d'); ?>" class="nav-btn">Suivant &gt;&gt;</a>
    </header>

    <div class="schedule-wrapper">
        <div class="header-cell time-header"></div>
        <?php foreach ($weekDates as $wd): ?>
            <div class="header-cell"><?php echo $wd['formatted']; ?></div>
        <?php endforeach; ?>

        <div class="time-axis">
            <?php for ($h = 8; $h <= 18; $h++): ?>
                <div class="time-label"><?php echo sprintf("%02d:00", $h); ?></div>
            <?php endfor; ?>
        </div>

        <?php foreach ($weekDates as $wd): ?>
            <div class="day-column">
                <?php for ($h = 8; $h <= 18; $h++): ?>
                    <div class="bg-slot"></div>
                <?php endfor; ?>

                <?php
                if (isset($events[$wd['iso']])):
                    foreach ($events[$wd['iso']] as $evt):
                        $top = ($evt['start'] - 8) * 50;
                        $height = $evt['duration'] * 50;
                        ?>
                        <div class="event-card" style="top: <?php echo $top; ?>px; height: <?php echo $height; ?>px;">
                            <span class="event-title"><?php echo htmlspecialchars($evt['title']); ?></span>
                            <span class="event-sub"><?php echo htmlspecialchars($evt['subtitle']); ?></span>
                            <div style="font-size:10px; margin-top:2px; color:#666;">
                                <?php echo htmlspecialchars($evt['room']); ?>
                            </div>
                        </div>
                    <?php
                    endforeach;
                endif;
                ?>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Collège Ahuntsic – Service Tuto+</p>
    </div>
</footer>

<script>
    function togglePopup() {
        const popup = document.getElementById('miniCalendar');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }
</script>

</body>
</html>