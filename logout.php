<?php
/**
 * Logout of the application.
 * Session variables will be removed
 * Session will be finally destroyed.
 */

// Verify that a session exists - someone is still logged in.
session_start();
 
// Unset all of the session variables.
session_unset();
 
// Destroy the session.
session_destroy();
 
// Redirect to home page
header("location: index.php");

exit;

?>