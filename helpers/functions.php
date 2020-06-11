<?php

/**
 * Gets a string of the administrators emails
 * For this project all administrator emails is not implemented
 *
 * @param $dbconn Database connection
 * @param string $all Get all administrator emails
 * @return string The administrator(s) emails as string
 */
function getAdministratorEmails($dbconn,$all=false) {

    $emailList = '';

	// Get users where type equals 1 - they are Administrators
    $stmt = $dbconn->query("SELECT email FROM user WHERE type=1");

    // If there are any administrators 
    if ($stmt->num_rows > 0) {

        // Get the administrator list
        while($row = $stmt->fetch_assoc()) {

             // get the first one
			if (!$all){
			   $emailList = $row['email'];
			   break;
			}
			else{
			  // not implemented yet
			}

        }
        
    }
	
	return $emailList;

}

/**
 * Get user's full name
 * Combines name and surname into a string.
 *
 * @param $dbconn DB connection
 * @param $id User is
 * @return string User fullname
 */
function getUserFullName($dbconn,$id){

    $userFullName = '';

    $returnedUserInformation = getUser($id,$dbconn);

    if (!$returnedUserInformation['haserror']){
        $userProfile = $returnedUserInformation['result'];

        if (!empty($userProfile)){
            $userFullName = $userProfile['firstname'].' '.$userProfile['lastname'];
        }  
    }

    return $userFullName;
}

/**
 * Get user's email
 *
 * @param $dbconn DB connection
 * @param $id User id
 * @return string User's email
 */
function getUserEmail($dbconn,$id){

    $userEmail = '';

    $returnedUserInformation = getUser($id,$dbconn);

    if (!$returnedUserInformation['haserror']){
        $userProfile = $returnedUserInformation['result'];

        if (!empty($userProfile)){
            $userEmail = $userProfile['email'];
        }  
    }

    return $userEmail;
}

/**
 * Checks if given email exists in the USER DB table.
 *
 * @param $dbconn DB connection
 * @param $email The email to be checked
 * @return array Array(thestoredpassword,thestoredid,thestoredfirstname,iferroroccured,emailfound)
 */
function userEmailExists ($dbconn,$email){

    // Initialize variables
    $storedid = $storedPassword = $storedemail = $storedfirstname = '';
    $returnedInformation = array('storedpassword'=>'','storedid' => '', 'storedfirstname' => '', 'haserror'=>1, 'emailfound'=>0);

    // Prepare a select statement
    $stmt = $dbconn->prepare("SELECT id, email, password, firstname FROM user WHERE email = ?");
        
    if($stmt){
        // Bind the username (email) to the statement as string
        $stmt->bind_param("s", $email);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){

            $returnedInformation['haserror'] = 0;

            // Store result
            $stmt->store_result();
            
            // Check if username exists, if yes then verify password
            if($stmt->num_rows == 1){

                $returnedInformation['emailfound'] = 1;
                
                // Bind result variables
                $stmt->bind_result($storedid, $storedemail, $storedPassword, $storedfirstname);
                if($stmt->fetch()){
                    $returnedInformation['storedid'] = $storedid;
                    $returnedInformation['storedpassword'] = $storedPassword;
                    $returnedInformation['storedfirstname'] = $storedfirstname;
                }
                
            }
        }
    }

     // Close statement
     $stmt->close();

    return $returnedInformation;
}

/**
 * Emails user regarding his submission change (on update/on reject).
 *
 * @param $userEmail The user's email
 * @param $supervisorEmail The supervisor/administrator email
 * @param $status The submission status (1 if approved, 2 if rejected)
 */
function sendUserEmail($userEmail,$supervisorEmail,$status){

    // Get the submission status in text
    if ($status == 2)
        $statusText = 'rejected';
    else if ($status == 1)
        $statusText = 'approved';
    else
        $statusText = 'pending';

    $serverurl = "http://" . $_SERVER['HTTP_HOST'];

    $to = $userEmail;
    $subject = 'Vacation Request status changed';

    $headers = "From: " . strip_tags($supervisorEmail) . "\r\n";
    //$headers .= "Reply-To: ". strip_tags('georgia.christodoulou1@gmail.com') . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = '<html><body>';
    $message .= '<p>Dear employee,</p>';
    $message .= '<p>Your submission request status is now '.$statusText.'.</p>';
    $message .= '<br>';
    $message .= '</body></html>';

    mail($to,$subject,$message,$headers);
}

/**
 * Sends administrator/supervisor an email regarding the particular employee's vacation request.
 * It creates the reject and approve links using the submission id and submission status (1 for approve, 2 for reject)
 * The reject and approve links are query strings.
 *
 * @param $userFullName String The employee's fullname
 * @param $email String The employee's email
 * @param $datefrom String The date the vacation begins
 * @param $dateto String The date the vacation ends
 * @param $reason String The reason for the vacation request
 * @param $submissionid Integer The id of the particular submission to be emailed
 * @param $emailTo String The administrator/supervisor email
 */
function sendSubmissionEmail($userFullName,$email,$datefrom,$dateto,$reason,$submissionid,$emailTo){

    $serverurl = "http://" . $_SERVER['HTTP_HOST'];
    $approveLink = $serverurl."/approve.php?approve=1&id=".$submissionid;
    $rejectLink = $serverurl."/approve.php?approve=2&id=".$submissionid;
    $to = $emailTo;
    $subject = 'Vacation Request';

    $headers = "From: " . strip_tags($email) . "\r\n";
    //$headers .= "Reply-To: ". strip_tags('georgia.christodoulou1@gmail.com') . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = '<html><body>';
    $message .= '<p>Dear supervisor,</p>';
    $message .= '<p>employee <strong>'.$userFullName.'</strong> requested for some time off starting on <strong>'.$datefrom.'</strong> and ending on <strong>'.$dateto.'</strong>, stating the reason:</p>'.'<p><i>'.$reason.'</i></p>';
    $message .= '<br>';
    $message .= '<p>Click on one of the below links to approve or reject the application: </p>';
    $message .= '<a href="'.$approveLink.'">Approve</a>  -  <a href="'.$rejectLink.'">Reject</a>';
    $message .= '</body></html>';

    mail($to,$subject,$message,$headers);

}

/**
 * Retrieves the list of users from the DB table User.
 *
 * @param $dbconn The DB connection
 * @return array(result,haserror) : result is an array of user objects, haserror if something occured
 */
function getUserList($dbconn){

    // Initialize
    // empty user list
    $userList = array();

    // The returned array.
    $returnedInformation = array('result'=>$userList,'haserror'=>1);

    // create the SQL query
    $stmt = $dbconn->query("SELECT id, firstname, lastname, email, type FROM user");

    // If there are any users
    if ($stmt->num_rows > 0) {

        $returnedInformation['haserror'] = 0;
        
        // Get the user list
        while($row = $stmt->fetch_assoc()) {

            // Check if Admin or Employee
            if ($row['type'] == 0){
                $row['type'] = "Employee";
            }
            else
                $row['type'] = "Admin";

            $row['edit'] = 'edituser.php?id='.$row['id'];

            array_push($userList,$row);
        }

        $returnedInformation['result'] = $userList;
        
    }
    else if ($stmt->num_rows == 0){
        // no users retrieved but still correct result
        $returnedInformation['haserror'] = 0;
    }

    return $returnedInformation;

}

/**
 * Retrieves user information based on userid.
 *
 * @param $userid The user's id
 * @param $dbconn The DB connection
 * @return array(result,haserror) : result is an array of user info, haserror if something occured
 */
function getUser($userid,$dbconn){

    // The returned array.
	 $returnedInformation = array('result'=>array(),'haserror'=>1);

    $param_userid = '';

    // Prepare a select statement
    $sql = "SELECT id, firstname, lastname, email, password, type FROM user WHERE id = ?";
        
    if($stmt = $dbconn->prepare($sql)){

        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_userid);
            
            // Set parameters
            $param_userid = $userid;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                
                // get the result
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    $returnedInformation['haserror'] = 0;
                    $returnedInformation['result'] = $result->fetch_assoc();
                   
                } 
            }

        // Close statement
        $stmt->close();
    }

    return $returnedInformation;
}

/**
 * Inserts user in db based on user's given information.
 * Password hash is produced based on user's password using 'password_hash' function with PASSWORD_DEFAULT
 * User type is stored as 0 (if employee) and 1 (if admin)
 *
 * @param $dbconn The DB connection
 * @param $firstname User's firstname
 * @param $lastname User's lastname
 * @param $email User's email
 * @param $type User's type (admin/employee)
 * @param $password User's password
 * @return array(status,haserror) : status 0 if inserted, haserror 0 if no errors occurred
 */
function insertUser($dbconn,$firstname,$lastname,$email,$type,$password){

     //Initialization
     // The returned array.
	 $returnedInformation = array('status'=>0,'haserror'=>1);
     $param_firstname = $param_lastname = $param_password = $param_username = $param_type = '';

     // Prepare an insert statement
     $sql = "INSERT INTO user (email, password, firstname, lastname, type) VALUES (?, ?, ?, ?, ?)";
         
     if($stmt = $dbconn->prepare($sql)){
         // Bind variables to the prepared statement as parameters
         $stmt->bind_param("ssssi", $param_username, $param_password, $param_firstname, $param_lastname, $param_type);
         
         // Set parameters
         $param_firstname = $firstname;
         $param_lastname =  $lastname;
         $param_username = $email;
         $type == 'admin' ? $param_type = 1 : $param_type = 0;
         $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
         
         // Attempt to execute the prepared statement
         if($stmt->execute()){
             $returnedInformation['haserror'] = 0;
         }

         // Close the statement
         $stmt->close();
     }

     return $returnedInformation;
}

/**
 * Updates user information in the DB table user
 * If password is the same with the stored one do not update password.
 *
 * @param $dbconn The DB connection
 * @param $firstname User's firstname
 * @param $lastname User's lastname
 * @param $email User's email
 * @param $type User's type (admin/employee)
 * @param $password User's password
 * @param $userid User's id
 * @return array(status,haserror) : status 0 if inserted, haserror 0 if no errors occurred
 */
function updateUser($dbconn,$firstname,$lastname,$email,$type,$password,$userid){

    // The returned array.
    $returnedInformation = array('status'=>0,'haserror'=>1);

    $type == 'admin' ? $param_type = 1 : $param_type = 0;

    // do not update password
    if (empty($password)){
        $sql = "UPDATE user SET email='".$email."', firstname='".$firstname."',lastname='".$lastname."',type=".$param_type." WHERE id=".$userid;
    }
    else{
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
        $sql = "UPDATE user SET email='".$email."', password='".$param_password."', firstname='".$firstname."',lastname='".$lastname."',type=".$param_type." WHERE id=".$userid;
    }

    if ($dbconn->query($sql) === TRUE)
        $returnedInformation['haserror'] = 0;
   
   return $returnedInformation;

}

/**
 * Update submission (status) in DB table submission.
 * Note that an already approved/rejected status cannot be changed.
 *
 * @param $dbconn The DB connection
 * @param $submissionId The submission id
 * @param $status: The new submission status 1 if Approved, 2 if Rejected
 * @return array(status,haserror) : 1 for update success, 0 for not, haserror equals 1 if there is an error
 */
function updateSubmission($dbconn,$submissionId,$status){

    // The returned array.
	$returnedInformation = array('status'=>0,'haserror'=>1);

     // create the SQL query
    $stmt = $dbconn->query("SELECT status FROM submission WHERE id=".$submissionId);

    // If row found
    if ($stmt->num_rows == 1) {
        
        // Match found.
        while($row = $stmt->fetch_assoc()) {

            $returnedInformation['haserror'] = 0;

            // Check if nothing is submitted yet
            if ($row['status'] == 0){
                $updateSql = "UPDATE submission SET status=".$status." WHERE id=".$submissionId;
				
				if ($dbconn->query($updateSql) === TRUE)
                    $returnedInformation['status'] = 1;
            }
			// has already approved or rejected - status is already 0
        }
        
    }
	
	return $returnedInformation;
}

/**
 * Returns the number of working days between two dates.
 *
 * @param $datefrom_int Date from in unix timestamp
 * @param $dateto_int: Date to in Unix timestamp
 * @return int Number of working days (monday to friday).
 */
function getNumOfWorkingDays($datefrom_int,$dateto_int){

    $workdays = 0; 

    // SATURDAY is 6, SUNDAY is 0
        
    //$daysDiff = date_diff(date_create($dateto),date_create($datefrom));
    //$days = $daysDiff->format('%a');

    for ($i = $datefrom_int; $i <= $dateto_int; $i = strtotime("+1 day", $i)) {
        $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
        if ($day != 0 && $day != 6)
            $workdays++;
    }

    return intval($workdays);

}

/**
 * Inserts a new submission in the submission table.
 * Note that status is automatically set to 0 (pending)
 *
 * @param $dbconn The DB connection
 * @param $userid The requested user's id
 * @param $datefrom The date to start from
 * @param $dateto The date to end
 * @param $days The number of working - vacation days
 * @param $reason The vacation reason
 * @return array(status,haserror,newid) : haserror 0 if no error occurred, newid is the new id created
 */
function addSubmission($dbconn,$userid,$datefrom,$dateto,$days,$reason){

    // The returned array.
    $returnedInformation = array('status'=>0,'haserror'=>1,'newid' => null);
    $param_userid = $param_datestart = $param_datesubmit = $param_dateend = $param_days = $param_status = $param_reason = '';

    // Prepare an insert statement
    $sql = "INSERT INTO submission (userid, datestart, datesubmitted, dateend, days, status, reason) VALUES (?, ?, ?, ?, ?, ?, ?)";
         
    if($stmt = $dbconn->prepare($sql)){

        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("isssiis", $param_userid, $param_datestart, $param_datesubmit, $param_dateend, $param_days, $param_status, $param_reason);

        // Set parameters
        $param_userid = $userid;
        $param_datestart =  $datefrom;
        $param_dateend = $dateto;
        $param_datesubmit = date('Y-m-d H:i:s');
        $param_days = $days;
        $param_status = 0; //pending status
        $param_reason = $reason;

        //echo json_encode($stmt)."<br>";
        $stmtExecution = $stmt->execute();
        
        // Execute the prepared statement
        if($stmtExecution != false){

            $returnedInformation['haserror'] = 0;
            $returnedInformation['newid'] = $stmt->insert_id;
                 
        }

        // Close statement
        $stmt->close();
    }

    return $returnedInformation;
}

/**
 * Check if a username already exists
 * Returns true if exists, false if not
 */

/**
 * Checks if a username already exists. Usernames in our app refer to emails.
 *
 * @param $tempUsername The username user is trying to login with
 * @param $dbconn The DB connection
 * @return bool true if found, false if not
 */
function usernameExists($tempUsername,$dbconn){

    // Prepare a select statement
    $sql = "SELECT id FROM user WHERE email = ?";
    $param_username = '';
        
    if($stmt = $dbconn->prepare($sql)){

        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = trim($tempUsername);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    return true;
                } else{
                    return false;
                }
            } else{
                echo "Error: Something went wrong. Please try again later.";
                exit();
            }

        // Close statement
        $stmt->close();
    }
    
}

/**
 * Get user submission list from DB
 *
 * @param $userid The user requested to view their submission list
 * @param $dbconn The DB connection
 * @return array (result,haserror) : result is the submission list, hasrror 1 if something occurred.
 */
function getSubmissionList($userid,$dbconn){

    // Initialize
    $submissionList = array();
    $param_userid = null;

    // The returned array.
    $returnedInformation = array('result'=>$submissionList,'haserror'=>1);

    $sql = ("SELECT datestart, dateend, days, status, datesubmitted FROM submission WHERE userid = ? ORDER BY datesubmitted DESC"); 
    $stmt= $dbconn->prepare($sql);

    if($stmt){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_userid);

        $param_userid = $userid;

        // Attempt to execute the prepared statement
        if($stmt->execute()){

            $returnedInformation['haserror'] = 0;

            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc())
            {
                $row['datesubmitted'] = date("d-m-Y",strtotime($row['datesubmitted']));
                array_push($submissionList,$row);
            }

            $returnedInformation['result'] = $submissionList;
        } 


        // Close statement
        $stmt->close();
    
    }

    return $returnedInformation;
}

/**
 * Admin Authorization method. Checks if user is an admin using user's type param.
 *
 * @param $id The user's id
 * @param $dbconn The DB connection
 * @return bool true if admin, false if not
 */
function isAdmin($id,$dbconn)
{
    $adminRole = false;

     // Verify user type
    $sql = "SELECT type FROM user WHERE id=?";
    $stmt= $dbconn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
 
    if ($user['type'] == 1)
        $adminRole = true;
    
    return $adminRole;
}

?>