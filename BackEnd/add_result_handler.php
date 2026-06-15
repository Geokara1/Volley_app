<?php

session_start();
require_once 'session_check.php';
require_once 'db.php';
requireRole('referee');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Volley_app/FrontEnd/add_result.php');
    exit;
}


$matchId = intval($_POST['match_id'] ?? 0);

if ($matchId <= 0) {
    $_SESSION['result_error'] = 'Παρακαλώ επιλέξτε αγώνα.';
    header('Location: /Volley_app/FrontEnd/add_result.php');
    exit;
}


$checkStmt = mysqli_prepare($conn, "SELECT id, status FROM match_result WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, "i", $matchId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$match = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

if (!$match) {
    $_SESSION['result_error'] = 'Ο αγώνας δεν βρέθηκε.';
    header('Location: /Volley_app/FrontEnd/add_result.php');
    exit;
}
if ($match['status'] !== 'unplayed') {
    $_SESSION['result_error'] = 'Αυτός ο αγώνας έχει ήδη καταχωρηθεί αποτέλεσμα.';
    header('Location: /Volley_app/FrontEnd/add_result.php');
    exit;
}

$homeSets = 0;
$awaySets = 0;

for ($i = 1; $i <= 5; $i++) {
    $sh = $_POST["set{$i}Home"] ?? '';
    $sa = $_POST["set{$i}Away"] ?? '';

    if ($sh === '' || $sa === '') continue;

    $sh = intval($sh);
    $sa = intval($sa);

    if ($sh > $sa) $homeSets++;
    elseif ($sa > $sh) $awaySets++;
  
}

if ($homeSets !== 3 && $awaySets !== 3) {
    $_SESSION['result_error'] = 'Μη έγκυρο αποτέλεσμα. Μία ομάδα πρέπει να κερδίσει ακριβώς 3 σετ (3-0, 3-1 ή 3-2).';
    header('Location: /Volley_app/FrontEnd/add_result.php?match_id=' . $matchId);
    exit;
}

$sheetPath = '';

if (isset($_FILES['matchSheet']) && $_FILES['matchSheet']['error'] === 0) {
    $uploadDir = __DIR__ . '/uploads/matchsheets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['matchSheet']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $_SESSION['result_error'] = 'Το φύλλο αγώνα πρέπει να είναι αρχείο PDF.';
        header('Location: /Volley_app/FrontEnd/add_result.php?match_id=' . $matchId);
        exit;
    }

    $filename = 'sheet_match' . $matchId . '_' . time() . '.pdf';
    move_uploaded_file($_FILES['matchSheet']['tmp_name'], $uploadDir . $filename);
    $sheetPath = 'uploads/matchsheets/' . $filename;
}

$refereeId = $_SESSION['user_id'];
$now       = date('Y-m-d H:i:s');

$updateStmt = mysqli_prepare($conn, "
    UPDATE match_result
    SET home_sets  = ?,
        away_sets  = ?,
        status     = 'pending',
        match_sheet = ?,
        referee_id = ?,
        played_at  = ?
    WHERE id = ?
");

mysqli_stmt_bind_param($updateStmt, "iisisi",
    $homeSets, $awaySets, $sheetPath, $refereeId, $now, $matchId
);

if (mysqli_stmt_execute($updateStmt)) {
    $_SESSION['result_success'] = 'Το αποτέλεσμα (' . $homeSets . '-' . $awaySets . ') καταχωρήθηκε και αναμένει επικύρωση από τον διαχειριστή.';
    mysqli_stmt_close($updateStmt);
    mysqli_close($conn);
    header('Location: /Volley_app/FrontEnd/matches.php');
} else {
    $_SESSION['result_error'] = 'Σφάλμα κατά την αποθήκευση. Προσπαθήστε ξανά.';
    mysqli_stmt_close($updateStmt);
    mysqli_close($conn);
    header('Location: /Volley_app/FrontEnd/add_result.php?match_id=' . $matchId);
}
exit;
?>