<?php
session_start();
require_once 'session_check.php';
requireRole('club_admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Club</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/addClubStyle.css">
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
                        <li><span>👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="addClubMainContainer">
        <div class="form-wrapper">
            <h2 class="form-title">Register a New Club</h2>

            <?php if (!empty($_SESSION['club_error'])): ?>
                <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <p style="color:#dc3545;margin:0;">✕ <?= htmlspecialchars($_SESSION['club_error']) ?></p>
                </div>
                <?php unset($_SESSION['club_error']); ?>
            <?php endif; ?>

            <!-- enctype ΑΠΑΡΑΙΤΗΤΟ για file uploads -->
            <form class="add-club-form"
                  action="add_club_handler.php"
                  method="POST"
                  enctype="multipart/form-data">

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
                        <label for="teamVideo">Highlight Video URL *</label>
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

                <!-- Players Roster (12 Players) — αναλλοίωτο -->
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
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <tr>
                                    <td><input type="number" name="p<?= $i ?>_num" min="0" max="99" required></td>
                                    <td><input type="text" name="p<?= $i ?>_name" required></td>
                                    <td>
                                        <select name="p<?= $i ?>_pos" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="πασαδόρος">Πασαδόρος</option>
                                            <option value="λίμπερο">Λίμπερο</option>
                                            <option value="ακραίος">Ακραίος</option>
                                            <option value="κεντρικός">Κεντρικός</option>
                                            <option value="διαγώνιος">Διαγώνιος</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="p<?= $i ?>_height" step="0.01" min="0" required></td>
                                    <td><input type="date" name="p<?= $i ?>_dob" required></td>
                                </tr>
                                <?php endfor; ?>
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
            <img src="media/uop_new_logo.png" alt="university of peloponnese logo" class="uop-footer-logo" />
        </div>
        <div class="footerText" id="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>
</body>
</html>