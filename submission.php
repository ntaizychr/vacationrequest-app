<?php
/*
*
*/

// User authentication verification
include("helpers/auth.php");

$notificationText = "";
$haserror = false;

$returnedInformation = getSubmissionList($_SESSION["id"],$dbconn);

// If there is an error
if ($returnedInformation['haserror']){
    $haserror = true;
    $notificationText = 'An error occurred, please try again later.';
}
else{
    $submissionList = $returnedInformation['result'];

    if (empty($submissionList)){
        $notificationText = "There are no submissions at this time." ;    
    }
}

?>
 
<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <!-- Add the main navbar -->
    <?php include 'includes/navbar.php';?>

    <!-- Submission List View -->
    <div class="container">

        <!-- If there is an error then notify user -->
        <?php if ($haserror) : ?>
            <div class="alert alert-danger" role="alert" style="margin-top:1rem">
                <strong>Message: </strong><span style="padding-left:1rem;"><?php echo $notificationText; ?></span>
            </div>
        <?php else : { ?>

        <!-- 1. Action row -->
        <div class="content-container row">
            <div class="col-10">
                <h4 style="float:left;">Vacation Request Submissions</h4>
            </div>
            <div class="col-2">
                <a type="button" href="addsubmission.php" class="btn btn-outline-primary" style="float:right;">Add</a>
            </div>
        </div>

        <!-- 2. Submission list table -->
        <?php if (empty($submissionList)) : ?>
            <div class="row alert alert-info" role="alert" style="margin-top:1rem">
                <strong>Information: </strong><span style="padding-left:1rem;"><?php echo $notificationText; ?></span>
            </div>
        <?php else : ?>
            <div class="table-responsive table-container">
                <table class="table table-striped table-hover" id="submissionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Submission Date</th>
                            <th>Date Start</th>
                            <th>Date End</th>
                            <th>Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($submissionList as $i => $row) : ?>
                        <tr>
                            <td>
                                <strong><?php echo $row['datesubmitted']; ?></strong>
                            </td>
                            <td><?php echo $row['datestart']; ?></td>
                            <td><?php echo $row['dateend']; ?></td>
                            <td><?php echo $row['days']; ?></td>
                            <td>
                                <?php if ($row['status'] == 1) : ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php elseif ($row['status'] == 2) : ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php else : ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php } endif; ?>
        
    </div>

</body>
</html>