<?php
/**
* Edit a single user
*/

// User authentication verification
include("helpers/auth.php");
$addUserStatus = false;

// this is a temporary password
$temppassword = '';
$showUpdateNotification = false;

if (!empty($isAdmin)){
  if (!$isAdmin){
      header("location: index.php");
      exit();
  }
}else{
    header("location: index.php");
      exit();
}

$firstnameHasError = '';
$haserror = false;
$backToHomeLink = "http://" . $_SERVER['HTTP_HOST'].'/index.php';
$notificationText = 'An error occurred, please try again later. Click <a class="btn btn-danger" href="'.$backToHomeLink.'">here</a> to return to the app home.';

// Retrieving user data from DB
if($_SERVER["REQUEST_METHOD"] == "GET"){

    $userProfile = '';
    $haserror = true;

    // get query params for the editing user
    if (isset($_GET["updated"])){
        $updated = trim(mysqli_real_escape_string($dbconn,$_GET["updated"]));
        $showUpdateNotification = true;
    }

    // get query params for the editing user
    if (isset($_GET["id"])){

        $userid = trim(mysqli_real_escape_string($dbconn,$_GET["id"]));
        $returnedInformation = getUser($userid,$dbconn);

        if ($returnedInformation['haserror'] != 1){
            $haserror = false;
            $userProfile = $returnedInformation['result'];

            $id = $userProfile['id'];
            $email = $userProfile['email'];
            $lastname = $userProfile['lastname'];
            $firstname = $userProfile['firstname'];
            $password = $userProfile['password'];
            $type = $userProfile['type'] == 1 ? 'admin' : 'employee';
        }
     
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $haserror = true;

    // escape strings from POST params
    $type = trim(mysqli_real_escape_string($dbconn,$_POST["type"]));
    $firstname = trim(mysqli_real_escape_string($dbconn,$_POST["firstname"]));
    $lastname = trim(mysqli_real_escape_string($dbconn,$_POST["lastname"]));
    $email = trim(mysqli_real_escape_string($dbconn,$_POST["email"]));
    $temppassword = '';
    
    if (isset($_POST["temppassword"]))
        $temppassword = trim(mysqli_real_escape_string($dbconn,$_POST["temppassword"]));

    // first get user data based on id
    // get query params for the editing user
    if (isset($_POST["id"])){
        $userid = trim(mysqli_real_escape_string($dbconn,$_POST["id"]));
        $returnedInformation = getUser($userid,$dbconn);
  
        if ($returnedInformation['haserror'] != 1){
            $haserror = false;
            $userProfile = $returnedInformation['result'];
        }
    }

    if (!$haserror){

        // Server based validity checks for inputs
        if (empty($firstname)){
            $firstnameHasError = "Please enter user's firstname.";
        }

        if (empty($lastname)){
            $lastnameHasError = "Please enter user's lastname.";
        }
    
        if (empty($email)){
            $emailHasError = "Please enter the user's email.";
            $email = ""; //TODO
        }
        else{
            if (strcmp($userProfile['email'],$email)){
                // check if email - username already exists
                if (!usernameExists($email,$dbconn)){
                    /*$email = trim($_POST["email"]);*/
                }
                else{
                    $emailHasError = "The email already exists.";
                }
            }
        }

        // if firstname, lastname and email (username) are valid, proceed to password validation
        if (empty($lastnameHasError) && empty($firstnameHasError) && empty($emailHasError)){
            
            // Validate the password
            if(empty($temppassword)){
                // if empty do not update password.
                $newpassword = '';
                
            } elseif ((strlen($temppassword)) < 6){
                $temppasswordHasError = "Password must have at least 6 characters.";
            } else{
                // temppassword is correct
                $newpassword = $temppassword;
            }

            // Check input errors before inserting in database
            if(empty($emailHasError) && empty($temppasswordHasError)){
            
                // Update user to db
                $returnedInformation = updateUser($dbconn,$firstname,$lastname,$email,$type,$newpassword,$userid);
        
                if (!$returnedInformation['haserror']){
                    $showNotification = true;
                    header("location: edituser.php?id=".$userid."&updated=1");
                }
                else{
                    $haserror = true;
                }
            }
        
        }/*else{
            header("location: edituser.php?id=".$userid);
        }*/
    }
}

?>

<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <!-- Add the main navbar -->
    <?php include 'includes/navbar.php';?>

    <div class="container ">

        <!-- If there is an error then notify user -->
        <?php if ($haserror) : ?>
            <div class="alert alert-danger" role="alert" style="margin-top:1rem">
                <strong>Message: </strong><span style="padding-left:1rem;"><?php echo $notificationText; ?></span>
            </div>
        <?php else : { ?>
    

          <div class="content-container">
            <?php if ($showUpdateNotification) : ?>
              <div class="alert alert-success" role="alert">
                <strong>Success: </strong>User successfully updated. You can return to the <a href="user.php" class="alert-link">user list</a> page. 
              </div>
              <?php endif; ?>
              <h4>Edit user</h4>
              <small class="text-muted">
                  All the form fields are required.
              </small>
          </div>

          <div class="form-container">
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="form-group row">
                      <div>
                          <input type="hidden" name="id" value="<?php echo $userid; ?>" >
                      </div>
                      <div class="col-6">
                          <label>First Name</label>
                          <input type="text" name="firstname" value="<?php echo $firstname; ?>" class="form-control <?php echo (!empty($firstnameHasError)) ? 'is-invalid' : ''; ?>" required>
                          <small class="text-muted haserror">
                              <?php echo (!empty($firstnameHasError)) ? ($firstnameHasError) : ''; ?>
                          </small>
                      </div>
                      <div class="col-6">
                          <label>Last Name</label>
                          <input type="text" name="lastname" value="<?php echo $lastname; ?>" class="form-control <?php echo (!empty($lastnameHasError)) ? 'is-invalid' : ''; ?>" required>
                          <small class="text-muted haserror">
                              <?php echo (!empty($lastnameHasError)) ? ($lastnameHasError) : ''; ?>
                          </small>
                      </div>
                  </div>
                  <div class="form-group row">
                      <div class="col-8">
                          <label>Email (Username)</label>
                          <input type="email" name="email" class="form-control <?php echo (!empty($emailHasError)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                          <small class="text-muted haserror">
                              <?php echo (!empty($emailHasError)) ? ($emailHasError) : ''; ?>
                          </small>
                      </div>
                      <div class="col-4">
                          <label>Account Type</label>
                          <select id="accounttype" name="type" class="form-control btn btn-secondary">
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                          </select>
                      </div>
                  </div>
                  <div class="form-group row">
                      <div class="col-6">
                          <label>Password</label>
                          <input id="temppassword-input" type="password" name="temppassword"  class="form-control <?php echo (!empty($temppasswordHasError)) ? 'is-invalid' : ''; ?>" disabled>
                          <small class="text-muted haserror">
                              <?php echo (!empty($temppasswordHasError)) ? ($temppasswordHasError) : ''; ?>
                          </small>
                          <div class="form-check ">
                            <input type="checkbox" class="form-check-input" id="changepassword">
                            <label class="form-check-label" for="changepassword">Click to enter a new password.</label>
                            <small class="text-muted"><p>You should enter something otherwise the default password will be used.</p></small>
                          </div>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col" style="display: flex; justify-content: flex-end">
                          <input id="submit" type="submit" class="btn btn-lg btn-primary" value="Update">
                      </div>
                  </div>
              
              </form>
          </div>
      
        <?php } endif; ?>
      </div>


</body>

    <!-- Add footer -->
    <?php include 'includes/footer.php';?>

    <script>
        /* Change the password by clicking the checkbox */
        $(document).ready(function(){

            // Change the account type based on stored value
            document.querySelector('#accounttype').value = <?php echo "'".$type."'"; ?>;

            $('#changepassword').change(function() {

                if ($(this).prop('checked')){
                    document.getElementById('temppassword-input').disabled = false;
                    document.getElementById('temppassword-input').value = '';

                }
                else
                    document.getElementById('temppassword-input').disabled = true;
            })
        });

    </script>

</html>

