 <?php
include('php_files_header.php');

// Prepare the query and bind the data entered on the add user form into the procedure
$stmt = $conn->prepare('CALL insert_systype_sp(?,?)');
$stmt->bind_param("ss", $tname, $u_add);

// set parameters based on fields from add user form and execute the procedure
$tname = $_POST["typename"];
$user = $_SESSION['username'];
$u_add = $user;
$stmt->execute();

// Error control
if (!$conn->errno) {
	$_SESSION["message"] = 'System-Type added successfully';
}
else if ($conn->errno == 1062) {
	$_SESSION["message"] = 'This system-type already exists.';
}
else {
	$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.';
}

// Closes all statements and the DB connection
$stmt->close();
$conn->close();

header('Location: ../add_system_type.php');
exit;
?> 