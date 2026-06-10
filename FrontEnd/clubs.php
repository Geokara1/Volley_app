<?php
session_start();
require_once 'session_check.php';
require_once 'db.php';

$teamsResult = mysqli_query($conn, "SELECT * FROM team_profile ORDER BY team_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs Profile</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/clubsStyle.css">
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

    <main class="clubsMainContainer">

        <?php if (mysqli_num_rows($teamsResult) === 0): ?>
            <div style="text-align:center; padding:60px 20px; color:#666;">
                <h3>Δεν υπάρχουν εγγεγραμμένοι σύλλογοι ακόμα.</h3>
            </div>

        <?php else: ?>
            <?php while ($team = mysqli_fetch_assoc($teamsResult)): ?>
                <?php
                // Παίκτες για αυτή την ομάδα
                $pStmt = mysqli_prepare($conn, "SELECT * FROM player WHERE team_id = ? ORDER BY jersey_number");
                mysqli_stmt_bind_param($pStmt, "i", $team['id']);
                mysqli_stmt_execute($pStmt);
                $players = mysqli_stmt_get_result($pStmt);
                ?>

                <section class="team-profile">

                    <div class="team-header">
                        <?php if (!empty($team['team_logo'])): ?>
                            <img src="<?= htmlspecialchars($team['team_logo']) ?>"
                                 alt="<?= htmlspecialchars($team['team_name']) ?> logo"
                                 class="club-logo">
                        <?php endif; ?>
                        <div class="team-titles">
                            <h2><?= htmlspecialchars($team['team_name']) ?></h2>
                            <?php if (!empty($team['team_site'])): ?>
                                <a href="<?= htmlspecialchars($team['team_site']) ?>"
                                   target="_blank" class="club-link">Official Team Site</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="team-media">
                        <?php if (!empty($team['team_photo'])): ?>
                            <div class="media-item">
                                <h3>Team Photo</h3>
                                <img src="<?= htmlspecialchars($team['team_photo']) ?>"
                                     alt="<?= htmlspecialchars($team['team_name']) ?> photo"
                                     class="club-photo">
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($team['team_video'])): ?>
                            <div class="media-item">
                                <h3>Highlight Video</h3>
                                <a href="<?= htmlspecialchars($team['team_video']) ?>"
                                   target="_blank">▶ Watch Video</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="team-staff">
                        <h3>Technical Staff</h3>
                        <ul>
                            <?php if (!empty($team['coach_name'])): ?>
                                <li><strong>Coach:</strong> <?= htmlspecialchars($team['coach_name']) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($team['trainer_name'])): ?>
                                <li><strong>Trainer:</strong> <?= htmlspecialchars($team['trainer_name']) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($team['physio_name'])): ?>
                                <li><strong>Physiotherapist:</strong> <?= htmlspecialchars($team['physio_name']) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($team['caretaker_name'])): ?>
                                <li><strong>Caretaker:</strong> <?= htmlspecialchars($team['caretaker_name']) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($team['statistician_name'])): ?>
                                <li><strong>Statistician:</strong> <?= htmlspecialchars($team['statistician_name']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="team-roster">
                        <h3>Players Roster</h3>
                        <div class="table-responsive">
                            <table class="roster-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Full Name</th>
                                        <th>Position</th>
                                        <th>Height (m)</th>
                                        <th>Date of Birth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = mysqli_fetch_assoc($players)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['jersey_number']) ?></td>
                                            <td><?= htmlspecialchars($p['full_name']) ?></td>
                                            <td><?= htmlspecialchars($p['position']) ?></td>
                                            <td><?= htmlspecialchars($p['height']) ?></td>
                                            <td><?= htmlspecialchars($p['date_of_birth']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </section>
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
        <?php if (isLoggedIn() && $_SESSION['role'] === 'club_admin'): ?>
            <div class="addClubContainer">
                <a href="add_club.php" class="add-club-btn">Add Club</a>
            </div>
        <?php endif; ?>
    </footer>
</body>
</html>