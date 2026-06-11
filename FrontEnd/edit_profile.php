<?php
// edit_profile.php
// Προσβάσιμο από: club_admin και referee (όχι admin, όχι visitor)
session_start();
require_once '../BackEnd/session_check.php';
require_once '../BackEnd/db.php';
requireLogin();

// Admin δεν χρειάζεται edit profile — έχει admin panel
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_panel.php');
    exit;
}

$userId  = $_SESSION['user_id'];
$success = '';
$errors  = [];

// ─── Φόρτωσε τρέχοντα στοιχεία χρήστη ──────────────────────────────────────
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// ─── HANDLE POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName']  ?? '');
    $phone     = trim($_POST['phone']     ?? '');
    $email     = trim($_POST['email']     ?? '');
    $newPass   = $_POST['newPassword']    ?? '';
    $confPass  = $_POST['confirmPassword'] ?? '';

    // ── Validation ────────────────────────────────────────────────────────────
    if (empty($firstName) || preg_match('/\d/', $firstName)) {
        $errors[] = 'Μη έγκυρο όνομα.';
    }
    if (empty($lastName) || preg_match('/\d/', $lastName)) {
        $errors[] = 'Μη έγκυρο επίθετο.';
    }
    if (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = 'Το τηλέφωνο πρέπει να έχει ακριβώς 10 ψηφία.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Μη έγκυρη διεύθυνση email.';
    }

    // Password: μόνο αν συμπληρώθηκε
    $updatePassword = false;
    if (!empty($newPass)) {
        if (strlen($newPass) < 5 || !preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>?\/\\|`~]/', $newPass)) {
            $errors[] = 'Ο νέος κωδικός χρειάζεται τουλάχιστον 5 χαρακτήρες και 1 σύμβολο.';
        } elseif ($newPass !== $confPass) {
            $errors[] = 'Οι νέοι κωδικοί δεν ταιριάζουν.';
        } else {
            $updatePassword = true;
        }
    }

    // ── Αν δεν υπάρχουν errors → UPDATE ──────────────────────────────────────
    if (empty($errors)) {

        if ($updatePassword) {
            // UPDATE με νέο password
            $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
            $upStmt = mysqli_prepare($conn, "
                UPDATE users
                SET first_name = ?, last_name = ?, phone = ?, email = ?, password = ?
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($upStmt, "sssssi",
                $firstName, $lastName, $phone, $email, $hashedPass, $userId
            );
        } else {
            // UPDATE χωρίς αλλαγή password
            $upStmt = mysqli_prepare($conn, "
                UPDATE users
                SET first_name = ?, last_name = ?, phone = ?, email = ?
                WHERE id = ?
            ");
            mysqli_stmt_bind_param($upStmt, "ssssi",
                $firstName, $lastName, $phone, $email, $userId
            );
        }

        if (mysqli_stmt_execute($upStmt)) {
            // Ανανέωσε το session ώστε το navbar να δείξει το νέο όνομα
            $_SESSION['first_name'] = $firstName;

            // Ανανέωσε το $user για να φαίνεται στη φόρμα
            $user['first_name'] = $firstName;
            $user['last_name']  = $lastName;
            $user['phone']      = $phone;
            $user['email']      = $email;

            $success = 'Το προφίλ σου ενημερώθηκε επιτυχώς!';
        } else {
            $errors[] = 'Σφάλμα κατά την αποθήκευση. Προσπαθήστε ξανά.';
        }
        mysqli_stmt_close($upStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <link rel="stylesheet" href="styles/authStyle.css">
    <style>
        .readonly-field {
            background: var(--color-background-secondary, #f5f5f5);
            padding: 8px 12px;
            border-radius: 6px;
            color: #888;
            font-size: 14px;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 12px;
            background: #E1F5EE;
            color: #085041;
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
                    <li><a href="clubs.php">See Clubs</a></li>
                    <li><a href="matches.php">Matches</a></li>
                    <li><a href="table.php">Ranking</a></li>
                    <?php if ($_SESSION['role'] === 'club_admin'): ?>
                        <li><a href="add_club.php">Add Club</a></li>
                    <?php elseif ($_SESSION['role'] === 'referee'): ?>
                        <li><a href="add_result.php">Add Result</a></li>
                    <?php endif; ?>
                    <li><a href="edit_profile.php"><strong>Edit Profile</strong></a></li>
                    <li><span>👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="authMainContainer">
        <div class="auth-card" style="max-width:520px;">
            <h2 class="auth-title">Edit Profile</h2>

            <!-- Success message -->
            <?php if ($success): ?>
                <div style="background:#f0fff4;border:1px solid #28a745;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <p style="color:#28a745;margin:0;">✓ <?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <!-- Error messages -->
            <?php if (!empty($errors)): ?>
                <div style="background:#fff3f3;border:1px solid #dc3545;border-radius:6px;padding:10px 14px;margin-bottom:16px;">
                    <?php foreach ($errors as $err): ?>
                        <p style="color:#dc3545;margin:4px 0;font-size:13px;">✕ <?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="edit_profile.php">

                <!-- Read-only: username και role -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Username (μη επεξεργάσιμο)</label>
                        <div class="readonly-field"><?= htmlspecialchars($user['username']) ?></div>
                    </div>
                    <div class="form-group">
                        <label>Role (μη επεξεργάσιμο)</label>
                        <div class="readonly-field">
                            <span class="role-badge">
                                <?= $user['role'] === 'club_admin' ? 'Διαχειριστής Συλλόγου' : 'Διαιτητής' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Editable fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">Όνομα *</label>
                        <input type="text" id="firstName" name="firstName"
                               value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Επίθετο *</label>
                        <input type="text" id="lastName" name="lastName"
                               value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Τηλέφωνο *</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <hr style="margin:20px 0;border:none;border-top:1px solid #eee;">
                <p style="font-size:13px;color:#888;margin-bottom:12px;">
                    Άλλαξε password (προαιρετικό — άφησε κενό για να κρατήσεις το τρέχον)
                </p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="newPassword">Νέο Password</label>
                        <input type="password" id="newPassword" name="newPassword">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Επιβεβαίωση Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="form-btn" style="text-decoration:none;text-align:center;">Ακύρωση</a>
                    <input type="submit" class="auth-btn" value="Αποθήκευση αλλαγών">
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