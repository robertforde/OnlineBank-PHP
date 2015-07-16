<?php

	session_start();

	// Function to return today's date in the format to save into mysql
	function retDate(){
	
		// Get an array of the current date
		$currDateArr = getdate(time());
	
		// Convert it to a string in yyyy/mm/dd format.
		$dateStr = $currDateArr["year"] . "-" . $currDateArr["mon"] . "-" . $currDateArr["mday"];
	
		// Create a date variable on the $dateStr string
		$date = date("Y-m-d", strtotime($dateStr));
	
		//Return $date
		return $date;
	}
	
	
	// Store the account that we are working with, in the Session. Check first if the session is not loaded 
	// already, this is in case the session is opened in another browser and the user goes directly to the 
	// transactions.php page (session already set so won't override it with posted "accTrans" i.e nothing)

	// If you open the transactions page and you don't have an open session then display a message with
	// re-direct to the login page.
	
	if(!isset($_SESSION["account"])) {
		
		if(!isset($_POST["accTrans"])) {
			$noAccess = true;
		}
		

		else{
			$_SESSION["account"] = $_POST["accTrans"];
		}
	}
	

	if(!isset($noAccess)) {
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
			//db = mysql_select_db($dbName, $con) or die(mysql_error());
			
			
			// Calculate balance prior to transaction
			
			$priorBalance = 0;
	
			$sql = "SELECT * FROM transactions WHERE accNo = " . $_SESSION['account'];
			$result = mysqli_query($con, $sql);
			
			// Check that the query ran ok, if it didn't set the $badMessage.
			if(!$result){
				$badMessage = 'Problem querying database' . mysqli_error();
			
			}else{
			
				// Transactions found - Calculate the $balance.
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					
					// Calculate Balance
					
					if($row['type'] == "Lodgement"){
						$priorBalance += $row['amount'];
						
					} else if($row['type'] == "Withdrawal"){
						$priorBalance -= $row['amount'];
						
					} else if($row['type'] == "Transfer Out"){
						$priorBalance -= $row['amount'];
						
					} else if($row['type'] == "Transfer In"){
						$priorBalance += $row['amount'];
					}
				}
			}
			
			// Load all of the accounts into a $transferAccounts array for transfers
			$sql = "SELECT accNo FROM account";
			$result = mysqli_query($con, $sql);
			
			// Check that the query ran ok, if it didn't set the $badMessage.
			if(!$result){
				$badMessage = 'Problem querying database' . mysqli_error();
			
			}else{
					
				$transferAccounts = array();
				
				// Load all of the account names (exclusind the selected one) into an array called $transferAccounts
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
					if($row['accNo']!=$_SESSION["account"]) {
						array_push($transferAccounts, $row);
					}
					
				}
			}
			
			// If the user submitted a lodgement
			if(isset($_POST['lodgeAmt'])){
	
				$date = retDate();
	
				// Save the Lodgement transaction to the transactions table.
				$sql  =  "INSERT INTO transactions (date, username,accNo,type, amount,comment) VALUES('" . $date . "', '"; 
				$sql .= $_SESSION['user'] . "', '";
				$sql .= $_SESSION['account'] . "', '";
				$sql .= "Lodgement" . "', ";
				$sql .= $_POST['amt'] . ", '";
				$sql .= $_POST['comment'] . "')";
				
				$result = mysqli_query($con, $sql);
	
				// Check that the query ran ok
				if(!$result){
					$$badMessage = 'Problem inserting into database: ' . mysqli_error();
				}
				
				// if transaction inserted then just go back to the transaction list.
				else {}
			}
			
			// If the user submitted a withdrawal
			else if(isset($_POST['withDAmt'])){
	
				// Check if there is enough in the account to make the withdrawal
				
				$sql = "SELECT * FROM transactions WHERE accNo = " . $_SESSION['account'];
				$result = mysqli_query($con, $sql);
				
				// Check that the query ran ok, if it didn't set the $badMessage.
				if(!$result){
					$badMessage = 'Problem querying database' . mysqli_error();
				
				}else{
				
					if($priorBalance >= $_POST['amt']){
				
						$date = retDate();
				
						// Save the Lodgement transaction to the transactions table.
						$sql  =  "INSERT INTO transactions (date, username,accNo,type, amount,comment) VALUES('" . $date . "', '";  
						$sql .= $_SESSION['user'] . "', '";
						$sql .= $_SESSION['account'] . "', '";
						$sql .= "Withdrawal" . "', ";
						$sql .= $_POST['amt'] . ", '";
						$sql .= $_POST['comment'] . "')";
						
						$result = mysqli_query($con, $sql);
				
						// Check that the query ran ok
						if(!$result){
							$badMessage = 'Problem inserting into database: ' . mysqli_error();
						}
					
						// if transaction inserted then just go back to the transaction list.
						else {}
						
					// Not enough money so set the $badMessage variable.
					} else{
						$badMessage = " Insufficient funds in your account, Withdrawal Unsuccessful !!";
					}
				}
			}
	
			//if the user submitted a transfer
			else if(isset($_POST['transferAmt'])){
				
				// Check that there is enough in the account to make cover the transfer 
				if($priorBalance >= $_POST['amt']){
	
					$date = retDate();
					
					// Save the transfer-out transaction to the transactions table.
					$sql  =  "INSERT INTO transactions (date, username,accNo,type,tranAccNo,amount,comment) VALUES('" . $date . "', '";
					$sql .= $_SESSION['user'] . "', '";
					$sql .= $_SESSION['account'] . "', '";
					$sql .= "Transfer Out" . "', ";
					$sql .= $_POST['accTransfer'] . ", ";
					$sql .= $_POST['amt'] . ", '";
					$sql .= $_POST['comment'] . "')";
						
					$result = mysqli_query($con, $sql);
						
					// Check that the query ran ok
					if(!$result){
						$badMessage = 'Problem inserting into database: ' . mysqli_error();
					}
					
					// if transaction inserted then insert the transfer-in transaction.
					else {
						
						// Save the transfer-in the transactions to the transaction table.
						$sql  =  "INSERT INTO transactions (date, username,accNo,type,tranAccNo,amount,comment) VALUES('" . $date . "', '";
						$sql .= $_SESSION['user'] . "', '";
						$sql .= $_POST['accTransfer'] . "', '";
						$sql .= "Transfer In" . "', ";
						$sql .= $_SESSION['account'] . ", ";
						$sql .= $_POST['amt'] . ", '";
						$sql .= $_POST['comment'] . "')";
							
						$result = mysqli_query($con, $sql);
						
						// Check that the query ran ok
						if(!$result){
							$badMessage = 'Problem inserting into database: ' . mysqli_error();
						
						
						// if transaction inserted then just go back to the transaction list.
						}else{}
					}
				
				// Not enough money so set the $badMessage variable.
				} else{
					$badMessage = " Insufficient funds in your account, Withdrawal Unsuccessful !!";
				}
			}
			
			else if(isset($_POST['Back'])){
				unset($_SESSION[""]);
			}
					
					
			// Once any new transactions are saved, calculate the balance on this account and load the transaction 
			// array - $tranArray.
			
			$sql = "SELECT * FROM transactions WHERE accNo = " . $_SESSION['account'] . " ORDER BY date DESC";
			$result = mysqli_query($con, $sql);
					
			// Check that the query ran ok, if it didn't set the $badMessage.
			if(!$result){
				$badMessage = 'Problem querying database' . mysqli_error();
			
			}else{
			
				$tranArray = array();
				$balance = 0;
			
				// Transactions found - save transactions to an array called $tranArray and calculate the $balance.
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			
					// Save transaction for list
					array_push($tranArray, $row);
			
					// Calculate Balance 
					if($row['type'] == "Lodgement"){
						$balance += $row['amount'];
							
					} else if($row['type'] == "Withdrawal"){
						$balance -= $row['amount'];
							
					} else if($row['type'] == "Transfer Out"){
						$balance -= $row['amount'];
						
					} else if($row['type'] == "Transfer In"){
						$balance += $row['amount'];
					}
				}
			
			}
	
		}
	}	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Online Bank - Account Screen</title>
	<link rel="stylesheet" href="style.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>

	<?php
		if(!isset($noAccess)) { 
	?>
		
	<section id="page">
		<header>
			<h1>Online Bank System</h1>
		</header>
		
			<!-- jQuery Functions to Show and Hide Divs -->
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
		
		<section id="content">
		
			<section id="badTransactionMessage">
			</section>
		
			<section id="transactionOptions">
			
				<form action="transactions.php" method="POST">
					<input type="submit" name="Lodge" value="Lodgement" />
					<input type="submit" name="Withd" value="Withdraw" />
					<input type="submit" name="Trans" value="Transfer" />
				</form>
				
				
			</section>
			
			<section id=detailTransactions>
				
				<section id="leftDetail">				

					<!-- Display Username in Session -->
					<section class="leftText">
						Username
					</section>
					<section class="leftBox">
						<?php echo $_SESSION["user"]; ?>
					</section>
					
					<!-- Display Account in Session -->
					<section class="leftText">
						Account 
					</section>
					<section class="leftBox">
						<?php echo $_SESSION["account"];?>
					</section>
					
					<!-- Display Calculated Balance -->
					<section class="leftText">
						Balance 
					</section>
					<section class="leftBox">
						<?php echo "&euro;" . $balance ?>
					</section>
					
					<form action="index.php" method="POST">
						<input type="submit" name="Back" value="Back" />
					</form>
									
				</section>
				
				<section id="rightDetail">
				
					<section id="transactionsList">
					
						<table>
							<tr>
								<th>DATE</th>
								<th>TRANSACTION TYPE</th>
								<th>DEBIT</th>
								<th>CREDIT</th>
								<th>COMMENT</th>
								<th>TRANSFER ACC</th>
							</tr>
							
							<?php 

								$debit  = array("Lodgement", "Transfer In");
								$credit = array("Withdrawal", "Transfer Out");
								$transf = array("Transfer Out", "Transfer In");
								
								// Loop through $tranArray and display the list of transactions
								foreach ($tranArray as $arr) {
									
										echo "<tr>";
										
											echo "<td class='lefftie'>" . $arr["date"] . "</td>";
										
											echo "<td class='lefftie'>" . $arr["type"] . "</td>";
											
											//Check if Debit or Credit
											if(in_array($arr["type"], $debit)) {
												echo "<td class='rightie'>" . $arr["amount"] . "</td>";
												echo "<td></td>";
												
											} else if(in_array($arr["type"], $credit)){
												echo "<td></td>";
												echo "<td class='rightie'>" . $arr["amount"] . "</td>";
											}
											
											
											echo "<td class='centie'>" . $arr["comment"] . "</td>";
											
											// If transfer
											if(in_array($arr["type"], $transf)) {
												echo "<td class='leftie'>" . $arr["tranAccNo"] . "</td>";
												
											} else{
												echo "<td></td>";
											}

										echo "</tr>";
									}
									
							?>
							<tr>
							</tr>
							
						</table>
						
					</section>
					
					<section id="transactionsLodge" class="transDetailEntry">
						<form action="transactions.php" method="post">
							Amount:
							<input type="text" name="amt"  maxlength="15" /><br><br>
							Comment
							<input type="text" name="comment" maxlength="20" /><br><br>
							<input type="submit" name="lodgeAmt" value="Lodge" />
							<input type="submit" name="cancel"  value="Cancel" /> 
						</form>
					</section>
					
					<section id="transactionsWithdraw" class="transDetailEntry">
						<form action="transactions.php" method="post">
							Amount:
							<input type="text" name="amt"  maxlength="15" /><br><br>
							Comment
							<input type="text" name="comment" maxlength="20" /><br><br>
							<input type="submit" name="withDAmt" value="Withdraw" />
							<input type="submit" name="cancel"  value="Cancel" /> 
						</form>
					</section>
					
					<section id="transactionsTransfer" class="transDetailEntry">
						<form action="transactions.php" method="post">
							Amount:
							<input type="text" name="amt"  maxlength="15" /><br><br>
							Transfer To Account
							<select name=accTransfer>
								
							<?php

								// Load the accounts from the $transferAccounts array
								foreach ($transferAccounts as $arr) {
									echo "<option value=" . $arr[accNo] . ">" . $arr[accNo] . "</option>";
								}
								echo "</select><br><br>";
									
							?>
							
							Comment
							<input type="text" name="comment" maxlength="20" /><br><br>
							<input type="submit" name="transferAmt" value="Transfer" />
							<input type="submit" name="cancel"  value="Cancel" /> 
						</form>
					</section>
										
				</section>
				
			</section>
			
			<?php 
			
				// If there is a bad message to be displayed
				if(isset($badMessage)){
					echo "<script>errorText('#badTransactionMessage','" . $badMessage , "');</script>";
				}
				
				// If the user hit the Lodgement button then hide the reansaction list and display lodgement form.
				if(isset($_POST['Lodge'])){
			
					echo "<script>hideDiv('#transactionsList');</script>";
					echo "<script>showDiv('#transactionsLodge');</script>";
			
				}
				
				// If the user hit the Withdraw button then hide the reansaction list and display withdraw form.
				else if(isset($_POST['Withd'])){
					
					echo "<script>hideDiv('#transactionsList');</script>";
					echo "<script>showDiv('#transactionsWithdraw');</script>";
					
				}
				
				// If the user hit the Transfer button then hide the reansaction list and display transfer form.
				else if(isset($_POST['Trans'])){
					
					echo "<script>hideDiv('#transactionsList');</script>";
					echo "<script>showDiv('#transactionsTransfer');</script>";
					
				}
				
			?>
			
		</section>
		<footer>
			<p>&copy; 2015 Online Bank</p>
		</footer>
	</section>
	
		
	<?php

		// If the user does not have a session with an account then display this message instead of the normal 
		// transaction page.
		} else if(isset($noAccess)){
			echo "YOU ARE NOT AUTORISED TO VIEW THIS PAGE<br><br>";
			echo "<a href='index.php'> Please Login here !</a>";
		} 
	?>
	
</body>
</html>