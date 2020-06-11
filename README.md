# Introduction
Please find report.docx for a more detailed description.  
  
# Installation  
1. Extract app structure in your local directory.  
2. Create the DB.  
    * Create the DB, user, password in your system.  
    * Import the dbstructure.sql file.  
    * Note: An admin user is already included in the dbstructure.sql file. Please change the email and password after the first login.  
	    * Email: Find it in the report.docx file
		* Password: Find it in the report.docx file
    * Replace specific globals with your DB values in ```config/dbconfig.php```.  
3. Verify that you are comply with the system Requirements:  
    * DB: MariaDB v10.1.34  
    * Server (Backend): PHP 7.2.11  
4. Verify that __mail()__ is correctly configured in your system.  
5. Access ```yourservername/index.php```  
  
# Documentation  
* Please refer to __report.docx__ file in the __Documentation__ section for a brief documentation.  
* There is an appropriate code document in the FUNCTIONSdesc.md referring to the ```helpers/functions.php``` file.  
  
# Isuues/Limitations  
1. Add Front End validation for email exists.
2. Mail works?
3. If there are many administrators, what is the relationship between employee and administrator/supervisor? In the current project we assumed that there is only one administrator with a valid email so that he will be sent the request submission.
4. Add database closing connections
5. Does not update session if current user updates his name.
6. Add logging (user audit)
   * User logins  
   * Supervisor approval/rejection