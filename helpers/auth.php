<?php
/*
* The user Authentication page
*/

// User session is required
session_start();

$isAdmin = '';

// Check if the user session exists and user is logged in - if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit();
}
else{
    require_once ("config/dbconfig.php");
    require_once ("helpers/functions.php");

    $isAdmin = isAdmin($_SESSION["id"],$dbconn);
}

?>