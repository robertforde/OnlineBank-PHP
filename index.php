<?php

	//Login, Registration, Personal Detail Maintenance and Account choice page

	// This part of the PHP script connects to the database and also does any session variable 
	// setting and any database interaction. 
	
	session_start();

	// Create a clear registration validation array
	$validationArray = array("user" => "", "pass" => "", "securityQ" => "", "securityA" => "");
	
	// Declare vars for connecting to the database
	$host = "localhost";
	$username = "root";
	$pswd = "mysql";
	$dbName = "OnlineBank";
	

	$con = mysqli_connect($host, $username, $pswd, $dbName);

	
	// If we couldn't connect to the Database
	if (!$con){
		echo "Could not connect: " . mysqli_connect_errno();
		die();
		
	} elseif ($con){
		// Select the database
//		$db = mysql_select_db($dbName, $con) or die(mysql_error());


		// Validate user info entered
		function checkEntries(&$arr){
			
			// If user logged in then we do not check the $_POST['regUser'] as it will be empty - only check new user.
			if(!isset($_SESSION['loggedIn'])){

				// If user length <6 then report as bad
				if(strlen($_POST['regUser'])<6){
					
					$arr["user"] = "Username must be greater than 6 characters long ";
				}
			}
			
			// If pass length <10 or pass has no numbers or pass>20 then report as bad
			if(strlen($_POST['regPass'])<10 ||
					strlen($_POST['regPass'])>20 ||
					preg_match("/\d/", $_POST['regPass'])==0){
					
				$arr["pass"] = "Password must be 10 to 20 characters long with at least one number";
			}
			
			// Check that a valid e-mail was entered, otherwiase report as bad
			if(!filter_var($_POST['regEmail'], FILTER_VALIDATE_EMAIL)){
					
				$arr["email"] = "You must enter a valid e-mail address";
			}
			
			// Check that a security question was entered, otherwiase report as bad
			if(empty($_POST['regSecQ'])){
					
				$arr["securityQ"] = "You must enter a security question";
				echo $arr["securityQ"]; 
			}
			
			// Check that a security answer was entered, otherwiase report as bad
			if(empty($_POST['regSecA'])){
					
				$arr["securityA"] = "You must enter a security answer";
			}

			// If any bad entries then return false, else true.
			if(!empty($arr["user"]) || !empty($arr["pass"]) || !empty($arr["email"]) || !empty($arr["securityQ"]) || !empty($arr["securityA"])){
				return false;
			} else{
				return true;
			}
			
		}
		
		// If the user hsa com back from the transactions.php page then reset the account session variable.
		if(isset($_POST['submit'])){
			unset($_SESSION["account"]);
		}
		
		
		// If the user hit the submit button
		if(isset($_POST['submit'])){
		
			// Search for user's entries in the users table.
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			
			$sql = "SELECT * FROM users WHERE username='" . $user . "' AND password = '" . $pass . "'";
			$result = mysqli_query($con, $sql);

			// Check that the query ran ok, if it didn't set the $loginBad.
			if(!$result){
				$loginBad = 'Problem querying database' . mysqli_error($con);
		
			}else{
				$found = mysqli_num_rows($result);
				// If the user entered is not found set the $loginBad.
				if($found == 0){
					$loginBad = 'Incorrect Username/Password';

				// User found - save all user details to the Session so they can be updated.
				}else {
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$_SESSION["user"] = $row["username"];
						$_SESSION["pass"] = $row["password"];
						$_SESSION["eMail"] = $row["email"];
						$_SESSION["title"] = $row["title"];
						$_SESSION["forename"] = $row["forename"];
						$_SESSION["surname"] = $row["surname"];
						$_SESSION["addr1"] = $row["addr1"];
						$_SESSION["addr2"] = $row["addr2"];
						$_SESSION["addr3"] = $row["addr3"];
						$_SESSION["addr4"] = $row["addr4"];
						$_SESSION["secuirtyQuestion"] = $row["sec_question"];
						$_SESSION["secuirtyAnswer"]   = $row["sec_answer"];
					}
					
					// Find the accounts for the User and put tem into an Array
					$sql = "SELECT * FROM account WHERE username='" . $_SESSION["user"] . "'";
					$result = mysqli_query($con, $sql);
					
					// Check that the query ran ok, if it didn't set the accountBad.
					if(!$result){
						$accountBad = 'Problem querying database' . mysqli_error($con);
					
					}else{
//						$found = mysqli_num_rows($result);
						$accArray = array();
						
						// Accounts found - save accounts to an array called $accArray.
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
							array_push($accArray, $row);
						}
						
						// Set $loggedIn to true so Registration becomes Update Details
						$_SESSION['loggedIn'] = "true";

						// Save the $accArray into the session so that the list remains as long as the session does.
						$_SESSION["accArray"] = $accArray;
					}
				}
			}
			
		// If the security e-mail entered and continue button clicked
		}else if(isset($_POST['submitSecEmail'])){
						
			// Search for the email in the users table.
			$userEmail = $_POST['securityMail'];
							
			$sql = "SELECT * FROM users WHERE email='" . $userEmail . "'";
			$result = mysqli_query($con, $sql);
													
			// Check that the query ran ok
			if(!$result){
				echo "<script>errorText('#badEmail','Problem querying database: ');</script>";

					
			}else {
						
				$found = mysqli_num_rows($result);
								
				// If the user entered is not found - display message.
				if($found == 0){
					$emailBad = "Email not found";

				// Save the question in the Session.
				}else{
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$_SESSION["eMail"] = $row["email"];
						$_SESSION["secuirtyQuestion"] = $row["sec_question"];
						$_SESSION["secuirtyAnswer"]   = $row["sec_answer"];
					}				
				}
			}

		// If the security question has been answered.
		}else if(isset($_POST['submitSecAnswer'])){
					
			// If the answer is correct, save user/pass into session
			if(strcasecmp(strtoupper($_POST["securityAnswer"]), $_SESSION["secuirtyAnswer"]) == 0) {
				
				// Search for the email in the users table.
				$userEmail = $_SESSION['eMail'];
					
				$sql = "SELECT * FROM users WHERE email='" . $userEmail . "'";
				$result = mysqli_query($con, $sql);
					
				// Check that the query ran ok
				if(!$result){
					echo "<script>errorText('#badEmail','Problem querying database: ');</script>";
				
						
				}else {
				
					$found = mysqli_num_rows($result);
				
					// This will not happen as we already found the user.
					if($found == 0){
						
				
					}else{
						
						// Unset the Session Email, Security Question and Answer.
						unset($_SESSION["eMail"]);
						unset($_SESSION["secuirtyQuestion"]);
						unset($_SESSION["secuirtyAnswer"]);
						
						// Save the user and password in the Session.
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
							$_SESSION["user"] = $row["username"];
							$_SESSION["pass"] = $row["password"];
						}
					}
				}

			// If the incorrect answer was given
			}else{
				$answerBad = "Incorrect Answer to the Security Question";
			}

		// Check to see if a valid e-mail address was entered
		}else if (isset($_POST['submitMailMe'])){
			
			// Search for the email in the users table.
			$userEmail = $_POST['mailMe'];
					
			$sql = "SELECT * FROM users WHERE email='" . $userEmail . "'";
			$result = mysqli_query($con, $sql);
					
			// Check that the query ran ok
			if(!$result){
				echo "<script>errorText('#badEmail','Problem querying database: ');</script>";
				
						
			}else {
				
				$found = mysqli_num_rows($result);
				
				// If the user entered is found - set $validEmail.
				if($found != 0){
					$validEmail = "Your E-mail has been sent";
					
					// Get the username and password to sent in the e-mail.
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						$emailMessage = "Username: " . $row['username'] . "     Password: " . $row['password'];
					}			
				}
			}
		}else if (isset($_POST['logout'])){
			
			// Destroy the Session and clear all variables
			session_destroy();
			unset($_SESSION["user"]);
			unset($_SESSION["pass"]);
			unset($_SESSION["eMail"]);
			unset($_SESSION["title"]);
			unset($_SESSION["forename"]);
			unset($_SESSION["surname"]);
			unset($_SESSION["addr1"]);
			unset($_SESSION["addr2"]);
			unset($_SESSION["addr3"]);
			unset($_SESSION["addr4"]);
			unset($_SESSION["secuirtyQuestion"]);
			unset($_SESSION["secuirtyAnswer"]);
			unset($_SESSION["loggedIn"]);
			unset($_SESSION["accArray"]);
			unset($_SESSION["account"]);
			
		// If the registration or personal details form was submitted
		}else if (isset($_POST['submitReg'])){

			// If a user is logged in
			if(isset($_SESSION['loggedIn'])){
				
				// Create an array to hold invalid entries for Registration
				$validationArray = array("user" => "", "pass" => "", "securityQ" => "", "securityA" => "");
				
				// Check the validity of the entries
				$entryOk = checkEntries($validationArray);
				
				if($entryOk) {
					$sql =  "UPDATE users SET password = '" . $_POST['regPass'] . "', ";
					$sql .= "email = '" . $_POST['regEmail'] . "', ";
					$sql .= "title = '" . $_POST['regTitle'] . "', ";
					$sql .= "forename = '" . $_POST['regForename'] . "', ";
					$sql .= "surname = '" . $_POST['regSurname'] . "', ";
					$sql .= "addr1 = '" . $_POST['regAddr1'] . "', ";
					$sql .= "addr2 = '" . $_POST['regAddr2'] . "', ";
					$sql .= "addr3 = '" . $_POST['regAddr3'] . "', ";
					$sql .= "addr4 = '" . $_POST['regAddr4'] . "', ";
					$sql .= "sec_question = '" . $_POST['regSecQ'] . "', ";
					$sql .= "sec_answer = '" . $_POST['regSecA'] . "' ";
					$sql .= "WHERE username = '" . $_SESSION['user'] . "'";
				
					$result = mysqli_query($con, $sql);
				
					// Check that the query ran ok
					if(!$result){
						echo "<script>errorText('#badEmail','Problem querying database: ');</script>";
				
					// If updated then update the Session variables to reflect the changes on the form
					}else{
						$_SESSION['pass']     = $_POST['regPass'];
						$_SESSION["eMail"]    = $_POST['regEmail'];
						$_SESSION["title"]    = $_POST['regTitle'];
						$_SESSION["forename"] = $_POST['regForename'];
						$_SESSION["surname"]  = $_POST['regSurname'];
						$_SESSION["addr1"]    = $_POST['regAddr1'];
						$_SESSION["addr2"]    = $_POST['regAddr2'];
						$_SESSION["addr3"]    = $_POST['regAddr3'];
						$_SESSION["addr4"]    = $_POST['regAddr4'];
						$_SESSION["secuirtyQuestion"] = $_POST['regSecQ'];
						$_SESSION["secuirtyAnswer"]   = $_POST['regSecA'];
					
					}
				}
				
			// New User to be registered
			}else{
				
				// First check that the username is not already registered
				$sql = "SELECT * FROM users WHERE username='" . $_POST["regUser"] . "'";
				$result = mysqli_query($con, $sql);

				// Check that the query ran ok, if it didn't set the $loginBad.
				if(!$result){
					
					$loginBad = 'Problem querying database' . mysqli_error($con);
				
				}else{
					
					$found = mysqli_num_rows($result);
					
					// Create an array to hold invalid entries for Registration
					$validationArray = array("user" => "", "pass" => "", "securityQ" => "", "securityA" => "");
					
					// Check the validity of the entries
					$entryOk = checkEntries($validationArray);
					
					// If the user entered is not found then save user, create an a/c and save 
					if($found == 0){
					
						if($entryOk) {
						
							// Save Registration User to the Users table
							$sql =  "INSERT INTO users VALUES('" . $_POST['regUser'] . "', '";
							$sql .= $_POST['regPass'] . "', '";
							$sql .= $_POST['regTitle'] . "', '";
							$sql .= $_POST['regForename'] . "', '";
							$sql .= $_POST['regSurname'] . "', '";
							$sql .= $_POST['regAddr1'] . "', '";
							$sql .= $_POST['regAddr2'] . "', '";
							$sql .= $_POST['regAddr3'] . "', '";
							$sql .= $_POST['regAddr4'] . "', '";
							$sql .= $_POST['regSecQ'] . "', '";
							$sql .= $_POST['regSecA'] . "', '";
							$sql .= $_POST['regEmail'] . "')";
							
							$result = mysqli_query($con, $sql);
	
							// Check that the query ran ok
							if(!$result){
								echo "<script>errorText('#badEmail','Problem inserting into database: ');</script>";
							
							
							}else {
	
								// Create a new account number that doesn't already exist
								$accFound = true;
								while($accFound) {
									$newAccNo = rand(10000000,99999999);
									$sql = "SELECT * FROM account WHERE accNo=" . $newAccNo;
									$result = mysqli_query($con, $sql);
									
									// Check that the query ran ok, if it didn't set the $loginBad.
									if(!$result){
										$loginBad = 'Problem querying database' . mysqli_error($con);
									
									}else{
										$found = mysqli_num_rows($result);
										// If the account number is not found then quit loop.
										if($found == 0){
											$accFound = false;
										}
									}
								}
								
								// Add the account to the account table
								$sql =  "INSERT INTO account VALUES('" . $_POST['regUser'] . "', " . $newAccNo . ")";
	
								$result = mysqli_query($con, $sql);
										
								// Check that the query ran ok
								if(!$result){
									echo "<script>errorText('#badEmail','Problem inserting into database: ');</script>";
								
								
								}else {
								
									// E-Mail details to New User
									$emailMessage  = "Welcome to Online Bank.\r\n Your Username: "; 
									$emailMessage .= $_POST['regEmail'] . "\r\n Password: ";
									$emailMessage .= $_POST['regPass'] . "\r\n Account: ";
									$emailMessage .= $newAccNo;
									//mail($_POST["regEmail"], 'OnlineBank Registration', $emailMessage);
								
									// User details and new account saved so set $$validEntries to display appropriate message.
									$validEntries = "<h3>Registration complete, login details and account no will be emailed to you</h3>";
								
									// Saved so clear the Session variables to clear the Registration Form.
									unset($_SESSION['pass']);
									unset($_SESSION["eMail"]);
									unset($_SESSION["title"]);
									unset($_SESSION["forename"]);
									unset($_SESSION["surname"]);
									unset($_SESSION["addr1"]);
									unset($_SESSION["addr2"]);
									unset($_SESSION["addr3"]);
									unset($_SESSION["addr4"]);
									unset($_SESSION["secuirtyQuestion"]);
									unset($_SESSION["secuirtyAnswer"]);
								}
							}
						}			
				
					// Username found - Set the $invalidUser so we will Notify the user
					// save the posted deails in the session so the user doesn't have to re-type them
					}else {
						
						$invalidUser = "Username already in use please choose another";
						
						$_SESSION['pass']     = $_POST['regPass'];
						$_SESSION["eMail"]    = $_POST['regEmail'];
						$_SESSION["title"]    = $_POST['regTitle'];
						$_SESSION["forename"] = $_POST['regForename'];
						$_SESSION["surname"]  = $_POST['regSurname'];
						$_SESSION["addr1"]    = $_POST['regAddr1'];
						$_SESSION["addr2"]    = $_POST['regAddr2'];
						$_SESSION["addr3"]    = $_POST['regAddr3'];
						$_SESSION["addr4"]    = $_POST['regAddr4'];
						$_SESSION["secuirtyQuestion"] = $_POST['regSecQ'];
						$_SESSION["secuirtyAnswer"]   = $_POST['regSecA'];
					}
				}				
			}
		}else if(isset($_POST['userNewAcc'])){
						
			// Create a new account number that doesn't already exist
			$accFound = true;
			while($accFound) {
				$newAccNo = rand(10000000,99999999);
				$sql = "SELECT * FROM account WHERE accNo=" . $newAccNo;
				$result = mysqli_query($con, $sql);
								
				// Check that the query ran ok, if it didn't set the $loginBad.
				if(!$result){
					$loginBad = 'Problem querying database' . mysqli_error($con);
					
				}else{
					$found = mysqli_num_rows($result);
					// If the account number is not found then quit loop.
					if($found == 0){
						$accFound = false;
					}
				}
			}
			
				
			// Add the account to the account table
			$sql =  "INSERT INTO account VALUES('" . $_SESSION['user'] . "', " . $newAccNo . ")";
			
			$result = mysqli_query($con, $sql);
				
			// Check that the query ran ok
			if(!$result){
				$userMsgNewAcc = 'Problem inserting new account into database';
					
			}else {
				
				// Reload the accounts and add the new account to the session accounts array
				$sql = "SELECT * FROM account WHERE username='" . $_SESSION["user"] . "'";
				$result = mysqli_query($con, $sql);
					
				// Check that the query ran ok, if it didn't set the accountBad.
				if(!$result){
					$accountBad = 'Problem querying database' . mysqli_error($con);
						
				}else{
					$found = mysqli_num_rows($result);
					$accArray = array();
				
					// Accounts found - save accounts to an array called $accArray.
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
						array_push($accArray, $row);
					}
				
					// Save the $accArray into the session so that the list remains as long as the session does.
					$_SESSION["accArray"] = $accArray;
				}
				
				// Create message for the user
				$userMsgNewAcc = 'New Account has been created: ' . $newAccNo;
				
			}
			
		// If we have come from the transaction.php page via the Back button  
		}else if(isset($_POST['Back'])){

			// Create a clear registration validation array 
			$validationArray = array("user" => "", "pass" => "", "securityQ" => "", "securityA" => "");
			
			// Unset the Session account as we are no longer working with an account.
			unset($_SESSION["acount"]);
			
		}
	}
				
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Online Bank - Entry Screen</title>
	<link rel="stylesheet" href="style.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>
	<section id="page">
		<header>
			<h1>Online Bank System</h1>
		</header>
		<section id="leftPanel">
			<h2 id="headTop">LOGIN</h2>
		
			<script>

				function changeText(id, txt){
					$(id).html(txt);
				}
				
				function errorText(id, txt){
					$(id).html(txt);
					$(id).slideDown("slow");
				}
			
				function showDiv(id){
					$(id).slideDown("slow");
				}
			
				function hideDiv(id){
					$(id).slideUp();
				}

			</script>
			
			<section id="loginPanel">	
				<!-- Create the form actioning it back to this page. -->
				<form action='index.php' method='post'>
					<section class="EntryFormText">Username</section>
					<input type='text' name='user' value='<?php if(isset($_SESSION["user"])) echo $_SESSION["user"];?>' /><br><br>
					<section class="EntryFormText">Password</section>
					<input type='password' name='pass' value='<?php if(isset($_SESSION["pass"])) echo $_SESSION["pass"];?>' /><br><br>
					<input type='submit' name='submit' value='Submit'/>
					<input type='submit' name='forgot' value='Forgot Password' />
				</form>
			</section>		
			
			<!-- Set up the Red Messages Section -->
			<section id='displayMessage'>
			</section>
			
			<!-- Set up the Info Messages Section -->
			<section id='displayInfo'>
			</section>
								
			<!-- Set up the Forgot Password Section -->
			<section id='forgotOptions'>
				<form action='index.php' method='post'>
					<input type='submit' name='security' value='Security Question'/>
					<input type='submit' name='eMail' value='E-Mail Me' />
					<input type='submit' name='cancel' value='Cancel' />
				</form>
			</section>
			
			<!-- Set up the Security Question Email Section -->
			<section id='securityEmail'>
				<form action='index.php' method='post'>
					Email 
					<input type='text' name='securityMail' /><br><br>
					<input type='submit' name='submitSecEmail' value='Continue'/>
					<input type='submit' name='cancel' value='Cancel' />
				</form>
			</section>
			
			
			<!-- Set up the Bad Email Section -->
			<section id='badEmail'>
			</section>
			
			<!-- Set up the Security Question Section -->
			<section id='securityQ'>
				<form action='index.php' method='post'>
					<h3><?php echo $_SESSION["secuirtyQuestion"]?></h3>
					Answer 
					<input type='password' name='securityAnswer' /><br><br>
					<input type='submit' name='submitSecAnswer' value='Continue'/>
					<input type='submit' name='cancelSecAnswer' value='Cancel' />
				</form>
			</section>
		
			<!-- Set up the E-Mail Section -->
			<section id='eMailMe'>
				<form action='index.php' method='post'>
					Email 
					<input type='text' name='mailMe' /><br><br>
					<input type='submit' name='submitMailMe' value='Continue'/>
					<input type='submit' name='cancel' value='Cancel' />
				</form>
			</section>
			
			<!-- Set up the user new account message panel -->
			<section id='userNewAccMsg'>
			</section>
			
			<?php 
			
				// Check to see if user tried to login or if user is logged in (then will come in here and display the accounts panel)
				if(isset($_POST['submit']) || isset($_SESSION['loggedIn'])){
					// Check if we need to report a bad login or if we need to ask for account.
					if(!empty($loginBad)){
						echo "<script>errorText('#displayMessage','" . $loginBad . "');</script>";
					
					// Hide the login form panel and display the accounts panel.
					} else{
						echo "<script>hideDiv('#displayMessage');</script>";
						echo "<script>hideDiv('#loginPanel');</script>";
						
						// If accountBad is set then display the message 
						if(isset($accountBad)){
							echo "<script>errorText('#displayMessage','" . $accountBad . "');</script>";
						
						// Display Welcome message and list accounts to choose from
						}else {
							echo "<script>changeText('#headTop','Welcome " . $_SESSION['user'] . "');</script>";
							echo "<section id='accounts'>";
								echo "<h3>Please choose an account<h3><br>";
								echo "<form action='transactions.php' method='POST'>";
									echo "<select name=accTrans>";
									foreach ($_SESSION["accArray"] as $arr) {
										echo "<option value=" . $arr[accNo] . ">" . $arr[accNo] . "</option>";
									}
									echo "</select>";
									echo "<br><br><br>";
							
									echo "<input type='submit' name='submitAccount' value='Choose'/>";
								echo "</form>";
								echo "<form action='index.php' method='POST'>";
									echo "<input type='submit' name='userNewAcc' value='Create New Account' />";
									echo "<input type='submit' name='logout' value='Logout' />";
								echo "</form>";
							echo "</section>";

							echo "<script>showDiv('#accounts');</script>";
							
							if(isset($userMsgNewAcc)){
								echo "<script>changeText('#userNewAccMsg','" . $userMsgNewAcc . "');</script>";
								echo "<script>showDiv('#userNewAccMsg');</script>";
							}
						}
					}
												
			
				// If the forgot password button is clicked
				}else if(isset($_POST['forgot'])){
					echo "<script>showDiv('#forgotOptions');</script>";

				// If the security button is clicked
				}else if(isset($_POST['security'])){
					echo "<script>showHide('#forgotOptions');</script>";
					echo "<script>showDiv('#securityEmail');</script>";
					
				// If the security e-mail entered and continue button clicked
				}else if(isset($_POST['submitSecEmail'])){
					
					// If e-mail address is not on our database
					if(isset($emailBad)){
						echo "<script>errorText('#displayMessage','" . $emailBad . "');</script>";
					
					} else{
						echo "<script>showDiv('#securityQ');</script>";
					}
					
				}else if(isset($_POST['submitSecAnswer'])){

					// If the answer given was incorrect
					if(isset($answerBad)){
						echo "<script>errorText('#displayMessage','" . $answerBad . "');</script>";
						
					// Give the username and password to the user
					}else{
						$msg = "Username: " . $_SESSION['user'] . " Password: " . $_SESSION['pass'];
						echo "<script>errorText('#displayInfo', '" . $msg. "');</script>";
			
					}
					
					// Unset the $answerBad variable for next time
					unset($answerBad);
				
				// If the email button was clicked in the forgot options
				}else if (isset($_POST['eMail'])){
					echo "<script>showDiv('#eMailMe');</script>";

				// If the user submitted an email under the email option under forgot password options.
				}else if (isset($_POST['submitMailMe'])){
					
					// Send an email if the e-mail address was in our database
					if(isset($validEmail)) {
						//mail($_POST["mailMe"], 'Your info at OnlineBank', $emailMessage);
						echo "<script>errorText('#displayInfo', 'Your E-Mail has been sent');</script>";
						
					// If e-mail address was not in our database, display error msg
					} else{
						echo "<script>errorText('#badEmail','Invalid Email');</script>";
					}
				}
				
			?>
			
		</section>
		<section id="rightPanel">
		
			<?php
			
				// Show Update Details for logged in users
				if(isset($_SESSION['loggedIn'])){
					echo "<h2>" . $_SESSION["user"] . " - Personal Details</h2>";
					
				// Show New User Registration for new users
				}else {
					echo "<h2>Registration - New User</h2>";
				}	
			?>

			<section id="registrationPanel">
				<section id="regHeading">
				
				<?php
			
					// Show Update Details info for logged in users
					if(isset($_SESSION['loggedIn'])){
						echo "<h3>Any changes saved will update your Personal details</h3>";
					
					// Show New User Registration info new in users
					}else {
						echo "<h3>Confirmation details and account number wil be e-mailed to you when you register</h3>";
					}	
				?>
				
				</section>
				
				<!-- This section will display message when new user registers -->
				<section id="regValid"></section>
				
				<form action="index.php" method="post">
				
					<?php
			
						// Only allow new users to enter/change the username
						if(!isset($_SESSION['loggedIn'])){
							echo "Username";
							echo "<input type='text' name='regUser' maxlength='20'/><br><br>";
						}
							
					?>
		
					<section id="badUser" class="invalidEntry"></section>			
					Password
					<input type='password' name='regPass' value= '<?php if(isset($_SESSION["pass"])) echo $_SESSION['pass'];?>' maxlength='20'/><br>
					<section id="badPassword" class="invalidEntry"></section>
					<br>
					E-Mail
					<input type='text' name='regEmail' value='<?php if(isset($_SESSION["eMail"])) echo $_SESSION['eMail'];?>' maxlength='30'/><br><br>
					<section id="badEntryEmail" class="invalidEntry"></section>
					Title
					<input type='text' name='regTitle' value='<?php if(isset($_SESSION["title"])) echo $_SESSION['title'];?>' maxlength='4'/><br><br>
					Forename
					<input type='text' name='regForename' value='<?php if(isset($_SESSION["forename"])) echo $_SESSION['forename'];?>' maxlength='20'/><br><br>
					Surname
					<input type='text' name='regSurname' value='<?php if(isset($_SESSION["surname"])) echo $_SESSION['surname'];?>' maxlength='20'/><br><br>
					Address Line 1
					<input type='text' name='regAddr1' value='<?php if(isset($_SESSION["addr1"])) echo $_SESSION['addr1'];?>' maxlength='30'/><br><br>
					Address Line 2
					<input type='text' name='regAddr2' value='<?php if(isset($_SESSION["addr2"])) echo $_SESSION['addr2'];?>'maxlength='30'/><br><br>
					Address Line 3
					<input type='text' name='regAddr3' value='<?php if(isset($_SESSION["addr3"])) echo $_SESSION['addr3'];?>'maxlength='30'/><br><br>
					Address Line 4
					<input type='text' name='regAddr4' value='<?php if(isset($_SESSION["addr4"])) echo $_SESSION['addr4'];?>'maxlength='30'/><br><br>
					Security - Question
					<input type='text' name='regSecQ' value='<?php if(isset($_SESSION["securityQuestion"])) echo $_SESSION['secuirtyQuestion'];?>'maxlength='100'/><br><br>
					<section id="badSecurityQ" class="invalidEntry"></section>
					Security - Answer
					<input type='text' name='regSecA' value='<?php if(isset($_SESSION["securityAnswer"])) echo $_SESSION['secuirtyAnswer'];?>'maxlength='20'/><br><br>
					<section id="badSecurityA" class="invalidEntry"></section>
				
					<?php
				
						// Show Save Button for logged in users
						if(isset($_SESSION['loggedIn'])){

							echo "<input type='submit' name='submitReg' value='Save'/>";
						
						// Show Register Button for new users
						}else {
							echo "<input type='submit' name='submitReg' value='Register'/>";
						}

						// Notify the user that they will have to pick another username
						if(isset($invalidUser)){
							echo "<script>errorText('#badUser','" . $invalidUser . "');</script>";
						} 
						
						// If invalid user entered
						if($validationArray["user"]!=""){
							echo "<script>errorText('#badUser','" . $validationArray["user"] . "');</script>";
						}
						
						// If invalid password entered
						if(!empty($validationArray["pass"])) {
							echo "<script>errorText('#badPassword','" . $validationArray["pass"] . "');</script>";
						}
						
						// If invalid E-mail address entered
						if(!empty($validationArray["email"])) {
							echo "<script>errorText('#badEntryEmail','" . $validationArray["email"] . "');</script>";
						}
						
						// If invalid Security Question address entered
						if(!empty($validationArray["securityQ"])) {
							echo "<script>errorText('#badSecurityQ','" . $validationArray["securityQ"] . "');</script>";
						}
												
						// If invalid Security Question address entered
						if(!empty($validationArray["securityA"])) {
							echo "<script>errorText('#badSecurityA','" . $validationArray["securityA"] . "');</script>";
						}
												
						// Show the message for valid entry
						else if(isset($validEntries)){
							echo "<script>hideDiv('#regHeading');</script>";
							echo "<script>showDiv('#regValid');</script>";
							echo "<script>changeText('#regValid','" . $validEntries . "');</script>";
						}
					?>
					
				</form>
				
			</section>
		</section>
		<footer>
			<p>&copy; 2015 Online Bank</p>
		</footer>
	</section>
</body>
</html>