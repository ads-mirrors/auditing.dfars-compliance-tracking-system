<?php 
include('php_files_header.php');

// Set user session variable
$stmt = $conn->prepare('SET @user = ?');
$stmt->bind_param("i", $user);
$user = $_SESSION["user_id"];
$stmt->execute();
// Initial basic search (defaults to first organization returned)
if (!isset($_POST["org_id"])) {	

	// Gather user's organizations
	$stmt = $conn->prepare('SELECT DISTINCT org_name, org_id FROM v_user_reference');
	$stmt->execute();
	$result = $stmt->get_result();
	$conn->close();
	
	// Create organization dropdown if user is associated with any organizations
	if ($result->num_rows > 0) {
		
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$values["organization"][] = $row["org_name"];
			$values["org_id"][] = $row["org_id"];
		}
	$allorgs = implode(', ',$values["org_id"]);
	
	// Render Organization Dropdown
	echo '
		<div id="org_search">
			<label>Organization</label>
			<select name="organization" id="org_dropdown">';
				for( $i = 0; $i<sizeof($values["organization"]); $i++ )  {
					echo '<option value="'.$values["org_id"][$i].'">'.$values["organization"][$i].'</option><br/>';
				}
			echo '
			</select>
			</br></br>
		</div>';
	}
	
	// Error out if he has no organizations
	else {
		exit('No organizations for this user. Contact your administrator for assistance');
	}
}

// After user chooses organization
else {
	
	// Gather chosen organization's systems
	$stmt = $conn->prepare('SELECT DISTINCT sys_name, sys_id FROM v_user_reference where org_id = ? && sys_id IS NOT NULL');
	$stmt->bind_param("i", $_POST["org_id"]);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$values["system"][] = $row["sys_name"];
			$values["sysid"][] = $row["sys_id"];
		}

		echo '
			<label>System</label>
			<select name="system" id="sys_dropdown">';
				for( $i = 0; $i<sizeof($values["system"]); $i++ )  {
					echo '<option value="'.$values["sysid"][$i].'">'.$values["system"][$i].'</option><br/>';
				}
				echo '
			</select>
			</br> </br>';
	} 
	else {
		mysqli_close($conn);
		exit('No systems for this organization. Add a system to continue.');
	}

	// Gather all standards
	$sql = 'SELECT DISTINCT stand_name, stand_version_rev_num, stand_id FROM v_standard WHERE stand_version_rev_num IN (SELECT max(stand_version_rev_num) from standard GROUP BY stand_name)';
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	mysqli_close($conn);
	
	if (mysqli_num_rows($result) > 0) {
		// output standard data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$values["standard"][] = $row["stand_name"];
			$values["standrev"][] = $row["stand_version_rev_num"];
			$values["standid"][] = $row["stand_id"];
		}
		
		echo '
			<label>Standard</label>
			<select name="standard" id="stand_dropdown">';
				for( $i = 0; $i<sizeof($values["standard"]); $i++ )  {
					echo '<option value="'.$values["standid"][$i].'">'.$values["standard"][$i].' ver. '.$values["standrev"][$i].'</option><br/>';
				}
				echo '
			</select>
		</fieldset></br>';			
	}
	else {
		exit('Standards could not be retrieved. Contact your administrator for assistance.');
	}
}
?>