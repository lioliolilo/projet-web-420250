<?php
date_default_timezone_set('America/Montreal');

$dateParam = $_GET['date'] ?? date('Y-m-d');
try {
    $currentDate = new DateTime($dateParam);
} catch (Exception $e) {
    $currentDate = new DateTime();
}

// semaine
$monday = clone $currentDate;
$monday->modify('monday this week');

$prevWeek = (clone $monday)->modify('-1 week');
$nextWeek = (clone $monday)->modify('+1 week');

// header dates
$weekDates = [];
$temp = clone $monday;

for ($i = 0; $i < 5; $i++) {
    $weekDates[] = [
        "obj" => clone $temp,
        "formatted" => strftime_fr($temp, '%a. %d %b.'),
        "iso" => $temp->format('Y-m-d')
    ];
    $temp->modify('+1 day');
}

$rendezvous = [
    [
        "id" => 101,
        "date" => "2025-11-17",
        "heure" => "09:30:00",
        "statut" => "confirmé",
        "tuteur" => "M. Mourad"
    ],
    [
        "id" => 102,
        "date" => "2025-11-19",
        "heure" => "14:00:00",
        "statut" => "confirmé",
        "tuteur" => "Mme. Clara"
    ],
    [
        "id" => 103,
        "date" => "2025-11-21",
        "heure" => "10:15:00",
        "statut" => "en attente",
        "tuteur" => "M. Jean"
    ]
];

$events = [];

foreach ($rendezvous as $rv) {
    list($h, $m) = explode(":", $rv["heure"]);
    $startDecimal = intval($h) + ($m / 60);

    $events[$rv["date"]][] = [
        "title" => $rv["tuteur"],
        "subtitle" => "Tutorat",
        "room" => "Biblio B-101",
        "start" => $startDecimal,
        "duration" => 1.5
    ];
}

function strftime_fr($dateObj, $format) {
    $days = ["dim", "lun", "mar", "mer", "jeu", "ven", "sam"];
    $months = ["", "jan.", "fév.", "mars", "avr.", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc."];

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
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier – Tuto+</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- ================= HEADER ================= -->
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
                <li><a href="index.php#fonctionnement">Fonctionnement</a></li>
                <li><a href="index.php#tuteurs">Tuteurs</a></li>
                <li><a href="index.php#contact">Contact</a></li>

                <li><a href="etudiant.php">Espace étudiant</a></li>
                <li><a href="tuteur.php">Espace tuteur</a></li>
                <li><a href="admin.php">Admin</a></li>
                <li><a class="active" href="calendrier.php">Calendrier</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- ================= CALENDRIER ================= -->
<main class="calendar-content-wrapper">

    <header class="cal-nav-bar">
        <a href="?date=<?=$prevWeek->format('Y-m-d')?>" class="cal-nav-btn">« Semaine précédente</a>

        <div class="current-week-label">
            Semaine du <?=strftime_fr($monday, '%d %b %Y')?>
        </div>

        <a href="?date=<?=$nextWeek->format('Y-m-d')?>" class="cal-nav-btn">Semaine suivante »</a>
    </header>

    <div class="schedule-wrapper">

        <div class="time-header"></div>

        <?php foreach ($weekDates as $wd): ?>
            <div class="header-cell"><?=$wd['formatted']?></div>
        <?php endforeach; ?>

        <div class="time-axis">
            <?php for ($h = 8; $h <= 18; $h++): ?>
                <div class="time-label"><?=sprintf("%02d:00", $h)?></div>
            <?php endfor; ?>
        </div>

        <?php foreach ($weekDates as $wd): ?>
        <div class="day-column">

            <?php for ($h = 8; $h <= 18; $h++): ?>
                <div class="bg-slot"></div>
            <?php endfor; ?>

            <?php if (isset($events[$wd["iso"]])): ?>
                <?php foreach ($events[$wd["iso"]] as $evt): 
                    $top = ($evt["start"] - 8) * 50;
                    $height = $evt["duration"] * 50;
                ?>
                    <div class="event-card" style="top: <?=$top?>px; height: <?=$height?>px;">
                        <strong><?=$evt["title"]?></strong><br>
                        <span><?=$evt["subtitle"]?></span><br>
                        <small><?=$evt["room"]?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>

</main>

<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?=date("Y")?> Collège Ahuntsic – Tuto+</p>
    </div>
</footer>

</body>
</html>
