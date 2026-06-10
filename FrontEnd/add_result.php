<?php
session_start();
require_once 'session_check.php';
requireRole('referee');      // ← μόνο για referee
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Result</title>
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/addResultStyle.css">
</head>
<body>
    <header>
        <div class="mainLogoContainer" id="mainLogoContainer">
            <a href="index.html">
                <img src="/media/mainpagelogo3.jpg" alt="page logo" class="main-page-logo" />
            </a>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="sign_up.html">Sign Up</a></li>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="clubs.html">See Clubs</a></li>
                    <li><a href="matches.html">Matches</a></li>
                    <li><a href="table.html">Ranking</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="resultMainContainer">
        <h2 class="form-title">Enter Match Results</h2>
        <form class="match-form">
            <div class="sets-container">
                <div class="set" id="set1">
                    <h3>Set 1</h3>
                    <label for="set1Home">Home:</label>
                    <input type="number" name="set1Home" min="0" max="99">
                    <label for="set1Away">Away:</label>
                    <input type="number" name="set1Away" min="0" max="99">
                </div>
                <div class="set" id="set2">
                    <h3>Set 2</h3>
                    <label for="set2Home">Home:</label>
                    <input type="number" name="set2Home" min="0" max="99">
                    <label for="set2Away">Away:</label>
                    <input type="number" name="set2Away" min="0" max="99">           
                </div>
                <div class="set" id="set3">
                    <h3>Set 3</h3>
                    <label for="set3Home">Home:</label>
                    <input type="number" name="set3Home" min="0" max="99">
                    <label for="set3Away">Away:</label>
                    <input type="number" name="set3Away" min="0" max="99">           
                </div>
                <div class="set" id="set4">
                    <h3>Set 4</h3>
                    <label for="set4Home">Home:</label>
                    <input type="number" name="set4Home" min="0" max="99">
                    <label for="set4Away">Away:</label>
                    <input type="number" name="set4Away" min="0" max="99">           
                </div>
                <div class="set" id="set5">
                    <h3>Set 5</h3>
                    <label for="set5Home">Home:</label>
                    <input type="number" name="set5Home" min="0" max="99">
                    <label for="set5Away">Away:</label>
                    <input type="number" name="set5Away" min="0" max="99">            
                </div>
            </div>

            <div class="file-upload">
                <label for="matchSheet"><strong>Upload Match Sheet (PDF):</strong></label>
                <input type="file" id="matchSheet" name="matchSheet">
            </div>

            <div class="formFunc" id="formFunc">
                <input type="reset" class="form-btn" value="Clear">
                <input type="submit" class="form-btn submit-btn" value="Submit Score">
            </div>
        </form>
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