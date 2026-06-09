<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs Profile</title>
    
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/clubsStyle.css">
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

    <main class="clubsMainContainer">

        <section class="team-profile">
            
            <div class="team-header">
                <img src="/media/AEKteamlogo.jpg" alt="AEK logo" class="club-logo">
                <div class="team-titles">
                    <h2>AEK V.C.</h2>
                    <a href="https://www.aek.gr" target="_blank" class="club-link">Official Team Site</a>
                </div>
            </div>

            <div class="team-media">
                <div class="media-item">
                    <h3>Team Photo</h3>
                    <img src="/media/aek_volley_men_mainpage.jpg" alt="AEK team photo" class="club-photo">
                </div>
                <div class="media-item">
                    <h3>Highlight Video</h3>
                    <iframe 
                        src="https://www.youtube.com/embed/er1GyoDkMHc" 
                        title="Volleyball Highlight" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>

            <div class="team-staff">
                <h3>Technical Staff</h3>
                <ul>
                    <li><strong>Coach:</strong> Giannis Kalmazidis</li>
                    <li><strong>Trainer:</strong> Kostas Nikolaou</li>
                    <li><strong>Physiotherapist:</strong> Maria Petrou</li>
                    <li><strong>Caretaker:</strong> Giorgos Antoniou</li>
                    <li><strong>Statistician:</strong> Dimitris Ioannou</li>
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
                            <tr><td>1</td><td>Andreas Andreadis</td><td>Κεντρικός</td><td>2.05</td><td>14/01/1982</td></tr>
                            <tr><td>2</td><td>Nikos Zoupani</td><td>Διαγώνιος</td><td>2.02</td><td>18/03/1989</td></tr>
                            <tr><td>5</td><td>Alexandros Raptis</td><td>Ακραίος</td><td>1.98</td><td>16/02/2000</td></tr>
                            <tr><td>7</td><td>Georgios Petreas</td><td>Κεντρικός</td><td>2.01</td><td>19/11/1986</td></tr>
                            <tr><td>8</td><td>Dimitris Gkaras</td><td>Λίμπερο</td><td>1.85</td><td>12/11/1985</td></tr>
                            <tr><td>9</td><td>Menelaos Kokkinakis</td><td>Ακραίος</td><td>1.93</td><td>21/01/1993</td></tr>
                            <tr><td>10</td><td>Stavros Kasampalis</td><td>Πασαδόρος</td><td>1.91</td><td>01/06/1995</td></tr>
                            <tr><td>12</td><td>Kostas Prousalis</td><td>Πασαδόρος</td><td>1.92</td><td>06/10/1980</td></tr>
                            <tr><td>14</td><td>Panagiotis Pelekoudas</td><td>Κεντρικός</td><td>2.04</td><td>08/11/1989</td></tr>
                            <tr><td>15</td><td>Thanasis Protopsaltis</td><td>Ακραίος</td><td>1.96</td><td>12/09/1993</td></tr>
                            <tr><td>18</td><td>Mitar Tzourits</td><td>Διαγώνιος</td><td>2.11</td><td>25/04/1989</td></tr>
                            <tr><td>20</td><td>Giannis Spanoudakis</td><td>Λίμπερο</td><td>1.88</td><td>15/07/2001</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
        

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

        <div class="addClubContainer">
            <a href="add_club.html" class="add-club-btn">Add Club</a>
        </div>
    </footer>
</body>
</html>