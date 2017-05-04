<?php
include_once('php/functions.php');
sec_session_start();
if (!login_check($conn)) {
	logout("web");
}
else {
	$_SESSION["expire"] = time() + 900;
}
?>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name"author" content="Rebus Group" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="scripts/bootstrap.min.js"></script>
<?php if (isset($customheader)) {
	echo $customheader;
} 
?>
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
				<li><a href="report.php" target="_self">Report</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Add</a>
					<ul class="dropdown-menu">
						<li><a href="add_organization.php" target="_self">Add Organization</a></li>
						<li><a href="add_user.php" target="_self">Add User</a></li>
						<li><a href="add_system.php" target="_self">Add System</a></li>
					</ul>
				</li>
				<?php if (strpos($_SESSION["username"], "g2-ops.com") !== FALSE) { echo '<li><a href="add_system_type.php" target="_self">Add System Type</a></li>';
				echo '<li><a href="standard_import_page.php" target="_self">Import Standard</a></li>'; } ?>
				<li><a href="sysreq_import_page.php" target="_self">Import Report</a></li>
				<li><a href="php/logout.php" target="_self">Logout</a></li> 
			</ul>
		  </div>
		</nav>
		<h3 id="message"><?php if (isset($_SESSION["message"])) { echo $_SESSION["message"]; unset($_SESSION["message"]); } ?></h3>