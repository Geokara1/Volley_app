<?php                                   
session_start();
require_once '../BackEnd/session_check.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/authStyle.css">
    <style>
        /* Error message styles — μπορείς να τα μεταφέρεις στο authStyle.css */
        .error-msg {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        .input-error {
            border-color: #dc3545 !important;
        }
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
        <div class="auth-card sign-up-card">
            <h2 class="auth-title">Create an Account</h2>

            <!-- Server-side errors (από signup_handler.php) -->
            <?php if (!empty($_SESSION['signup_errors'])): ?>
                <div class="server-errors" style="background:#fff3f3;border:1px solid #dc3545;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <?php foreach ($_SESSION['signup_errors'] as $err): ?>
                        <p style="color:#dc3545;margin:4px 0;font-size:13px;">
                            ✕ <?= htmlspecialchars($err) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['signup_errors']); ?>
            <?php endif; ?>

            <!-- action: στέλνει στο signup_handler.php | method POST: τα δεδομένα ΔΕΝ φαίνονται στο URL -->
            <form class="auth-form" id="signupForm" action="../BackEnd/signup_handler.php" method="POST">

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" name="firstName" required>
                        <span class="error-msg" id="firstNameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" name="lastName" required>
                        <span class="error-msg" id="lastNameError"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <!-- ΣΗΜΑΝΤΙΚΟ: μόνο club_admin και referee — visitors είναι οι μη-εγγεγραμμένοι -->
                        <select id="role" name="role" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="club_admin">Διαχειριστής Συλλόγου</option>
                            <option value="referee">Διαιτητής</option>
                        </select>
                        <span class="error-msg" id="roleError"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" required>
                        <span class="error-msg" id="phoneError"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-msg" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error-msg" id="usernameError"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                        <span class="error-msg" id="passwordError"></span>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <span class="error-msg" id="confirmPasswordError"></span>
                    </div>
                </div>

                <div class="form-actions">
                    <input type="submit" class="auth-btn" value="Register">
                </div>

                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>

            </form>
        </div>
    </main>

    <footer>
        <div class="uopLogo" id="uopLogo">
            <img src="media/uop_new_logo.png" alt="university of peloponnese logo" class="uop-footer-logo" />
        </div>
        <div class="footerText" id="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>

    <!-- Το validation script φορτώνει ΜΕΤΑ το HTML ώστε να βρει τα elements -->
    <script src="/scripts/signup_validation.js"></script>
</body>
</html>