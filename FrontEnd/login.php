<?php
session_start();
// Αν είναι ήδη logged in, πήγαινέ τον στην αρχική
if (!empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/authStyle.css">
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
                    <li><a href="sign_up.php">Sign Up</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="clubs.php">See Clubs</a></li>
                    <li><a href="matches.php">Matches</a></li>
                    <li><a href="table.php">Ranking</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="authMainContainer">
        <div class="auth-card">
            <h2 class="auth-title">Login to Your Account</h2>

            <!-- Μήνυμα επιτυχίας από εγγραφή (signup_handler → login.php) -->
            <?php if (!empty($_SESSION['signup_success'])): ?>
                <div style="background:#f0fff4;border:1px solid #28a745;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <p style="color:#28a745;margin:0;font-size:13px;">
                        ✓ <?= htmlspecialchars($_SESSION['signup_success']) ?>
                    </p>
                </div>
                <?php unset($_SESSION['signup_success']); ?>
            <?php endif; ?>

            <!-- Μήνυμα λάθους από login (login_handler → login.php) -->
            <?php if (!empty($_SESSION['login_error'])): ?>
                <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <p style="color:#dc3545;margin:0;font-size:13px;">
                        ✕ <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </p>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form class="auth-form" id="loginForm" action="/BackEnd/login_handler.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-actions">
                    <input type="submit" class="auth-btn" value="Login">
                </div>

                <div class="auth-links">
                    <p>Don't have an account? <a href="sign_up.php">Sign up here</a></p>
                </div>
            </form>
        </div>
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