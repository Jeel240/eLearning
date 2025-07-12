<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Redirect to instructor login page
header("Location: instructor_login.php");
exit();
?>
