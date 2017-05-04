<?php
include('php_files_header.php');

// Prepare the query and bind the data entered on the add org form into a procedure 
$stmt = $conn->prepare('CALL insert_org_sp(?,?,?,?,?,?,?,?,?,?)');
$stmt->bind_param("sssssssiss", $orgname, $saddress, $city, $state, $o_state, $zipcode, $country, $parent, $s_provider, $user);

// set parameters based on fields from add user form and execute the procedure
$orgname = $_POST["oname"];
$saddress = $_POST["addr"];
$city = $_POST["city"];
$state = $_POST["usstate"];
$o_state = $_POST["otherstate"];
$zipcode = $_POST["zip"];
$country = $_POST["country"];
$parent =  !empty($_POST["orgid"]) ? $_POST["orgid"] : NULL;
$s_provider = !empty($_POST['provider']) ? $_POST['provider'] : NULL;
$user = $_SESSION['username'];
$stmt->execute();

// Error control
if (!$conn->errno) {
	$_SESSION["message"] = 'Organization has registered successfully';
}
else if ($conn->errno == 1062) {
	$_SESSION["message"] = 'This organization already exists.';
}
else {
	$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.';
}

// Closes all statements and the DB connection
$stmt->close();
$conn->close();

header('Location: ../add_organization.php');
exit;
?> 