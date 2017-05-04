<?php
include('php_files_header.php');

	// Set system session variable
	$sql = 'SET @sys = '.$_POST["sys_id"];
	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	// Gather chosen standard's categories
	$sql = 'SELECT DISTINCT standcat_id, standcat_num, cat_name FROM v_standard where stand_id = '.$_POST["stand_id"]; 
	$reqresult = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	// Gather chosen standard's ratings
	$sql = 'SELECT rate_id, rate_abbv, rate_name FROM rating WHERE rate_root_stand = '.$_POST["stand_id"].' OR rate_root_stand = (SELECT stand_root FROM standard WHERE stand_id = '.$_POST["stand_id"].')';
	$ratresult = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	
	mysqli_close($conn);
	
	// Create data arrays and dropdowns
	// Category and requirement data arrays
	if (mysqli_num_rows($reqresult) > 0) {
		
		// Output data for each row
		while($row = mysqli_fetch_assoc($reqresult)) {

				$catid[] = $row["standcat_id"];
				$cats[$row["standcat_id"]] = array ("catnum" => $row["standcat_num"], "catname" => $row["cat_name"]);
		} 
		
		$allcat = implode(', ',$catid);
		
		// Create category and requirement advanced search dropdowns
		echo '
			<fieldset id="advanced"><legend>Advanced Search</legend>
				<label>Category</label>
				<select name="cat_dropdown" id="cat_dropdown">
					<option value = "'.$allcat.'">All</option>';
					foreach( $cats as $id => $arrays )  {
						echo '<option value="'.$id.'">'.$arrays["catnum"].' -- '.$arrays["catname"].'</option><br/>';
					}
					echo '
				</select>
				</br> </br>';
	} 
	else {
		exit('Could not find the requirements for the standard you chose. Contact your administrator for aassistance');	
	}
	
	// Create Rating dropdown
	if (mysqli_num_rows($ratresult) > 0) {
		while($row = mysqli_fetch_assoc($ratresult)) {
			$rateid[] = $row["rate_id"];
			$rating[$row["rate_id"]] = array( "ratename" => $row["rate_name"], "rateabbv" => $row["rate_abbv"]);
		}
		
		$allrate = implode(', ',$rateid);

		echo '
			<label>Rating</label>
			<select name="rate_dropdown" id="rate_dropdown">
				<option value = "'.$allrate.'">All</option>
				<option value = "">NR -- Not Reviewed</option>';
				foreach( $rating as $id => $array )  {
					echo '<option value="'.$id.'">'.$array["rateabbv"].' -- '.$array["ratename"].'</option><br/>';
				}
				echo '
			</select>
			</br> </br>';
	}
	else {
		exit('Could not find ratings for the standard you chose. Contact your administrator for assistance.');				
	}

	// Create Range dropdown
	echo '
		<label>Range</label>
		<select name="range_dropdown" id="range_dropdown">
			<option value=1,2,3>All</option>
			<option value=1>Short-Term: 0-6 Months</option>
			<option value=2>Mid-Term: 6-12 Months</option>
			<option value=3>Long-Term: 12+ Months</option>
			<option value="not">No Range Set</option>
		</select>
	</fieldset>
	</br>
	</br>';
?>
