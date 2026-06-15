<?php
session_start();
require_once '../BackEnd/session_check.php';
require_once '../BackEnd/db.php';


$standings = [];

$result = mysqli_query($conn, "
    SELECT mr.*,
           ht.id AS hid, ht.team_name AS home_name, ht.team_logo AS home_logo,
           at.id AS aid, at.team_name AS away_name, at.team_logo AS away_logo
    FROM match_result mr
    JOIN team_profile ht ON mr.home_team_id = ht.id
    JOIN team_profile at ON mr.away_team_id = at.id
    WHERE mr.status = 'valid'
");

while ($m = mysqli_fetch_assoc($result)) {
    $hid = $m['hid'];
    $aid = $m['aid'];
    $hs  = (int)$m['home_sets'];
    $as  = (int)$m['away_sets'];

    foreach ([$hid => $m['home_name'], $aid => $m['away_name']] as $tid => $tname) {
        if (!isset($standings[$tid])) {
            $logo = ($tid === $hid) ? $m['home_logo'] : $m['away_logo'];
            $standings[$tid] = [
                'name'       => $tname,
                'logo'       => $logo,
                'played'     => 0,
                'wins'       => 0,
                'losses'     => 0,
                'sets_won'   => 0,
                'sets_lost'  => 0,
                'points'     => 0
            ];
        }
    }

    $standings[$hid]['played']++;
    $standings[$aid]['played']++;
    $standings[$hid]['sets_won']  += $hs;
    $standings[$hid]['sets_lost'] += $as;
    $standings[$aid]['sets_won']  += $as;
    $standings[$aid]['sets_lost'] += $hs;

    if ($hs > $as) {
        
        $standings[$hid]['wins']++;
        $standings[$aid]['losses']++;
        $standings[$hid]['points'] += ($as === 2) ? 2 : 3;
        $standings[$aid]['points'] += ($as === 2) ? 1 : 0;
    } else {
        
        $standings[$aid]['wins']++;
        $standings[$hid]['losses']++;
        $standings[$aid]['points'] += ($hs === 2) ? 2 : 3;
        $standings[$hid]['points'] += ($hs === 2) ? 1 : 0;
    }
}


usort($standings, function ($a, $b) {
    if ($b['points'] !== $a['points']) return $b['points'] - $a['points'];
    $ratioA = $a['sets_lost'] > 0 ? $a['sets_won'] / $a['sets_lost'] : $a['sets_won'];
    $ratioB = $b['sets_lost'] > 0 ? $b['sets_won'] / $b['sets_lost'] : $b['sets_won'];
    return $ratioB <=> $ratioA;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Rank Table</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/rankItemStyle.css">
</head>
<body>
    <header>
        <div class="mainLogoContainer" id="mainLogoContainer">
            <a href="index.php">
                <img src="media/mainpagelogo3.jpg" alt="page logo" class="main-page-logo" />
            </a>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="clubs.php">See Clubs</a></li>
                    <li><a href="matches.php">Matches</a></li>
                    <li><a href="table.php">Ranking</a></li>
                    <?php if (!isLoggedIn()): ?>
                        <li><a href="sign_up.php">Sign Up</a></li>
                        <li><a href="login.php">Login</a></li>
                    <?php else: ?>
                        <?php if ($_SESSION['role'] === 'club_admin'): ?>
                            <li><a href="add_club.php">Add Club</a></li>
                        <?php elseif ($_SESSION['role'] === 'referee'): ?>
                            <li><a href="add_result.php">Add Result</a></li>
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="admin_panel.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><span>👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                        <li><a href="../BackEnd/logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="mainRankingContainer" id="mainRankingContainer">


        <div class="rankItem" id="rankItemTitles">
            <ul>
                <li>No</li>
                <li>Team</li>
                <li>Points</li>
                <li>Played</li>
                <li>Wins</li>
                <li>Losses</li>
                <li>Sets Won</li>
                <li>Sets Lost</li>
            </ul>
        </div>

        <?php if (empty($standings)): ?>
            <div style="text-align:center; padding:40px; color:#666;">
                <p>Δεν υπάρχουν επικυρωμένα αποτελέσματα ακόμα.</p>
            </div>
        <?php else: ?>
            <?php foreach ($standings as $rank => $team): ?>
                <div class="rankItem" id="rankItem_<?= $rank + 1 ?>">
                    <ul>
                        <li><?= $rank + 1 ?></li>
                        <li>
                            <?php if (!empty($team['logo'])): ?>
                                <img src="<?= htmlspecialchars($team['logo']) ?>"
                                     alt="<?= htmlspecialchars($team['name']) ?>"
                                     class="team-logo">
                            <?php else: ?>
                                <?= htmlspecialchars($team['name']) ?>
                            <?php endif; ?>
                        </li>
                        <li><?= $team['points'] ?></li>
                        <li><?= $team['played'] ?></li>
                        <li><?= $team['wins'] ?></li>
                        <li><?= $team['losses'] ?></li>
                        <li><?= $team['sets_won'] ?></li>
                        <li><?= $team['sets_lost'] ?></li>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <footer>
        <div class="uopLogo" id="uopLogo">
            <img src="media/uop_new_logo.png" alt="university of peloponnese logo" class="uop-footer-logo" />
        </div>
        <div class="footerText" id="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>
</body>
</html>