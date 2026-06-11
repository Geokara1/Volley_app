<?php
// api.php — REST API για τους συλλόγους
// Endpoints:
//   GET    api.php/clubs      → όλοι οι σύλλογοι
//   GET    api.php/clubs/{id} → ένας σύλλογος + παίκτες
//   POST   api.php/clubs      → προσθήκη νέου συλλόγου
//   PUT    api.php/clubs/{id} → επεξεργασία συλλόγου
//   DELETE api.php/clubs/{id} → διαγραφή συλλόγου

session_start();
require_once 'db.php';

// Κάθε απάντηση είναι JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS request (preflight για CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function respondError(string $message, int $code = 400): void {
    respond(['status' => 'error', 'message' => $message], $code);
}

function requireAuth(string $role = ''): void {
    if (empty($_SESSION['logged_in'])) {
        respondError('Απαιτείται σύνδεση.', 401);
    }
    if ($role && $_SESSION['role'] !== $role && $_SESSION['role'] !== 'admin') {
        respondError('Δεν έχετε δικαίωμα για αυτή την ενέργεια.', 403);
    }
}

// ─── PARSE URL ────────────────────────────────────────────────────────────────
// api.php/clubs/3  →  PATH_INFO = /clubs/3
$pathInfo = $_SERVER['PATH_INFO'] ?? '/';
$segments = explode('/', trim($pathInfo, '/'));

$resource = $segments[0] ?? '';
$id       = (isset($segments[1]) && is_numeric($segments[1]))
            ? intval($segments[1])
            : null;

// Μόνο το 'clubs' resource υποστηρίζεται
if ($resource !== 'clubs') {
    respondError('Resource not found. Χρησιμοποίησε /api.php/clubs', 404);
}

// ─── ROUTING ──────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ── GET ───────────────────────────────────────────────────────────────────
    case 'GET':
        if ($id !== null) {
            getClub($id);
        } else {
            getAllClubs();
        }
        break;

    // ── POST ──────────────────────────────────────────────────────────────────
    case 'POST':
        requireAuth('club_admin');
        createClub();
        break;

    // ── PUT ───────────────────────────────────────────────────────────────────
    case 'PUT':
        requireAuth('club_admin');
        if ($id === null) respondError('Απαιτείται ID. π.χ. api.php/clubs/3', 400);
        updateClub($id);
        break;

    // ── DELETE ────────────────────────────────────────────────────────────────
    case 'DELETE':
        requireAuth('club_admin');
        if ($id === null) respondError('Απαιτείται ID. π.χ. api.php/clubs/3', 400);
        deleteClub($id);
        break;

    default:
        respondError('Method not allowed', 405);
}

// ─── GET ALL CLUBS ─────────────────────────────────────────────────────────
function getAllClubs(): void {
    global $conn;

    $result = mysqli_query($conn,
        "SELECT id, team_name, team_site, team_logo, team_video, coach_name
         FROM team_profile
         ORDER BY team_name"
    );

    $clubs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clubs[] = $row;
    }

    respond([
        'status' => 'success',
        'count'  => count($clubs),
        'data'   => $clubs
    ]);
}

// ─── GET CLUB BY ID ───────────────────────────────────────────────────────────
function getClub(int $id): void {
    global $conn;

    // Fetch club
    $stmt = mysqli_prepare($conn, "SELECT * FROM team_profile WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $club = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$club) {
        respondError("Ο σύλλογος με id=$id δεν βρέθηκε.", 404);
    }

    // Fetch players
    $pStmt = mysqli_prepare($conn,
        "SELECT id, jersey_number, full_name, position, height, date_of_birth
         FROM player WHERE team_id = ? ORDER BY jersey_number"
    );
    mysqli_stmt_bind_param($pStmt, "i", $id);
    mysqli_stmt_execute($pStmt);
    $pResult = mysqli_stmt_get_result($pStmt);

    $players = [];
    while ($p = mysqli_fetch_assoc($pResult)) {
        $players[] = $p;
    }
    mysqli_stmt_close($pStmt);

    $club['players'] = $players;

    respond(['status' => 'success', 'data' => $club]);
}

// ─── CREATE CLUB (POST) ───────────────────────────────────────────────────────
function createClub(): void {
    global $conn;

    // Διάβασε JSON body
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $teamName  = trim($input['team_name']  ?? '');
    $teamSite  = trim($input['team_site']  ?? '');
    $teamVideo = trim($input['team_video'] ?? '');
    $coachName = trim($input['coach_name'] ?? '');
    $adminId   = $_SESSION['user_id'];

    if (empty($teamName) || empty($coachName)) {
        respondError('Τα πεδία team_name και coach_name είναι υποχρεωτικά.', 400);
    }

    // Έλεγχος: έχει ήδη σύλλογο αυτός ο admin;
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM team_profile WHERE admin_id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $adminId);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        mysqli_stmt_close($checkStmt);
        respondError('Έχεις ήδη καταχωρήσει σύλλογο.', 409);
    }
    mysqli_stmt_close($checkStmt);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO team_profile (team_name, team_site, team_video, coach_name, admin_id)
         VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssi",
        $teamName, $teamSite, $teamVideo, $coachName, $adminId
    );

    if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        respond([
            'status'  => 'success',
            'message' => 'Ο σύλλογος δημιουργήθηκε.',
            'id'      => $newId
        ], 201);
    } else {
        respondError('Σφάλμα κατά την αποθήκευση.', 500);
    }
}

// ─── UPDATE CLUB (PUT) ────────────────────────────────────────────────────────
function updateClub(int $id): void {
    global $conn;

    // Έλεγχος: υπάρχει ο σύλλογος;
    $checkStmt = mysqli_prepare($conn, "SELECT admin_id FROM team_profile WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $id);
    mysqli_stmt_execute($checkStmt);
    $club = mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt));
    mysqli_stmt_close($checkStmt);

    if (!$club) {
        respondError("Ο σύλλογος με id=$id δεν βρέθηκε.", 404);
    }

    // Μόνο ο club_admin του συλλόγου ή admin μπορεί να επεξεργαστεί
    if ($_SESSION['role'] === 'club_admin' && $club['admin_id'] != $_SESSION['user_id']) {
        respondError('Δεν έχεις δικαίωμα να επεξεργαστείς αυτόν τον σύλλογο.', 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $teamName  = trim($input['team_name']  ?? '');
    $teamSite  = trim($input['team_site']  ?? '');
    $teamVideo = trim($input['team_video'] ?? '');
    $coachName = trim($input['coach_name'] ?? '');

    if (empty($teamName) || empty($coachName)) {
        respondError('Τα πεδία team_name και coach_name είναι υποχρεωτικά.', 400);
    }

    $stmt = mysqli_prepare($conn,
        "UPDATE team_profile
         SET team_name=?, team_site=?, team_video=?, coach_name=?
         WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssssi",
        $teamName, $teamSite, $teamVideo, $coachName, $id
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        respond(['status' => 'success', 'message' => 'Ο σύλλογος ενημερώθηκε.']);
    } else {
        respondError('Σφάλμα κατά την ενημέρωση.', 500);
    }
}

// ─── DELETE CLUB (DELETE) ─────────────────────────────────────────────────────
function deleteClub(int $id): void {
    global $conn;

    $checkStmt = mysqli_prepare($conn, "SELECT admin_id FROM team_profile WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $id);
    mysqli_stmt_execute($checkStmt);
    $club = mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt));
    mysqli_stmt_close($checkStmt);

    if (!$club) {
        respondError("Ο σύλλογος με id=$id δεν βρέθηκε.", 404);
    }

    if ($_SESSION['role'] === 'club_admin' && $club['admin_id'] != $_SESSION['user_id']) {
        respondError('Δεν έχεις δικαίωμα να διαγράψεις αυτόν τον σύλλογο.', 403);
    }

    // Διαγραφή παικτών πρώτα (foreign key)
    $delPlayers = mysqli_prepare($conn, "DELETE FROM player WHERE team_id = ?");
    mysqli_stmt_bind_param($delPlayers, "i", $id);
    mysqli_stmt_execute($delPlayers);
    mysqli_stmt_close($delPlayers);

    // Διαγραφή συλλόγου
    $stmt = mysqli_prepare($conn, "DELETE FROM team_profile WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        respond(['status' => 'success', 'message' => "Ο σύλλογος id=$id διαγράφηκε."]);
    } else {
        respondError('Σφάλμα κατά τη διαγραφή.', 500);
    }
}
?>