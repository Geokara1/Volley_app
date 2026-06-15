<?php
session_start();
session_unset();    
session_destroy(); 

header('Location: /Volley_app/FrontEnd/index.php');
exit;
?>