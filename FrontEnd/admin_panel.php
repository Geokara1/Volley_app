<?php
// admin_panel.php
session_start();
require_once '/BackEnd/session_check.php';
require_once '/BackEnd/db.php';
requireRole('admin');

// ─── ROUND-ROBIN ALGORITHM ───────────────────────────────────────────────────
function generateRoundRobin(array $teamIds): array {
    $n       = count($teamIds); // 10
    $fixed   = $teamIds[0];
    $rotating = array_slice($teamIds, 1); // υπόλοιπες 9

    $firstLeg = [];
    for ($round = 0; $round < $n - 1; $round++) {
        $matches   = [];
        $matches[] = [$fixed, $rotating[0]]; // σταθερή vs πρώτη της rotation
        for ($i = 1; $i < $n / 2; $i++) {
            $matches[] = [$rotating[$i], $rotating[$n - 1 - $i]];
        }
        $firstLeg[] = $matches;
        // Rotation: τελευταία ομάδα μπαίνει πρώτη
        array_unshift($rotating, array_pop($rotating));
    }

    // 2ος γύρος: αντίστροφα home/away
    $secondLeg = [];
    foreach ($firstLeg as $round) {
        $secondLeg[] = array_map(fn($m) => [$m[1], $m[0]], $round);
    }

    return array_merge($firstLeg, $secondLeg); // 18 αγωνιστικές
}

// ─── HANDLE POST ACTIONS ─────────────────────────────────────────────────────
$flashMsg  = '';
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Ενεργοποίηση χρήστη
    if ($action === 'activate_user') {
        $uid  = intval($_POST['user_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "UPDATE users SET status='active' WHERE id=? AND role!='admin'");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $flashMsg = 'Ο χρήστης ενεργοποιήθηκε επιτυχώς.';
    }

    // 2. Απενεργοποίηση χρήστη
    elseif ($action === 'deactivate_user') {
        $uid  = intval($_POST['user_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "UPDATE users SET status='inactive' WHERE id=? AND role!='admin'");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $flashMsg = 'Ο χρήστης απενεργοποιήθηκε.';
    }

    // 3. Επικύρωση αγώνα
    elseif ($action === 'validate_match') {
        $mid  = intval($_POST['match_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "UPDATE match_result SET status='valid' WHERE id=? AND status='pending'");
        mysqli_stmt_bind_param($stmt, "i", $mid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $flashMsg = 'Το αποτέλεσμα επικυρώθηκε.';
    }

    // 4. Δημιουργία κλήρωσης
    elseif ($action === 'generate_schedule') {
        $teamsResult = mysqli_query($conn, "SELECT id FROM team_profile ORDER BY id");
        $teamIds     = [];
        while ($t = mysqli_fetch_assoc($teamsResult)) {
            $teamIds[] = $t['id'];
        }

        if (count($teamIds) !== 10) {
            $flashMsg  = 'Χρειάζονται ακριβώς 10 σύλλογοι. Αυτή τη στιγμή υπάρχουν ' . count($teamIds) . '.';
            $flashType = 'error';
        } else {
            // Διαγραφή παλιού προγράμματος
            mysqli_query($conn, "DELETE FROM match_result");
            mysqli_query($conn, "DELETE FROM matchday");

            $schedule = generateRoundRobin($teamIds);

            foreach ($schedule as $roundIdx => $roundMatches) {
                $roundNo = $roundIdx + 1;

                // INSERT αγωνιστική
                $mdStmt = mysqli_prepare($conn, "INSERT INTO matchday (round_number) VALUES (?)");
                mysqli_stmt_bind_param($mdStmt, "i", $roundNo);
                mysqli_stmt_execute($mdStmt);
                $matchdayId = mysqli_insert_id($conn);
                mysqli_stmt_close($mdStmt);

                // INSERT αγώνες
                $mStmt = mysqli_prepare($conn,
                    "INSERT INTO match_result (matchday_id, home_team_id, away_team_id, status)
                     VALUES (?, ?, ?, 'unplayed')"
                );
                foreach ($roundMatches as [$homeId, $awayId]) {
                    mysqli_stmt_bind_param($mStmt, "iii", $matchdayId, $homeId, $awayId);
                    mysqli_stmt_execute($mStmt);
                }
                mysqli_stmt_close($mStmt);
            }
            $flashMsg = 'Η κλήρωση δημιουργήθηκε: 18 αγωνιστικές, 90 αγώνες.';
        }
    }

    // PRG pattern: redirect για να αποφύγουμε double submit
    $redirectTab = $_POST['redirect_tab'] ?? 'users';
    header('Location: admin_panel.php?tab=' . $redirectTab
        . '&msg=' . urlencode($flashMsg)
        . '&type=' . $flashType);
    exit;
}

// Flash message από redirect
if (!empty($_GET['msg'])) {
    $flashMsg  = $_GET['msg'];
    $flashType = $_GET['type'] ?? 'success';
}

// ─── ACTIVE TAB ───────────────────────────────────────────────────────────────
$tab = $_GET['tab'] ?? 'users';

// ─── FETCH USERS ──────────────────────────────────────────────────────────────
$roleFilter   = $_GET['role']   ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';

$userWhere = "WHERE u.role IN ('club_admin','referee')";
if ($roleFilter !== 'all')   $userWhere .= " AND u.role='" . mysqli_real_escape_string($conn, $roleFilter) . "'";
if ($statusFilter !== 'all') $userWhere .= " AND u.status='" . mysqli_real_escape_string($conn, $statusFilter) . "'";

$usersResult = mysqli_query($conn,
    "SELECT u.*, tp.team_name
     FROM users u
     LEFT JOIN team_profile tp ON tp.admin_id = u.id
     $userWhere
     ORDER BY u.status ASC, u.role ASC, u.last_name ASC"
);

// ─── FETCH MATCHES ────────────────────────────────────────────────────────────
$matchStatusFilter = $_GET['match_status'] ?? 'all';

$matchWhere = ($matchStatusFilter !== 'all')
    ? "WHERE mr.status='" . mysqli_real_escape_string($conn, $matchStatusFilter) . "'"
    : '';

$matchesResult = mysqli_query($conn,
    "SELECT mr.*, md.round_number,
            ht.team_name AS home_name, at.team_name AS away_name,
            u.username AS referee_name
     FROM match_result mr
     JOIN matchday md ON mr.matchday_id = md.id
     JOIN team_profile ht ON mr.home_team_id = ht.id
     JOIN team_profile at ON mr.away_team_id = at.id
     LEFT JOIN users u ON mr.referee_id = u.id
     $matchWhere
     ORDER BY md.round_number, mr.id"
);

// ─── SCHEDULE INFO ────────────────────────────────────────────────────────────
$teamCount      = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM team_profile"))['c'];
$scheduleExists = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM matchday"))['c'] > 0;
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles/headerstyle.css">
    <link rel="stylesheet" href="styles/footerstyle.css">
    <style>
        body { font-family: sans-serif; margin: 0; }
        .admin-main { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        /* Tabs */
        .tabs { display: flex; gap: 4px; margin-bottom: 24px; border-bottom: 2px solid #ddd; }
        .tab-btn {
            padding: 10px 24px; background: #f5f5f5; border: none;
            border-radius: 8px 8px 0 0; cursor: pointer; font-size: 14px;
            text-decoration: none; color: #555;
        }
        .tab-btn.active { background: #1D9E75; color: #fff; font-weight: 500; }

        /* Flash message */
        .flash-success { background:#f0fff4; border:1px solid #28a745; border-radius:6px; padding:10px 16px; margin-bottom:20px; color:#28a745; }
        .flash-error   { background:#fff3f3; border:1px solid #dc3545; border-radius:6px; padding:10px 16px; margin-bottom:20px; color:#dc3545; }

        /* Filters */
        .filters { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
        .filters a { padding:6px 14px; border-radius:20px; background:#f0f0f0; font-size:13px; text-decoration:none; color:#555; }
        .filters a.active { background:#1D9E75; color:#fff; }

        /* Table */
        table { width:100%; border-collapse:collapse; font-size:13px; }
        th { background:#f5f5f5; padding:10px 12px; text-align:left; font-weight:500; border-bottom:2px solid #e0e0e0; }
        td { padding:9px 12px; border-bottom:1px solid #eee; vertical-align:middle; }
        tr:hover td { background:#fafafa; }

        /* Badges */
        .badge { display:inline-block; padding:2px 10px; border-radius:10px; font-size:12px; }
        .badge-active   { background:#E1F5EE; color:#085041; }
        .badge-inactive { background:#FFF3CD; color:#856404; }
        .badge-valid    { background:#E1F5EE; color:#085041; }
        .badge-pending  { background:#FFF3CD; color:#856404; }
        .badge-unplayed { background:#e9ecef; color:#495057; }
        .badge-admin    { background:#E6F1FB; color:#185FA5; }
        .badge-referee  { background:#F0E6FB; color:#6B2FA0; }

        /* Buttons */
        .btn { padding:5px 14px; border:none; border-radius:5px; cursor:pointer; font-size:13px; }
        .btn-activate   { background:#1D9E75; color:#fff; }
        .btn-deactivate { background:#dc3545; color:#fff; }
        .btn-validate   { background:#378ADD; color:#fff; }
        .btn-generate   { background:#BA7517; color:#fff; padding:10px 24px; font-size:14px; }

        /* Schedule info box */
        .info-box { border:1px solid #ddd; border-radius:8px; padding:20px; margin-bottom:20px; }
        .info-box h3 { margin-top:0; }
        .warning { background:#FFF3CD; border:1px solid #ffc107; border-radius:6px; padding:10px 14px; margin-bottom:12px; font-size:13px; }
    </style>
</head>
<body>
    <header>
        <div class="mainLogoContainer">
            <a href="index.php">
                <img src="media/mainpagelogo3.jpg" alt="logo" class="main-page-logo">
            </a>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="clubs.php">See Clubs</a></li>
                    <li><a href="matches.php">Matches</a></li>
                    <li><a href="table.php">Ranking</a></li>
                    <li><a href="admin_panel.php"><strong>Admin Panel</strong></a></li>
                    <li><span>👤 <?= htmlspecialchars($_SESSION['first_name']) ?></span></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <h2 style="margin-bottom:20px;">Admin Panel</h2>

        <!-- Flash message -->
        <?php if ($flashMsg): ?>
            <div class="flash-<?= $flashType === 'error' ? 'error' : 'success' ?>">
                <?= htmlspecialchars($flashMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <a href="admin_panel.php?tab=users"    class="tab-btn <?= $tab==='users'    ? 'active' : '' ?>">👥 Χρήστες</a>
            <a href="admin_panel.php?tab=matches"  class="tab-btn <?= $tab==='matches'  ? 'active' : '' ?>">⚽ Αγώνες</a>
            <a href="admin_panel.php?tab=schedule" class="tab-btn <?= $tab==='schedule' ? 'active' : '' ?>">📅 Κλήρωση</a>
        </div>

        <!-- ══ TAB 1: USERS ══════════════════════════════════════════════════ -->
        <?php if ($tab === 'users'): ?>

            <!-- Φίλτρα -->
            <div class="filters">
                <strong style="line-height:2;">Ρόλος:</strong>
                <a href="?tab=users&role=all&status=<?= $statusFilter ?>"    class="<?= $roleFilter==='all'        ? 'active' : '' ?>">Όλοι</a>
                <a href="?tab=users&role=club_admin&status=<?= $statusFilter ?>" class="<?= $roleFilter==='club_admin' ? 'active' : '' ?>">Club Admin</a>
                <a href="?tab=users&role=referee&status=<?= $statusFilter ?>"   class="<?= $roleFilter==='referee'    ? 'active' : '' ?>">Referee</a>
                &nbsp;
                <strong style="line-height:2;">Status:</strong>
                <a href="?tab=users&role=<?= $roleFilter ?>&status=all"      class="<?= $statusFilter==='all'      ? 'active' : '' ?>">Όλα</a>
                <a href="?tab=users&role=<?= $roleFilter ?>&status=inactive" class="<?= $statusFilter==='inactive' ? 'active' : '' ?>">Inactive</a>
                <a href="?tab=users&role=<?= $roleFilter ?>&status=active"   class="<?= $statusFilter==='active'   ? 'active' : '' ?>">Active</a>
            </div>

            <?php if (mysqli_num_rows($usersResult) === 0): ?>
                <p style="color:#888;">Δεν βρέθηκαν χρήστες με αυτά τα φίλτρα.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ονοματεπώνυμο</th>
                        <th>Username</th>
                        <th>Ρόλος</th>
                        <th>Σύλλογος</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Ενέργεια</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($usersResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['last_name'] . ' ' . $u['first_name']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span class="badge badge-<?= $u['role'] === 'club_admin' ? 'admin' : 'referee' ?>">
                                <?= $u['role'] === 'club_admin' ? 'Club Admin' : 'Referee' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['team_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $u['status'] ?>">
                                <?= $u['status'] === 'active' ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['status'] === 'inactive'): ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="activate_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="redirect_tab" value="users">
                                    <button type="submit" class="btn btn-activate">✓ Ενεργοποίηση</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" onsubmit="return confirm('Απενεργοποίηση χρήστη;')">
                                    <input type="hidden" name="action" value="deactivate_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="redirect_tab" value="users">
                                    <button type="submit" class="btn btn-deactivate">✗ Απενεργοποίηση</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>

        <!-- ══ TAB 2: MATCHES ════════════════════════════════════════════════ -->
        <?php elseif ($tab === 'matches'): ?>

            <!-- Φίλτρα status -->
            <div class="filters">
                <strong style="line-height:2;">Status:</strong>
                <a href="?tab=matches&match_status=all"      class="<?= $matchStatusFilter==='all'      ? 'active' : '' ?>">Όλοι</a>
                <a href="?tab=matches&match_status=pending"  class="<?= $matchStatusFilter==='pending'  ? 'active' : '' ?>">Pending</a>
                <a href="?tab=matches&match_status=valid"    class="<?= $matchStatusFilter==='valid'    ? 'active' : '' ?>">Valid</a>
                <a href="?tab=matches&match_status=unplayed" class="<?= $matchStatusFilter==='unplayed' ? 'active' : '' ?>">Unplayed</a>
            </div>

            <?php if (mysqli_num_rows($matchesResult) === 0): ?>
                <p style="color:#888;">Δεν βρέθηκαν αγώνες.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Αγωνιστική</th>
                        <th>Γηπεδούχος</th>
                        <th>Φιλοξενούμενος</th>
                        <th>Σκορ</th>
                        <th>Status</th>
                        <th>Διαιτητής</th>
                        <th>Ενέργεια</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($m = mysqli_fetch_assoc($matchesResult)): ?>
                    <tr>
                        <td style="text-align:center;"><?= $m['round_number'] ?></td>
                        <td><?= htmlspecialchars($m['home_name']) ?></td>
                        <td><?= htmlspecialchars($m['away_name']) ?></td>
                        <td style="text-align:center;">
                            <?= $m['status'] !== 'unplayed' ? $m['home_sets'] . ' – ' . $m['away_sets'] : '—' ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $m['status'] ?>">
                                <?= ucfirst($m['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($m['referee_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($m['status'] === 'pending'): ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="validate_match">
                                    <input type="hidden" name="match_id" value="<?= $m['id'] ?>">
                                    <input type="hidden" name="redirect_tab" value="matches">
                                    <button type="submit" class="btn btn-validate">✓ Επικύρωση</button>
                                </form>
                            <?php else: ?>
                                <span style="color:#aaa;font-size:12px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>

        <!-- ══ TAB 3: SCHEDULE ═══════════════════════════════════════════════ -->
        <?php elseif ($tab === 'schedule'): ?>

            <div class="info-box">
                <h3>Κλήρωση Πρωταθλήματος</h3>

                <p>
                    Καταχωρημένοι σύλλογοι:
                    <strong style="color:<?= $teamCount===10 ? '#1D9E75' : '#dc3545' ?>;">
                        <?= $teamCount ?> / 10
                    </strong>
                </p>

                <?php if ($teamCount !== 10): ?>
                    <div class="warning">
                        ⚠️ Χρειάζονται ακριβώς <strong>10 σύλλογοι</strong> για να γίνει κλήρωση.
                        <?= $teamCount < 10 ? 'Λείπουν ' . (10-$teamCount) . ' ακόμα.' : 'Υπάρχουν παραπάνω από 10.' ?>
                    </div>
                <?php else: ?>
                    <?php if ($scheduleExists): ?>
                        <div class="warning">
                            ⚠️ Υπάρχει ήδη πρόγραμμα. Η νέα κλήρωση θα <strong>διαγράψει</strong> όλους τους υπάρχοντες αγώνες και αποτελέσματα.
                        </div>
                    <?php endif; ?>

                    <p style="font-size:13px;color:#666;margin-bottom:16px;">
                        Θα δημιουργηθούν: <strong>18 αγωνιστικές</strong>, <strong>90 αγώνες</strong> (κάθε ομάδα παίζει 2× με κάθε άλλη — 1× εντός, 1× εκτός έδρας).
                    </p>

                    <form method="POST" onsubmit="return confirm('<?= $scheduleExists ? 'ΠΡΟΣΟΧΗ: Θα διαγραφούν όλοι οι υπάρχοντες αγώνες. Συνέχεια;' : 'Δημιουργία κλήρωσης;' ?>')">
                        <input type="hidden" name="action" value="generate_schedule">
                        <input type="hidden" name="redirect_tab" value="schedule">
                        <button type="submit" class="btn btn-generate">
                            📅 <?= $scheduleExists ? 'Αναδημιουργία κλήρωσης' : 'Δημιουργία κλήρωσης' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if ($scheduleExists): ?>
                <p style="font-size:13px;color:#666;">
                    Για να δεις το πρόγραμμα: <a href="matches.php">→ Matches page</a>
                </p>
            <?php endif; ?>

        <?php endif; ?>
    </main>

    <footer>
        <div class="uopLogo">
            <img src="media/uop_new_logo.png" alt="university of peloponnese logo" class="uop-footer-logo">
        </div>
        <div class="footerText">
            &#169; 2026 Ioannis Spanoudakis. All rights reserved.
        </div>
    </footer>
</body>
</html>