<?php
// This code ensures a session is started and active or you can not acces the page
include_once 'connect/connect.php';
include_once 'functions.php';

// prepare and bind
$stmt = $conn->prepare('UPDATE user SET user_password = ?, user_temp_flag = "Y" WHERE user_email = ?');
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

if($conn->affected_rows == 1) {
	// Reset Password, set parameters, execute
	$pass = bin2hex(random_bytes(8));
	$hash = PASSWORD_HASH($pass, PASSWORD_DEFAULT);
	$email = $_POST["useremail"];
	$stmt->execute();

	// Send email
	// Recipients
	$to = $email;

	// Subject
	$subject = 'G2 Ops Password Reset';

	// Message
	$message = '
	<html>
	<head>
	<title>G2 Ops Password Reset</title>
	<style>
	body {
		background-image: url("/rebus/img/background-clear-cropped.jpg");
		background-position: right bottom;
		background-size: contain;
		background-repeat: no-repeat;
		background-color: #eeeeee;
	}
	
	#prefooter {
		min-height: 100%;
		margin-bottom: -53px;	
		background-repeat: no-repeat; 
		background-size: contain;
		background-position: center 30px;
		font-size: 18px;
	}

	#prefooter:after {
		content: "";
		display: block;
		height: 53px
	}

	footer {
		height: 30px;
	}

	footer {
		background-color: silver ;
		bottom: 0;
		padding-top: 0px;
		width: 100%;
		text-decoration: small-caps;
		text-align: center;
		color: black;
		border: 1px solid black;
		font-size: 20px;
	}
	</head>
	<body>
	<div id="prefooter">
	<p>Your password has been reset upon request. If you did not initiate this request, contact G2 Ops immediately. 
	Your password has been reset to ' . $pass . '. After logging in with this password, you will be prompted to input a permanent password.</p>
	</div>
	<div id="footer">
	<footer>
		G2 Ops &diams;
		205 Business Park Dr #200 &diams;
		Virginia Beach, VA 23462 &diams;
		(757) 965-8330 &diams;
	</footer>
	</div>
	</body>
	</html>
	';

	// To send HTML mail, the Content-type header must be set
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	// Additional headers
	$headers[] = 'To: ' . $to;
	$headers[] = 'From: G2 Ops <G2 Ops Email>';
	// Bcc if you want to be notified when an email is reset 
	// $headers[] = 'Bcc: example@g2-opts.com';

	// Mail it
	mail($to, $subject, $message, implode("\r\n", $headers));
}
else {
	header('Location: ../reset_page.php?error=1');
	exit;
}
?> 