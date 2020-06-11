<?php

// Configuration for DB
define('DB_HOST', 'yourhost');
define('DB_USER', 'youruser');
define('DB_PASS', 'yourpass');
define('DB_NAME', 'yourdbname');
 
/* Connect to MySQL database - we are using mysqli for the prepared statements  */
$dbconn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
// Check the DB connection
if($dbconn->connect_errno){
    echo "Error: Failed to connect to MySQL." .  $dbconn->connect_error;
    exit('Exiting');
}

?>