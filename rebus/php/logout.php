<?php
// Processes the logout request, kills sessions and deletes cookies
include('functions.php');
sec_session_start();
logout("php");
?>