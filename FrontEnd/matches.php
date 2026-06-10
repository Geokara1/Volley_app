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
    <link rel="stylesheet" href="/styles/matchesStyle.css">
    <title>Tournament Page</title>
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

    <main>
        <div class="seriesTitle" id="seriesTitle1">
            Series 1
        </div>
        
        <div class="series" id="series1">
            <div class="match" id="match1">
                <ul>
                    <li>Wednesday 1-4</li>
                    <li>20:00</li>
                    <li>
                        <img
                            src="/media/AEKteamlogo.jpg"
                            alt="AEK team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>VS</li>
                    <li>
                        <img
                            src="/media/OSFPteamlogo.png"
                            alt="OSFP team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>
                        25-15,18-25,25-9
                    </li>
                </ul>
            </div>

            <div class="match" id="match2">
                <ul>
                    <li>Thursday 2-4</li>
                    <li>20:00</li>
                    <li>
                        <img
                            src="/media/PANATHteamlogo.png"
                            alt="Panathinaikos team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>VS</li>
                    <li>
                        <img
                            src="/media/PAOKteamlogo.jpg"
                            alt="PAOK team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>
                        25-15,18-25,25-9
                    </li>
                </ul>
            </div>
        </div>

        <div class="seriesTitle" id="seriesTitle2">
            Series 2
        </div>

        <div class="series" id="series2">
            <div class="match" id="match3">
                <ul>
                    <li>Friday 17-4</li>
                    <li>20:00</li>
                    <li>
                        <img
                            src="/media/OSFPteamlogo.png"
                            alt="Olympiakos team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>VS</li>
                    <li>
                        <img
                            src="/media/PAOKteamlogo.jpg"
                            alt="PAOK team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>
                        <button class="scoreButton" id="scoreButton1" onclick="document.location='add_result.html'">
                            Add Score
                        </button>
                    </li>
                </ul>
            </div>

            <div class="match" id="match4">
                <ul>
                    <li>Saturday 18-4</li>
                    <li>20:00</li>
                    <li>
                        <img
                            src="/media/AEKteamlogo.jpg"
                            alt="AEK team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>VS</li>
                    <li>
                        <img
                            src="/media/PANATHteamlogo.png"
                            alt="Panathinaikos team logo"
                            class="team-logo"
                        />
                    </li>
                    <li>
                        <button class="scoreButton" id="scoreButton1" onclick="document.location='add_result.html'">
                            Add Score
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>