# Please read the Documentation/userdocumentation.md for

/**
 * Gets a string of the administrators emails
 * For this project all administrator emails is not implemented
 *
 * @param $dbconn Database connection
 * @param string $all Get all administrator emails
 * @return string The administrator(s) emails as string
 */
getAdministratorEmails($dbconn,$all=false)

/**
 * Get user's full name
 * Combines name and surname into a string.
 *
 * @param $dbconn DB connection
 * @param $id User id
 * @return string User fullname
 */
getUserFullName($dbconn,$id)

/**
 * Get user's email
 *
 * @param $dbconn DB connection
 * @param $id User id
 * @return string User's email
 */
getUserEmail($dbconn,$id)

/**
 * Checks if given email exists in the USER DB table.
 *
 * @param $dbconn DB connection
 * @param $email The email to be checked
 * @return array Array(thestoredpassword,thestoredid,thestoredfirstname,iferroroccured,emailfound)
 */
userEmailExists ($dbconn,$email)

/**
 * Emails user regarding his submission change (on update/on reject).
 *
 * @param $userEmail String The user's email
 * @param $supervisorEmail String The supervisor/administrator email
 * @param $status Integer The submission status (1 if approved, 2 if rejected)
 */
sendUserEmail($userEmail,$supervisorEmail,$status)

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
sendSubmissionEmail($userFullName,$email,$datefrom,$dateto,$reason,$submissionid,$emailTo)

/**
 * Retrieves the list of users from the DB table User.
 *
 * @param $dbconn The DB connection
 * @return array(result,haserror) : result is an array of user objects, haserror if something occured
 */
getUserList($dbconn)

/**
 * Retrieves user information based on userid.
 *
 * @param $userid The user's id
 * @param $dbconn The DB connection
 * @return array(result,haserror) : result is an array of user info, haserror if something occured
 */
getUser($userid,$dbconn)

/**
 * Inserts user in db based on user's given information.
 * Password hash is produced based on user's password using 'password_hash' function with PASSWORD_DEFAULT
 * User type is stored as 0 (if employee) and 1 (if admin)

 * @param $dbconn The DB connection
 * @param $firstname User's firstname
 * @param $lastname User's lastname
 * @param $email User's email
 * @param $type User's type (admin/employee)
 * @param $password User's password
 * @return array(status,haserror) : status 0 if inserted, haserror 0 if no errors occurred
 */
insertUser($dbconn,$firstname,$lastname,$email,$type,$password)

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
updateUser($dbconn,$firstname,$lastname,$email,$type,$password,$userid)

/**
 * Update submission (status) in DB table submission.
 * Note that an already approved/rejected status cannot be changed.
 *
 * @param $dbconn The DB connection
 * @param $submissionId The submission id
 * @param $status: The new submission status 1 if Approved, 2 if Rejected
 * @return array(status,haserror) : 1 for update success, 0 for not, haserror equals 1 if there is an error
 */
updateSubmission($dbconn,$submissionId,$status)

/**
 * Returns the number of working days between two dates.
 *
 * @param $datefrom_int Date from in unix timestamp
 * @param $dateto_int: Date to in Unix timestamp
 * @return int Number of working days (monday to friday).
 */
getNumOfWorkingDays($datefrom_int,$dateto_int)

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
addSubmission($dbconn,$userid,$datefrom,$dateto,$days,$reason)

/**
 * Checks if a username already exists. Usernames in our app refer to emails.
 *
 * @param $tempUsername The username user is trying to login with
 * @param $dbconn The DB connection
 * @return bool true if found, false if not
 */
usernameExists($tempUsername,$dbconn)

/**
 * Get user submission list from DB
 *
 * @param $userid The user requested to view their submission list
 * @param $dbconn The DB connection
 * @return array (result,haserror) : result is the submission list, hasrror 1 if something occurred.
 */
getSubmissionList($userid,$dbconn)

/**
 * Admin Authorization method. Checks if user is an admin.
 *
 * @param $id The user's id
 * @param $dbconn The DB connection
 * @return bool true if admin, false if not
 */
isAdmin($id,$dbconn)