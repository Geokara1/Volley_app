<?php
// logout.php
// Καταστρέφει το session και επιστρέφει στην αρχική

session_start();
session_unset();    // αδειάζει όλες τις session μεταβλητές
session_destroy();  // διαγράφει το session από τον server

header('Location: /Volley_app/FrontEnd/index.php');
exit;
?>