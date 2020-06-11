<?php
/**
 * Users list page
 */

// User authentication verification
include("helpers/auth.php");

$notificationText = "";
$haserror = false;

// Fetch user list
if ($isAdmin){
    
    $returnedInformation = getUserList($dbconn);

    // If there is an error
    if ($returnedInformation['haserror']){
        $haserror = true;
        $notificationText = 'An error occurred, please try again later.';
    }
    else{
        $userList = $returnedInformation['result'];

        if (empty($userList)){
            $notificationText = "There are no users at this time." ;    
        }
    }
    
}

?>
 
<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <!-- Add the main navbar -->
    <?php include 'includes/navbar.php';?>

    <!-- User List View -->
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
                <h4 style="float:left;">User Accounts</h4>
            </div>
            <div class="col-2">
                <a type="button" href="adduser.php" class="btn btn-outline-primary" style="float:right;">Create</a>
            </div>
        </div>

        <!-- 2. User list table -->
        <?php if (empty($userList)) : ?>
            <div class="row alert alert-info" role="alert" style="margin-top:1rem">
                <strong>Information: </strong><span style="padding-left:1rem;"><?php echo $notificationText; ?></span>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table id="userlist-table" class="table table-striped table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($userList as $i => $row) : ?>
                        
                        <tr onclick="document.location='<?php echo $row['edit'];?>'">
                            
                            <td><?php echo $row['firstname']; ?></td>
                            <td><?php echo $row['lastname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['type']; ?></td>

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