<?php
include('functions.php');
sec_session_start();

if ($_SERVER['REQUEST_METHOD'] != "POST") {
	logout("unauthphp");
}

else if (!login_check($conn)) {
	logout("php");
}
else {
	$_SESSION["expire"] = time() + 900;
}
?>