<?php
include_once('php/functions.php');
sec_session_start();

if (isset($_SESSION["user_id"])) {
	logout("unauthweb");
}
?>

<html>
<head>
	<title>Reset Password</title>
	<meta charset="UTF-8" />
	<meta name"author" content="Rebus Group" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container" id="prefooter">
<!--Menu bar -->
	<nav class="navbar navbar-inverse">
	  <div class="container-fluid">
		<div class="navbar-header">
			<img id="title" src="img/G2globe-gray.png" style="height:50px;"/>
		</div>
		<ul class="nav navbar-nav">
			<li><a href="index.php" target="_self">Home</a></li>
			<li><a href="add_organization.php" target="_self">Add Organization</a></li>
			<li><a href="add_user.php" target="_self">Add User</a></li>
			<li><a href="add_system.php" target="_self">Add System</a></li>
			<li><a href="add_system_type.php" target="_self">Add System Type</a></li>
			<li><a href="report.php" target="_self">Report</a></li>
			<li><a href="sysreq_import_page.php" target="_self">Import</a></li>
			<li><a href="php/logout.php" target="_self">Logout</a></li> 
		</ul>
	  </div>
	</nav>
	<h3 id="message">
		<?php if (isset($_GET["error"])) {
			echo 'You are not in our system.';
		}
		?>
	</h3>
	<h2>Reset Password</h2>
	<div class="text-center">
		<p>You will receive an email with your reset password. After logging in with that passoword, you will be prompted to put in a new, permanent password.</p>
		<form method="post" action="php/reset.php">
			<div class="form-group">
				<input type="email" name="useremail" placeholder="email" required>
			</div> 
			<button type="submit" class="btn btn-primary">Reset</button>
		</form>
	</div>
</div>
</body>
</html>