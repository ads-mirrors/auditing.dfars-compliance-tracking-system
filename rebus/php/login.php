<?php
include_once 'functions.php';
sec_session_start(); // Our custom secure way of starting a PHP session.

if (isset($_POST['username'], $_POST['password'])) {
    $email = $_POST['username'];
    $password = $_POST['password']; // The hashed password.
    if (login($email, $password, $conn) == true) {
        // Login success
		header('Location: ../index.php');
		exit;
    } else {
        // Login failed
		header('Location: ../login.php?error=1');
		exit;
    }
} else {
    // The correct POST variables were not sent to this page.
	header('Location: ../login.php?error=2');
	exit;
}
?>








 
