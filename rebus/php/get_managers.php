<?php 
	include ("connect/connect.php");
	
	// Gather user's organizations
	$sql = 'SELECT CONCAT(user_fname, " ", user_lname) user_name, user_id FROM user WHERE org_id = '.$_POST["org_id"];
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	mysqli_close($conn);
	
	echo '
		<option value=""></option>
		<option value="">NA</option>';
	while ($row=mysqli_fetch_assoc($result)) {
		$manname=$row["user_name"];
		$manid=$row["user_id"];
		echo '<option value= '.$manid.'>'.$manname.'</option>';
	}  
?>