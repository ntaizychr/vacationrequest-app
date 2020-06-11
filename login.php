<?php
/**
 * Login page
 */

// Include database config file
require_once "config/dbconfig.php";
require_once "helpers/functions.php";
 
// Define form variables and initialize with empty values
$email = $password = $passwordHasError = $emailHasError = "" ;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Get email and password from request params after escaping
    $email = mysqli_real_escape_string($dbconn,$_POST["email"]);
    $password = mysqli_real_escape_string($dbconn,$_POST["password"]);

    // Verify that email field is not empty
    if(empty($email)){
        $emailHasError = "Email is required.";
        $email = "";
    }
    
    // Verify that password field is not empty
    if(empty($password)){
        $passwordHasError = "Password is required.";
        $password = "";
    }

     // Validate credentials
     if(empty($emailHasError) && empty($passwordHasError)){

        $returnedInformation = userEmailExists($dbconn,$email);

        // an SQL error maybe
        if (!$returnedInformation['haserror']){

          if ($returnedInformation['emailfound']){

            // check if password is OK
            if(password_verify($password, $returnedInformation['storedpassword'])) {
              // Password is correct, so start a new session
              session_start();
            
              // Store data in session variables
              $_SESSION["loggedin"] = true;
              $_SESSION["id"] = $returnedInformation['storedid'];
              $_SESSION["username"] = $email;                        
              $_SESSION["firstname"] = $returnedInformation['storedfirstname'];
            
              // Redirect user back to index page - now session exists
              header("location: index.php");
            } else{
              // Display an error message if password is not valid
              $passwordHasError = "The password you entered is not valid.";
              $password = "";
            }
          }
          else{
               // Display an error message if username doesn't exist
               $emailHasError = "No account found with that email.";
               $password = "";
          }
          
        }
        else{
          echo "Oops! Something went wrong. Please try again later.";
        }

       
    }

    // Close connection
    $dbconn->close();
}
?>
 
<!-- Add header -->
<?php include 'includes/header.php';?>

<body>

    <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-4 d-none d-lg-block bg-login-image" style="background-color:lightblue"></div>
              <div class="col-lg-8">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Log In</h1>
                  </div>
                  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" >
                    <div class="form-group">
                        <input placeholder="Email" type="email" name="email" value="<?php echo $email; ?>" class="form-control <?php echo (!empty($emailHasError)) ? 'is-invalid' : ''; ?>" required>
                        <small class="text-muted haserror">
                            <?php echo (!empty($emailHasError)) ? ($emailHasError) : ''; ?>
                        </small>
                    </div> 
                    <div class="form-group">
                        <input placeholder="Password" type="password" name="password" value="<?php echo $email; ?>" class="form-control <?php echo (!empty($passwordHasError)) ? 'is-invalid' : ''; ?>" required>
                        <small class="text-muted haserror">
                            <?php echo (!empty($passwordHasError)) ? ($passwordHasError) : ''; ?>
                        </small>
                    </div>
                    <hr>
                    <div class="form-group">
                        <input type="submit" name="user_login" class="btn btn-primary btn-block" value="Login">
                    </div>
                  </form>
                  
                  <!--
                  <div class="text-center">
                    <a class="small" href="register.html">Create an Account!</a>
                  </div>
                    -->
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

</body>
</html>