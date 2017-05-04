<?php 
include_once('php/functions.php');
sec_session_start();

if (isset($_SESSION["user_id"]) && $_SESSION["expire"] < time()) {
	header("Location: index.php");
}
?>

<!--Login Page of the Tidewater Rebus Group-->
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container text-center align-middle" id="prefooter">
	<h3 id="error">
		<?php
		// Login Error
		if (isset($_GET["error"])) {
			// Invalid Credentials
			if ($_GET["error"] == 0) {
				echo 'You have logged out.';
			}
			else if ($_GET["error"] == 1) {
				echo 'You have provided invalid credentials.';
			}
			// The correct POST variables were not sent to this page.
			else if ($_GET["error"] == 2) {
				echo 'Invalid Request. Contact administrator for assistance.';
			}
			else if ($_GET["error"] == 3) {
				echo 'You are not authorized to visit this page. You have been logged out.';
			}
			else if ($_GET["error"] == 4) {
				echo 'File does not exist. Contact G2 Ops at support@g2-ops.com.';
			}
			else if ($_GET["error"] == 5) {
				echo 'Your account has been locked. Contact your administrator for assistance.';
			}
			else if ($_GET["error"] == 6) {
				echo 'Your account is currently locked. Contact your administrator for assistance.';
			}
			else if ($_GET["error"] == 7) {
				echo 'Could not initiate a safe session (ini_set).';
			}
			else if ($_GET["error"] == 8) {
				echo 'You have created a new password. You may now sign in with it.';
			}
		}
		?>
	</h3>
	<div id="login" style="padding-top: 100px">
	<h2>Login</h2>
	<form action="php/login.php" method="POST">
		<div class="form-group">
			<input type="email" name="username" placeholder="Email" autofocus required></br>
		</div>
		<div class="form-group">
			<input type="password" name="password" placeholder="Password" required><br/>
		</div>
		<button type="submit" class="btn btn-primary">Log In</button>
	</form>
	</div>
	<div id="forgot" style="padding-top: 20px">
		<a href="reset_page.php">Forgot Password?</a>
	</div>
</div>
<div class="container" id="footer">
	<footer>
		205 Business Park Dr #200 &diams;
		Virginia Beach, VA 23462 &diams;
		(757) 965-8330 &diams;
	</footer>
</div>
</body>
</html>