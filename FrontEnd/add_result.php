<?php
session_start();
require_once 'session_check.php';
require_once 'db.php';
requireRole('referee');

// Φόρτωσε unplayed αγώνες για να επιλέξει ο διαιτητής
$matchId = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;

$matchesStmt = mysqli_query($conn, "
    SELECT mr.id,
           md.round_number,
           ht.team_name AS home_name,
           at.team_name AS away_name
    FROM match_result mr
    JOIN matchday md ON mr.matchday_id = md.id
    JOIN team_profile ht ON mr.home_team_id = ht.id
    JOIN team_profile at ON mr.away_team_id = at.id
    WHERE mr.status = 'unplayed'
    ORDER BY md.round_number, mr.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Result</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/addResultStyle.css">
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
                        <?php if ($_SESSION['role'] === 'referee'): ?>
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

    <main class="resultMainContainer">
        <h2 class="form-title">Enter Match Results</h2>

        <?php if (!empty($_SESSION['result_error'])): ?>
            <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                <p style="color:#dc3545;margin:0;">✕ <?= htmlspecialchars($_SESSION['result_error']) ?></p>
            </div>
            <?php unset($_SESSION['result_error']); ?>
        <?php endif; ?>

        <?php if (mysqli_num_rows($matchesStmt) === 0): ?>
            <div style="text-align:center;padding:40px;color:#666;">
                <p>Δεν υπάρχουν αδιεξαγώγιστοι αγώνες προς καταχώρηση.</p>
                <a href="matches.php">← Πίσω στους αγώνες</a>
            </div>
        <?php else: ?>

        <form class="match-form"
              action="add_result_handler.php"
              method="POST"
              enctype="multipart/form-data">

            <!-- Επιλογή αγώνα -->
            <div class="form-group" style="margin-bottom:20px;">
                <label for="match_id"><strong>Επιλογή Αγώνα *</strong></label>
                <select name="match_id" id="match_id" required style="width:100%;padding:8px;margin-top:6px;">
                    <option value="" disabled selected>-- Επιλέξτε αγώνα --</option>
                    <?php while ($m = mysqli_fetch_assoc($matchesStmt)): ?>
                        <option value="<?= $m['id'] ?>"
                            <?= ($matchId === $m['id']) ? 'selected' : '' ?>>
                            Αγωνιστική <?= $m['round_number'] ?>:
                            <?= htmlspecialchars($m['home_name']) ?> vs
                            <?= htmlspecialchars($m['away_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Σκορ ανά σετ -->
            <div class="sets-container">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                <div class="set" id="set<?= $s ?>">
                    <h3>Set <?= $s ?> <?= $s === 5 ? '(προαιρετικό)' : '' ?></h3>
                    <label>Home:</label>
                    <input type="number" name="set<?= $s ?>Home" min="0" max="99"
                           <?= $s <= 3 ? 'required' : '' ?>>
                    <label>Away:</label>
                    <input type="number" name="set<?= $s ?>Away" min="0" max="99"
                           <?= $s <= 3 ? 'required' : '' ?>>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Upload φύλλου αγώνα -->
            <div class="file-upload">
                <label for="matchSheet"><strong>Upload Match Sheet (PDF):</strong></label>
                <input type="file" id="matchSheet" name="matchSheet" accept=".pdf">
            </div>

            <div class="formFunc" id="formFunc">
                <input type="reset" class="form-btn" value="Clear">
                <input type="submit" class="form-btn submit-btn" value="Submit Score">
            </div>
        </form>

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