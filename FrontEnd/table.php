<?php
session_start();
require_once 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/rankItemStyle.css">
    <title>Team Rank Table</title>
</head>
<body>
    <header>
        <div class="mainLogoContainer" id="mainLogoContainer">
            <a href="index.html">
                <img
                    src="/media/mainpagelogo3.jpg"
                    alt="page logo"
                    class="main-page-logo" 
                />
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

    <main class="mainRankingContainer" id="mainRankingContainer">

        <div class="rankItem" id="rankItemTitles">
            <ul>
                <li>No</li>
                <li>Team</li>
                <li>Total Points</li>
                <li>Matches Played</li>
                <li>Wins</li>
                <li>Losses</li>
                <li>Sets Won</li>
                <li>Sets Lost</li>
            </ul>
        </div>
        <div class="rankItem" id="rankItem_1">
            <ul>
                <li>1</li>
                <li>
                    <img
                        src="/media/OSFPteamlogo.png"
                        alt="Olympiakos logo"
                        class="team-logo"
                    />
                </li>
                <li>20</li>
                <li>8</li>
                <li>5</li>
                <li>3</li>
                <li>18</li>
                <li>6</li>
            </ul>
        </div>
        <div class="rankItem" id="rankItem_2">
            <ul>
                <li>1</li>
                <li>
                    <img
                        src="/media/PAOKteamlogo.jpg"
                        alt="PAOK logo"
                        class="team-logo"
                    />
                </li>
                <li>20</li>
                <li>8</li>
                <li>5</li>
                <li>3</li>
                <li>18</li>
                <li>6</li>
            </ul>
        </div>
        <div class="rankItem" id="rankItem_3">
            <ul>
                <li>1</li>
                <li>
                    <img
                        src="/media/AEKteamlogo.jpg"
                        alt="AEK logo"
                        class="team-logo"
                    />
                </li>
                <li>20</li>
                <li>8</li>
                <li>5</li>
                <li>3</li>
                <li>18</li>
                <li>6</li>
            </ul>
        </div>
        <div class="rankItem" id="rankItem_4">
            <ul>
                <li>1</li>
                <li>
                    <img
                        src="/media/PANATHteamlogo.png"
                        alt="Panathinaikos logo"
                        class="team-logo"
                    />
                </li>
                <li>20</li>
                <li>8</li>
                <li>5</li>
                <li>3</li>
                <li>18</li>
                <li>6</li>
            </ul>
        </div>

    </main>

    <footer>
        <div class="uopLogo" id="uopLogo">
            <img
                src="/media/uop_new_logo.png"
                alt="univeristy of peloponnese logo"
                class="uop-footer-logo"
            />
        </div>

        <div class="footerText" id="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>
</body>
</html>