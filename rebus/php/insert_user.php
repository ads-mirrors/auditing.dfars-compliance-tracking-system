 <?php
include('php_files_header.php');

// Prepare the query and bind the data entered on the add user form into the procedure 
$stmt = $conn->prepare('CALL insert_user_sp(?,?,?,?,?,?,?,?,?,?)');
$stmt->bind_param("ssssssssii", $fname, $mname, $lname, $ccode, $phone, $uname, $hash, $u_add, $org, $manager);

// set parameters based on fields from add user form and execute the procedure
$fname = $_POST["first"];
$mname = $_POST["middle"];
$lname = $_POST["last"];
$ccode = $_POST["country"];
$phone = $_POST["pnum"];
$uname = trim($_POST["email"]);
$pass = bin2hex(random_bytes(8));
$hash = password_hash($pass, PASSWORD_DEFAULT);
$u_add = $_SESSION["username"];
$org = $_POST['orgid'];
$manager = !empty($_POST['manid']) ? $_POST['manid'] : NULL;
$stmt->execute();

// Error control
if ($conn->errno == 1062) {
	$_SESSION["message"] = 'This email already exists.';
}
else if ($conn->errno) {
	$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.';
}
else {
	// Check if user's organization is a managed service provider
	$stmt = $conn->prepare('SELECT org_mnged_serv_provider FROM organization WHERE org_id = ?');
	$stmt->bind_param("i", $org);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();

	// Closes all statements and the DB connection
	$stmt->close();
	$conn->close();

	// Send email
	// Recipients
	$to = $uname;

	// Subject
	$subject = 'G2 Ops User Registration';

	// Message
	$message = '
	<html>
	<head>
	<title>G2 Ops User Registration</title>
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
			text-decoration: small-caps;
			text-align: center;
			color: black;
			border: 1px solid black;
			font-size: 20px;
		}
	</style>
	</head>
	<body>
	<div id="prefooter">
	<p>Welcome to the G2 Ops Standard Evaluation System. You are now capable of evaluating your organization\'s systems against our ever growing list of compliance standards. Alternatively, you may review evaluations run by G2 Ops personnel or your Managed Service Provider.</p>';

	if($row[0] == 'Y') {
		$message .= '<p>As a Managed Service Provider, you will have the additional capability of reviewing and evaluating the systems of your own organization and the organizations you are managing.</p>';
	} 

	$message .= '<p>Your password has been set to ' . $pass . ' After logging in <a href="localhost/rebus/login.php">here</a> with this password, you will be prompted to input a permanent password.</p>

	<p>For detailed instructions, download the user manual <a href="#">here</a>. If you have any questions or concerns, contact support@g2-ops.com.</p>
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
	// $headers[] = 'Bcc: example@g2-ops.com';

	// Mail it
	mail($to, $subject, $message, implode("\r\n", $headers));
	$_SESSION["message"] = 'User has registered successfully. He/She will receive a confirmation email shortly.';
}
header('Location: ../add_user.php');
exit;
?> 