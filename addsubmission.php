<?php
/**
 * Add submission
 */

// User authentication verification
include_once ("helpers/auth.php");

$reasonHasError = $datefromHasError = "" ;
$haserror = false;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // escape strings from POST params
    $reason = mysqli_real_escape_string($dbconn,$_POST["reason"]);
    $datefrom = mysqli_real_escape_string($dbconn,$_POST["datefrom"]); //string
    $dateto = mysqli_real_escape_string($dbconn,$_POST["dateto"]); // string
    $userid = mysqli_real_escape_string($dbconn,$_SESSION["id"]);
    $datefrom_int = strtotime($datefrom);
    $dateto_int = strtotime($dateto);

    // Get today as date
    $today = date("Y-m-d"); // string

    // Check for any validation errors (empty, non valid date etc) 
    if(empty($reason)){
        $reasonHasError = "Please enter a reason.";
        $reasonHasError = "";
    }

    // Check if dates is valid (not before today and valid days)
    if (($datefrom < $dateto) && ($today < $datefrom)) {
       
        // get number of days between them (ignore weekend)
       $days = getNumOfWorkingDays($datefrom_int,$dateto_int);

    }else{
        if ($datefrom >= $dateto){
            $datefromHasError = "You cannot select a from date before/equals to the end date.";
        }
        if ($today >= $datefrom){
            $datefromHasError = "You cannot select a from day before today or today.";
        }
    }

    // Check input errors before inserting in database
    if(empty($reasonHasError) && empty($datefromHasError)){

        $returnedInformation = addSubmission($dbconn,$userid,$datefrom,$dateto,$days,$reason);

        if ($returnedInformation['haserror']){
            $notificationText = 'An error occurred, please try again later.';
            $haserror = true;
            exit();
        }
        else{
            // get administrator list (we just get one)
            $administratorEmail = getAdministratorEmails ($dbconn);
            $userEmail = getUserEmail($dbconn,$userid);
            $userFullName = getUserFullName($dbconn,$userid);
            $submissionid = $returnedInformation['newid'];

            // send email to administrator
            sendSubmissionEmail($userFullName,$userEmail,$datefrom,$dateto,$reason,$userid,$submissionid,$administratorEmail);

             // Redirect to the submissions page
            header("location: submission.php");
        }
        
        
    }

}
?>

<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <!-- Add the main navbar -->
    <?php include 'includes/navbar.php';?>

    <div class="container ">

        <div class="content-container">
            <h4>Add a new holiday request</h4>
            <small class="text-muted">
                All the form fields are required.
            </small>
        </div>

        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group row">
                    <div class="col-6">
                        <label>From Date</label>
                        <input type="date" class="form-control <?php echo (!empty($datefromHasError)) ? 'is-invalid' : ''; ?>" name="datefrom" required>
                        <small class="text-muted">
                            <?php echo (!empty($datefromHasError)) ? ($datefromHasError) : ''; ?>
                        </small>
                    </div>
                    <div class="col-6">
                        <label>To Date</label>
                        <input type="date" class="form-control" name="dateto" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <label>Reason</label>
                        <textarea class="form-control <?php echo (!empty($reasonHasError)) ? 'is-invalid' : ''; ?>" rows="4" name="reason" placeholder="I will get married ..."  required ></textarea>
                        <small class="text-muted">
                            <?php echo (!empty($reasonHasError)) ? ($reasonHasError) : ''; ?>
                        </small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col" style="display: flex; justify-content: flex-end">
                        <input id="submit-button" type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </div>
            
            </form>
        </div>
    
    </div>


        </body>
</html>

