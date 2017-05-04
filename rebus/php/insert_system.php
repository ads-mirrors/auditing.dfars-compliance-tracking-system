 <?php
include('php_files_header.php');

// Prepare the query and bind the data entered on the add user form into the procedure
$stmt = $conn->prepare('CALL insert_system_sp(?,?,?,?,?)');
$stmt->bind_param("sssii", $sys_name, $ip_addr, $user, $type, $org);

// Set parameters based on fields from add user form and execute the procedure
$sys_name = $_POST["sysname"];
$ip_addr = $_POST["ip"];
$user = $_SESSION['username'];
$type = $_POST["typeid"];
$org = $_POST["orgid"];
$stmt->execute();

// Error control
if (!$conn->errno) {
	$_SESSION["message"] = 'System added successfully';
}
else if ($conn->errno == 1062) {
	$_SESSION["message"] = 'This system already exists for that organization.';
}
else {
	$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.'.$conn->error;
	
}

// Closes all statements and the DB connection
$stmt->close();
$conn->close();

header('Location: ../add_system.php');
exit;
?> 