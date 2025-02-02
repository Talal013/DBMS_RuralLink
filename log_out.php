<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect the user to the login page
header("Location: login.php");
exit();
?>
<?php
session_start();
session_unset();
session_destroy();

// Clear browser cache
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to login
header("Location: login.php");
exit();
?>
