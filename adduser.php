<?php
/**
 * Add user
 */

// User authentication verification
include("helpers/auth.php");

if (!empty($isAdmin)){
  if (!$isAdmin){
      header("location: index.php");
      exit();
  }
}else{
    header("location: index.php");
      exit();
}

// Define variables and initialize with empty values
$type = 'employee';
$haserror = false;
$backToHomeLink = "http://" . $_SERVER['HTTP_HOST'].'/index.php';
$notificationText = 'An error occurred, please try again later. Click <a class="btn btn-danger" href="'.$backToHomeLink.'">here</a> to return to the app home.';
$firstname = $lastname = $email = $password = $confirm_password = "";
$lastnameHasError = $firstnameHasError = $emailHasError = "";
$usernameHasError = $passwordHasError = $confirmpasswordHasError = ""; 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // escape strings from POST params
    $type = trim(mysqli_real_escape_string($dbconn,$_POST["type"]));
    $firstname = trim(mysqli_real_escape_string($dbconn,$_POST["firstname"]));
    $lastname = trim(mysqli_real_escape_string($dbconn,$_POST["lastname"]));
    $email = trim(mysqli_real_escape_string($dbconn,$_POST["email"]));
    $password = trim(mysqli_real_escape_string($dbconn,$_POST["password"]));
    $confirm_password = trim(mysqli_real_escape_string($dbconn,$_POST["confirm_password"]));

    // Server - based validity checks for inputs

    // Firstname validation
    if (empty($firstname)){
        $firstnameHasError = "User's Firstname cannot be empty.";
    }

    // Lastname validation
    if (empty($lastname)){
        $lastnameHasError = "User's Lastname cannot be empty.";
    }
 
    // Email validation
    if (empty($email)){
        $emailHasError = "User's Email cannot be empty.";
        $email = ""; //TODO
    }
    else{
        // check if email - username already exists
        if (usernameExists($email,$dbconn)){
            $emailHasError = "The email already exists.";
        }
    }

    // if firstname, lastname and email (username) are valid, proceed to password validation
    if (empty($lastnameHasError) && empty($firstnameHasError) && empty($emailHasError)){
        
        // Validate the password
        if(empty($password)){
            $passwordHasError = "Please enter a password.";     
        } elseif(strlen($password) < 6){
            $passwordHasError = "Password must have at least 6 characters.";
        }
        else
        {
            // Validate the confirm password
            if(empty($confirm_password)){
                $confirmpasswordHasError = "Please confirm the password.";     
            } else{
                if(empty($passwordHasError) && ($password != $confirm_password)){
                    $confirmpasswordHasError = "Password did not match.";
                }
            }
        }

        // Check input errors before inserting in database
        if(empty($emailHasError) && empty($passwordHasError) && empty($confirmpasswordHasError)){
        
            // Add user to db
            $returnedInformation = insertUser($dbconn,$firstname,$lastname,$email,$type,$password);

            // If there is an error
            if ($returnedInformation['haserror']){
                $haserror = true;
            }
            else{
                header("location: user.php");
            }
    
        }
       
    }    
    
    // Close the db connection
    //$dbconn->close();
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
            <h4>Add a new user account</h4>
            <small class="text-muted">
                All the form fields are required.
            </small>
        </div>

        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group row">
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
                        <label>Email</label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($emailHasError)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                        <small class="text-muted haserror" >
                            <?php echo (!empty($emailHasError)) ? ($emailHasError) : ''; ?>
                        </small>
                    </div>
                    <div class="col-4">
                        <label>Account Type</label>
                        <select name="type" class="form-control btn btn-secondary">
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label>Password  <small class="text-muted">(Must have at least 6 characters)</small></label>
                        <input type="password" name="password" value="<?php echo $password; ?>" class="form-control <?php echo (!empty($passwordHasError)) ? 'is-invalid' : ''; ?>" required>
                        <small class="text-muted haserror">
                            <?php echo (!empty($passwordHasError)) ? ($passwordHasError) : ''; ?>
                        </small>
                    </div>
                    <div class="col-6">
                    <label>Confirm Password</label>
                        <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>" class="form-control <?php echo (!empty($confirmpasswordHasError)) ? 'is-invalid' : ''; ?>" required>
                        <small class="text-muted haserror">
                            <?php echo (!empty($confirmpasswordHasError)) ? ($confirmpasswordHasError) : ''; ?>
                        </small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col" style="display: flex; justify-content: flex-end">
                        <input id="submit" type="submit" class="btn btn-lg btn-primary" value="Submit">
                        
                    </div>
                </div>
            
            </form>
        </div>
        <?php } endif; ?>
    </div>
</body>
</html>