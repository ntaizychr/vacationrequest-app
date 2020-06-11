<?php
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">CompanyName</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">    
            <!-- add any actions for both employee and admin -->
            <?php if ($isAdmin) : ?>
                <li class="nav-item active">
                    <a class="nav-link" href="user.php">Accounts <span class="sr-only">(current)</span></a>
                </li>
            <?php else : ?>
                <li class="nav-item active">
                    <a class="nav-link" href="submission.php">Submissions <span class="sr-only"></span></a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="my-2 my-lg-0">
            <span class="navbar-text" style="margin-right:20px!important;">
                Hi, <b><?php echo htmlspecialchars($_SESSION["firstname"]); ?></b>
            </span>
            <a type="button" href="logout.php" class="btn btn-danger my-2 my-sm-0" type="submit">Logout</a> 
        </div>
    </div>
</nav>