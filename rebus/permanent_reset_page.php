<?php 
include_once('php/functions.php');
sec_session_start();

if (!isset($_SESSION["username"]) || isset($_SESSION["user_id"])) {
	logout("unauthweb");
}
?>

<html>
<head>
	<title>Permanent Password Set</title>
	<meta charset="UTF-8" />
	<meta name"author" content="Rebus Group" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container" id="prefooter">
	<div id="set_password" style="padding-top: 100px">
		<h3 id="message"></h3>
		<h2>Set Permanent Password</h2>
		<div class="text-center" >
			<p>Enter your temporary password and desired permanent password.</p>
			<p>Permanent password must contain at least 8 characters and include at least one lowercase letter, uppercase letter, numeral, and of these special symbols: ! @ # $ & *</p>
			<form method="post" action="php/permanent_reset.php">
				<div class="form-group">
					<input type="password" name="oldpass" id="oldpass" placeholder="Old Password" required>
				</div>
				<div class="form-group">
					<input type="password" name="newpass1" id="newpass1" placeholder="New Password" required>
				</div>
				<div class="form-group">
					<input type="password" name="newpass2" id="newpass2" placeholder="Confirm New Password" required>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</div>
</body>
<script>
	$("button").click(function(event) {
		event.preventDefault();
		var oldpass = $("#oldpass").val();
		var newpass1 = $("#newpass1").val();
		var newpass2 = $("#newpass2").val();
		
		// Confirm both password fields match
		if (newpass1 == newpass2) {
			// Validate password schema before sending
			// At least eight characters
			var regex = new RegExp('^.{8}');
			if (regex.test(newpass1)) {
				// One uppercase letter
				regex = RegExp('^(?=.*[A-Z])');
				if (regex.test(newpass1)) {
					// One lowercase letter
					regex = RegExp('^(?=.*[a-z])');
					if (regex.test(newpass1)) {
						// One numeral
						regex = RegExp('(?=.*[0-9])');
						if (regex.test(newpass1)) {
							// One special character
							regex = RegExp('^(?=.*[!@#$&*])');
							if (regex.test(newpass1)) {
								// Password passes. Pass to change password php page
								$.ajax({
									url: "php/permanent_reset.php",
									type: "POST",
									data: ({
											oldpass: oldpass,
											newpass: newpass1
									}),
									success: function (data){
										if ($.trim(data) == "success") {
											$(location).attr('href', 'login.php?error=8');
										}
										else if (parseInt($.trim(data), 10) <= 3) {
											$("#message").text("Enter the correct temporary password."+data);
										}
										else if (parseInt($.trim(data), 10) >= 3) {
											$(location).attr('href', 'login.php?error=5');
										}
										else {
											$("#message").text(data);
										}
									},
									error: function(data) {
										$("#message").text("Could not communicate with the server. Please contact your administrator for assistance.");
									}
								});
								
							}
							else {
								$("#message").text("At least one of the above mentioned special symbols is required.");
							}
						}
						else {
							$("#message").text("At least one digit is required.");
						}
					}
					else {
						$("#message").text("At least one lowercase letter is required.");
					}
				}
				else {
					$("#message").text("At least one uppercase letter is required.");
				}
			}
			else {
				$("#message").text("At least 8 characters are required.");
			}
		}
		else {
			$("#message").text("New password fields do not match.");
		}
	});
</script>
</html>