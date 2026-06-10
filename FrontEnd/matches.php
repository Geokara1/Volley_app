<?php
session_start();
require_once 'session_check.php';
require_once 'db.php';

$matchdaysResult = mysqli_query($conn, "SELECT * FROM matchday ORDER BY round_number");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Page</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/matchesStyle.css">
    <style>
        .badge-valid    { background:#28a745; color:#fff; padding:3px 10px; border-radius:12px; font-size:13px; }
        .badge-pending  { background:#ffc107; color:#333; padding:3px 10px; border-radius:12px; font-size:13px; }
        .badge-unplayed { background:#6c757d; color:#fff; padding:3px 10px; border-radius:12px; font-size:13px; }
    </style>
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
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php if (mysqli_num_rows($matchdaysResult) === 0): ?>
            <div style="text-align:center; padding:60px 20px; color:#666;">
                <h3>Δεν υπάρχει πρόγραμμα αγώνων ακόμα.</h3>
                <p>Ο διαχειριστής δεν έχει πραγματοποιήσει κλήρωση.</p>
            </div>

        <?php else: ?>
            <?php while ($matchday = mysqli_fetch_assoc($matchdaysResult)): ?>

                <div class="seriesTitle" >
                    Series <?= $matchday['round_number'] ?>
                </div>

                <div class="series">
                    <?php
                    // Αγώνες αυτής της αγωνιστικής με ονόματα ομάδων
                    $mStmt = mysqli_prepare($conn, "
                        SELECT mr.*,
                               ht.team_name AS home_name, ht.team_logo AS home_logo,
                               at.team_name AS away_name, at.team_logo AS away_logo
                        FROM match_result mr
                        JOIN team_profile ht ON mr.home_team_id = ht.id
                        JOIN team_profile at ON mr.away_team_id = at.id
                        WHERE mr.matchday_id = ?
                        ORDER BY mr.id
                    ");
                    mysqli_stmt_bind_param($mStmt, "i", $matchday['id']);
                    mysqli_stmt_execute($mStmt);
                    $matchesRes = mysqli_stmt_get_result($mStmt);
                    ?>

                    <?php while ($match = mysqli_fetch_assoc($matchesRes)): ?>
                        <div class="match">
                            <ul>
                                <?php if (!empty($match['played_at'])): ?>
                                    <li><?= date('l d/m', strtotime($match['played_at'])) ?></li>
                                    <li><?= date('H:i', strtotime($match['played_at'])) ?></li>
                                <?php else: ?>
                                    <li>TBD</li>
                                    <li>—</li>
                                <?php endif; ?>

                                <!-- Home team -->
                                <li>
                                    <?php if (!empty($match['home_logo'])): ?>
                                        <img src="<?= htmlspecialchars($match['home_logo']) ?>"
                                             alt="<?= htmlspecialchars($match['home_name']) ?>"
                                             class="team-logo">
                                    <?php else: ?>
                                        <?= htmlspecialchars($match['home_name']) ?>
                                    <?php endif; ?>
                                </li>

                                <li>VS</li>

                                <!-- Away team -->
                                <li>
                                    <?php if (!empty($match['away_logo'])): ?>
                                        <img src="<?= htmlspecialchars($match['away_logo']) ?>"
                                             alt="<?= htmlspecialchars($match['away_name']) ?>"
                                             class="team-logo">
                                    <?php else: ?>
                                        <?= htmlspecialchars($match['away_name']) ?>
                                    <?php endif; ?>
                                </li>

                                <!-- Status / Score -->
                                <li>
                                    <?php if ($match['status'] === 'valid'): ?>
                                        <span class="badge-valid">
                                            <?= $match['home_sets'] ?> – <?= $match['away_sets'] ?>
                                        </span>
                                    <?php elseif ($match['status'] === 'pending'): ?>
                                        <span class="badge-pending">Αναμονή επικύρωσης</span>
                                    <?php else: ?>
                                        <?php if (isLoggedIn() && $_SESSION['role'] === 'referee'): ?>
                                            <a href="add_result.php?match_id=<?= $match['id'] ?>"
                                               class="scoreButton">Add Score</a>
                                        <?php else: ?>
                                            <span class="badge-unplayed">Αδιεξαγώγιστος</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    <?php endwhile; ?>

                </div>
            <?php endwhile; ?>
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