<?php
// ==================================================================
// init
// ==================================================================
session_start();

// login faux
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'etudiant';
}

// ==================================================================
// date et temps
// ==================================================================
date_default_timezone_set('America/Montreal');

$dateParam = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
try {
    $currentDate = new DateTime($dateParam);
} catch (Exception $e) {
    $currentDate = new DateTime();
}

// semaine
$monday = clone $currentDate;
$monday->modify('monday this week');

// semaine d'avant ou après
$prevWeek = clone $monday;
$prevWeek->modify('-1 week');
$nextWeek = clone $monday;
$nextWeek->modify('+1 week');

// head date
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
// data faux
// ==================================================================

// structure
$rawRendezvous = [
    [
        'id' => 101,
        'date' => '2025-11-17',   // lundi
        'heure' => '09:30:00',
        'statut' => 'confirmé',
        'etudiant_nom' => 'D Student',
        'tuteur_nom' => 'Prof. Klein'
    ],
    [
        'id' => 102,
        'date' => '2025-11-19',   // mercredi
        'heure' => '14:00:00',
        'statut' => 'confirmé',
        'etudiant_nom' => 'D Student',
        'tuteur_nom' => 'Mme. Smith'
    ],
    [
        'id' => 103,
        'date' => '2025-11-21',   // vendredi
        'heure' => '10:15:00',
        'statut' => 'en_attente',
        'etudiant_nom' => 'Other Student',
        'tuteur_nom' => 'Dr. Who'
    ]
];

// ==================================================================
// analyse de data
// ==================================================================

$events = [];

foreach ($rawRendezvous as $rv) {
    $rvDate = $rv['date'];

    // decimal (14:30 -> 14.5)
    $timeParts = explode(':', $rv['heure']);
    $hour = intval($timeParts[0]);
    $minute = intval($timeParts[1]);
    $startTimeDecimal = $hour + ($minute / 60);

    // titre par role
    if ($_SESSION['role'] == 'etudiant') {
        $title = $rv['tuteur_nom'];
        $sub = "Tutorat (Math)";
    } else {
        $title = $rv['etudiant_nom'];
        $sub = "Cours";
    }

    if (!isset($events[$rvDate])) {
        $events[$rvDate] = [];
    }
    $events[$rvDate][] = [
        'id'       => $rv['id'],
        'title'    => $title,
        'subtitle' => $sub,
        'room'     => 'Biblio B-101',
        'start'    => $startTimeDecimal,
        'duration' => 1.5 // estime 1.5h
    ];
}

// ==================================================================
// fonction assistant
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
    <title>Calendrier Tuto+ (Simulé)</title>
    <style>
        /* ================= CSS ================= */
        :root {
            --primary-blue: #003366;
            --bg-header: #e6e6e6;
            --border-color: #999;
            --slot-height: 50px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 20px;
        }

        /* --- nav --- */
        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .nav-btn {
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 6px 12px;
            color: var(--primary-blue);
            font-weight: bold;
            border-radius: 4px;
            font-size: 14px;
        }
        .nav-btn:hover { background-color: #e2e6ea; }

        .current-week-display {
            position: relative;
            font-size: 1.2em;
            font-weight: bold;
            color: var(--primary-blue);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* --- mini calendrier --- */
        .mini-calendar-popup {
            display: none;
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 240px;
            background: white;
            border: 2px solid var(--primary-blue);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
            text-align: center;
        }
        .popup-header {
            background: var(--primary-blue);
            color: white;
            padding: 5px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }
        .popup-close {
            background: red; border: none; color: white; cursor: pointer; width: 20px;
        }
        .popup-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            padding: 5px;
            gap: 2px;
        }
        .popup-cell {
            padding: 5px;
            font-size: 12px;
            text-decoration: none;
            color: #333;
        }
        .popup-cell:hover { background-color: #ddd; }

        .popup-head-cell { font-weight: bold; font-size: 12px; color: #666; }

        /* --- calendrier --- */
        .schedule-wrapper {
            display: grid;
            grid-template-columns: 60px repeat(5, 1fr);
            border: 1px solid var(--border-color);
            background-color: #fff;
        }

        .header-cell {
            background-color: var(--bg-header);
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            font-size: 14px;
        }
        .time-header { border-right: 1px solid var(--border-color); }

        .time-axis {
            border-right: 1px solid var(--border-color);
        }
        .time-label {
            height: var(--slot-height);
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 5px;
            box-sizing: border-box;
        }

        .day-column {
            position: relative;
            border-right: 1px solid var(--border-color);
        }
        .day-column:last-child { border-right: none; }

        .bg-slot {
            height: var(--slot-height);
            border-bottom: 1px solid #e0e0e0;
            box-sizing: border-box;
        }
        .bg-slot:nth-child(odd) { background-color: #fff; }
        .bg-slot:nth-child(even) { background-color: #f9f9f9; }

        .event-card {
            position: absolute;
            left: 2px;
            right: 2px;
            background-color: #eef5ff;
            border-left: 4px solid var(--primary-blue);
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
            padding: 4px;
            font-size: 11px;
            overflow: hidden;
            box-shadow: 1px 1px 4px rgba(0,0,0,0.1);
            z-index: 10;
            cursor: pointer;
        }
        .event-card:hover {
            background-color: #dbe9ff;
            z-index: 20;
        }
        .event-title { font-weight: bold; color: var(--primary-blue); display: block; }
        .event-sub { color: #555; }
    </style>
</head>
<body>

<header class="nav-bar">
    <a href="?date=<?php echo $prevWeek->format('Y-m-d'); ?>" class="nav-btn">&lt;&lt; Précédent</a>

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

<script>
	function togglePopup() {
		const popup = document.getElementById('miniCalendar');
		popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
	}
</script>

</body>
</html>