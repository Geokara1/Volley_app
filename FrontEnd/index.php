<?php
// ── ΑΥΤΕΣ ΟΙ 2 ΓΡΑΜΜΕΣ ΜΠΑΙΝΟΥΝ ΣΤΗΝ ΑΡΧΗ ΚΑΘΕ .php ΑΡΧΕΙΟΥ ───────────────
session_start();
require_once '../BackEnd/session_check.php';
// Για προστατευμένες σελίδες προσθέτεις και:
// requireLogin();           → μόνο για logged in users
// requireRole('club_admin') → μόνο για club admin
// requireRole('referee')    → μόνο για referee
// requireRole('admin')      → μόνο για admin
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolleyballApp</title>
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/indexstyle.css">
</head>
<body>
    <header>
        <div class="mainLogoContainer" id="mainLogoContainer">
            <a href="index.php">
                <img src="/media/mainpagelogo3.jpg" alt="page logo" class="main-page-logo" />
            </a>
        </div>

        <div class="navbar">
            <nav>
                <ul>
                    <!-- ── ΣΤΑΘΕΡΑ LINKS (φαίνονται σε ΟΛΟΥΣ) ── -->
                    <li><a href="clubs.php">See Clubs</a></li>
                    <li><a href="matches.php">Matches</a></li>
                    <li><a href="table.php">Ranking</a></li>

                    <!-- ── ΔΥΝΑΜΙΚΑ LINKS (αλλάζουν ανά ρόλο) ── -->
                    <?php if (!isLoggedIn()): ?>
                        <!-- Visitor: δεν είναι logged in -->
                        <li><a href="sign_up.php">Sign Up</a></li>
                        <li><a href="login.php">Login</a></li>

                    <?php else: ?>
                        <!-- Logged in: εμφάνισε links ανάλογα με ρόλο -->
                        <?php if ($_SESSION['role'] === 'club_admin'): ?>
                            <li><a href="add_club.php">Add Club</a></li>

                        <?php elseif ($_SESSION['role'] === 'referee'): ?>
                            <li><a href="add_result.php">Add Result</a></li>

                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="admin_panel.php">Admin Panel</a></li>
                        <?php endif; ?>

                        <!-- Όνομα χρήστη + Logout (για όλους τους logged in) -->
                        <li><span style="color:#ccc;">👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- Το περιεχόμενο της σελίδας εδώ -->
    </main>

    <footer>
        <div class="uopLogo" id="uopLogo">
            <img src="/media/uop_new_logo.png" alt="university of peloponnese logo" class="uop-footer-logo" />
        </div>
        <div class="footerText" id="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>
</body>
</html>