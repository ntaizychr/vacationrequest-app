<?php

// User authentication verification
include("helpers/auth.php");

$notificationText = '';
$haserror = false;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if (isset($_GET["approve"]) && (isset($_GET["id"]))){
        $id = trim(mysqli_real_escape_string($dbconn,$_GET["id"]));
        $status = trim(mysqli_real_escape_string($dbconn,$_GET["approve"]));

        if (isset($_SESSION["id"])){
            $supervisorId = trim($_SESSION["id"]);
        }
    }
    else{
        $notificationText = "Something bad happened, please try again later.";
        $haserror = true;
        exit();
    }

    // get the update status and error
    $returnedInformation = updateSubmission($dbconn,$id,$status);
    $backToHomeLink = "http://" . $_SERVER['HTTP_HOST'].'/index.php';

    if ($returnedInformation['haserror']){
        $notificationText = 'An error occurred, please try again later.';
        $haserror = true;
    }
    else{
        // if 1 then submission status updated.
        if ($returnedInformation['status']){

            $notificationText = 'Vacation request sucessfully updated. Click <a class="btn btn-info" href="'.$backToHomeLink.'">here</a> to return to the app.';
            $supervisorEmail = getUserEmail($dbconn,$supervisorId);

            // get user profile to send email
            $returnedUserInformation = getUser($id,$dbconn);
            if (!$returnedUserInformation['haserror']){
                $userProfile = $returnedUserInformation['result'];

                if (!empty($userProfile)){

                    //send email to user to notify for the submission update.
                    sendUserEmail($userProfile['email'],$supervisorEmail,$status);
                }    
            }
        }
        else
            $notificationText = 'Vacation request has already been submitted. Click <a class="btn btn-info" href="'.$backToHomeLink.'">here</a> to return to the app.';
    }
}

?>

<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <div class="container">
        <?php if (!$haserror) : ?>
            <div class="alert alert-info" role="alert">
                <strong>Message: </strong><?php echo $notificationText; ?>
            </div>
        <?php elseif ($haserror) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Message: </strong><?php echo $notificationText; ?>
            </div>
        <?php endif; ?>
    </div>

</body>