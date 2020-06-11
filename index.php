<?php
// Initialize the session
//session_start();

/*if(!isset($_GET["logout"]) || $_GET["logout"] !== true){
    header("location: login.php");
    exit;
}*/

// User authentication verification - will redirect user to login.php
include("helpers/auth.php");

if (isset($_SESSION['loggedin'])){
    if (!$isAdmin){
        header("location: submission.php");
        
    }
    else{
        header("location: user.php"); 
    }
}

?>
 
 <!-- Add header -->
 <?php include 'includes/header.php';?>

 

<body>

  
    
</body>
</html>