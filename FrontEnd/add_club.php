<?php
session_start();
require_once 'session_check.php';
requireRole('club_admin');   // ← μόνο για club_admin
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Club</title>
    <link rel="stylesheet" href="/styles/headerstyle.css">
    <link rel="stylesheet" href="/styles/footerstyle.css">
    <link rel="stylesheet" href="/styles/addClubStyle.css">
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

    <main class="addClubMainContainer">
        <div class="form-wrapper">
            <h2 class="form-title">Register a New Club</h2>
            <form class="add-club-form">
                
                <!-- General Team Info -->
                <div class="form-section">
                    <h3>General Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="teamName">Team Name *</label>
                            <input type="text" id="teamName" name="teamName" required>
                        </div>
                        <div class="form-group">
                            <label for="teamSite">Team Website URL *</label>
                            <input type="url" id="teamSite" name="teamSite" placeholder="https://..." required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="teamLogo">Team Logo *</label>
                            <input type="file" id="teamLogo" name="teamLogo" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label for="teamPhoto">Team Photo *</label>
                            <input type="file" id="teamPhoto" name="teamPhoto" accept="image/*" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="teamVideo">Highlight Video (URL or File) *</label>
                        <input type="url" id="teamVideo" name="teamVideo" placeholder="https://youtube.com/..." required>
                    </div>
                </div>

                <!-- Technical Staff -->
                <div class="form-section">
                    <h3>Technical Staff</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="coachName">Coach Name *</label>
                            <input type="text" id="coachName" name="coachName" required>
                        </div>
                        <div class="form-group">
                            <label for="trainerName">Trainer Name</label>
                            <input type="text" id="trainerName" name="trainerName">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="physioName">Physiotherapist Name</label>
                            <input type="text" id="physioName" name="physioName">
                        </div>
                        <div class="form-group">
                            <label for="caretakerName">Caretaker / Manager Name</label>
                            <input type="text" id="caretakerName" name="caretakerName">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="statisticianName">Statistician Name</label>
                        <input type="text" id="statisticianName" name="statisticianName">
                    </div>
                </div>

                <!-- Players Roster (12 Players) -->
                <div class="form-section">
                    <h3>Players Roster (Minimum 12 Players) *</h3>
                    <div class="table-responsive">
                        <table class="roster-input-table">
                            <thead>
                                <tr>
                                    <th>Jersey No.* (0-99)</th>
                                    <th>Full Name *</th>
                                    <th>Position *</th>
                                    <th>Height (m) *</th>
                                    <th>Date of Birth *</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Player 1 to 12 (Hardcoded to avoid JS) -->
                                <!-- Player 1 -->
                                <tr>
                                    <td><input type="number" name="p1_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p1_name" required></td>
                                    <td>
                                        <select name="p1_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p1_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p1_dob" required></td>
                                </tr>
                                <!-- Player 2 -->
                                <tr>
                                    <td><input type="number" name="p2_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p2_name" required></td>
                                    <td>
                                        <select name="p2_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p2_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p2_dob" required></td>
                                </tr>
                                <!-- Player 3 -->
                                <tr>
                                    <td><input type="number" name="p3_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p3_name" required></td>
                                    <td>
                                        <select name="p3_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p3_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p3_dob" required></td>
                                </tr>
                                <!-- Player 4 -->
                                <tr>
                                    <td><input type="number" name="p4_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p4_name" required></td>
                                    <td>
                                        <select name="p4_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p4_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p4_dob" required></td>
                                </tr>
                                <!-- Player 5 -->
                                <tr>
                                    <td><input type="number" name="p5_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p5_name" required></td>
                                    <td>
                                        <select name="p5_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p5_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p5_dob" required></td>
                                </tr>
                                <!-- Player 6 -->
                                <tr>
                                    <td><input type="number" name="p6_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p6_name" required></td>
                                    <td>
                                        <select name="p6_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p6_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p6_dob" required></td>
                                </tr>
                                <!-- Player 7 -->
                                <tr>
                                    <td><input type="number" name="p7_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p7_name" required></td>
                                    <td>
                                        <select name="p7_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p7_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p7_dob" required></td>
                                </tr>
                                <!-- Player 8 -->
                                <tr>
                                    <td><input type="number" name="p8_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p8_name" required></td>
                                    <td>
                                        <select name="p8_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p8_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p8_dob" required></td>
                                </tr>
                                <!-- Player 9 -->
                                <tr>
                                    <td><input type="number" name="p9_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p9_name" required></td>
                                    <td>
                                        <select name="p9_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p9_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p9_dob" required></td>
                                </tr>
                                <!-- Player 10 -->
                                <tr>
                                    <td><input type="number" name="p10_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p10_name" required></td>
                                    <td>
                                        <select name="p10_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p10_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p10_dob" required></td>
                                </tr>
                                <!-- Player 11 -->
                                <tr>
                                    <td><input type="number" name="p11_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p11_name" required></td>
                                    <td>
                                        <select name="p11_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p11_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p11_dob" required></td>
                                </tr>
                                <!-- Player 12 -->
                                <tr>
                                    <td><input type="number" name="p12_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p12_name" required></td>
                                    <td>
                                        <select name="p12_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p12_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p12_dob" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-actions">
                    <input type="reset" class="form-btn" value="Clear Form">
                    <input type="submit" class="form-btn submit-btn" value="Add Club">
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