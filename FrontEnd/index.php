<?php
session_start();
require_once '../BackEnd/session_check.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolleyballApp</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/indexstyle.css">
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

                        <li><span style="color:#ccc;">👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                        <li><a href="../BackEnd/logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
      
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