<?php
	include ("functions.php");
	sec_session_start();
	
	$stmt = $conn->prepare("SELECT user_password FROM user WHERE user_email = ?");
	$stmt->bind_param("s", $_SESSION["username"]);
	$stmt->execute();
	$stmt->bind_result($db_password);
	$stmt->fetch();
	
	if(password_verify($_POST["oldpass"], $db_password)) {
		
		$newpass = password_hash($_POST["newpass"], PASSWORD_DEFAULT);
		$stmt = 'UPDATE user SET user_password = "'.$newpass.'", user_temp_flag = "N" WHERE user_email = "'.$_SESSION["username"].'"';
		$conn->query($stmt);
		
		//$stmt = $conn->prepare("UPDATE user SET user_password = ?, user_temp_flag = 'N' WHERE user_id = 1");
		//$stmt->bind_param("s", $newpass);
		//$stmt->bind_param("si", $newpass, $_POST["user"]);
		
		//$stmt->execute();
		
		if (isset($_SESSION["reset_attempts"])) {
			unset($_SESSION["reset_attempts"]);
		}
		echo 'success';
		session_unset();
	}
	
	// Old password as entered did not match old password in database
	else {
		if (!isset($_SESSION["reset_attempts"])) {
			$_SESSION["reset_attempts"] = 1;
		}
		else {
			$_SESSION["reset_attempts"] = $_SESSION["reset_attempts"] + 1;
			
			// Lock account if failed to correctly input temp password 3 times in a row.
			if ($_SESSION["reset_attempts"] == 3) {
				lock_account($conn, $_SESSION["username"]);
			}
		}
		echo $_SESSION["reset_attempts"];
	}
?>