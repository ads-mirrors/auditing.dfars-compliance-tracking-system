<!-- Return new report based on advanced settings -->	
<?php
	include ("php_files_header.php");

	// Set system session variable
	$stmt = $conn->prepare('SET @sys = ?');
	$stmt->bind_param("i", $_POST["sys_id"]);
	$stmt->execute();
	
	// Detect advanced filters
	// Category != All
	if(!strpos($_POST["standcat_id"], ",")) {
		$conditions[] = 'AND standcat_id IN ('.$_POST["standcat_id"].')';
	}
	
	// Rating != Not Reviewed or All
	if (!empty($_POST["rate_id"]) && !strpos($_POST["rate_id"], ',')) {
		$conditions[] = 'AND req_id IN (SELECT req_id FROM v_system_requirement WHERE rate_id = '.$_POST["rate_id"].')';
	}
	
	// Rating = Not Reviewed (doesn't exist in db)
	else if(empty($_POST["rate_id"])) {
		$conditions[] = 'AND req_id NOT IN (SELECT req_id FROM v_system_requirement)';
	}
	
	// Range != No Range Set or All
	if (1 === preg_match('~[0-9]~', $_POST["range_id"]) && !strpos($_POST["rate_id"], ',')) {
		$conditions[] = 'AND req_id IN (SELECT req_id FROM v_system_requirement WHERE rate_id = '.$_POST["rate_id"].')';
	}
	
	// Range = No Range Set
	else if ($_POST["range_id"] == "not") {
		$conditions[] = 'AND req_id IN (SELECT req_id FROM v_system_requirement WHERE range_id IS NULL)';
	}
	
	$stmt = 'SELECT DISTINCT standcat_id, standcat_num, cat_name, req_id, req_num, req_simple_desc FROM v_standard WHERE stand_id = '.$_POST["stand_id"];
		
	if (isset($conditions)) {
		$stmt .= ' '.implode(" ", $conditions);
	}
	
	$stmt = $conn->prepare($stmt);
	$stmt->execute();
	$reqresult = $stmt->get_result();
	
	// Only gather results if Rating != Not Reviewed
	if(!empty($_POST["rate_id"])) {
		// Gather results from running standard against system
		unset($conditions);
		$conditions[] = 'rate_id IN ('.$_POST["rate_id"].')';

		if(!empty($_POST["range_id"]) && $_POST["range_id"] != "not") {
			$conditions[] = 'range_id IN ('.$_POST["range_id"].')';
		}
		else {
			$conditions[] = 'range_id IS NULL';
		}
			$stmt = $conn->prepare('SET @sys = ?');
	$stmt->bind_param("i", $_POST["sys_id"]);
	$stmt->execute();
		$stmt = 'SELECT sys_id, req_id, rate_name, IFNULL(range_desc, "No Range Set") range_desc FROM v_system_requirement WHERE '.implode(" AND ", $conditions);
		$sysreqresult = mysqli_query($conn, $stmt);
	}
	
	$conn->close();
	
	// Create categories-requirements data arrays
	if ($reqresult->num_rows > 0) {
		while($row = $reqresult->fetch_assoc()) {

			// Category_id is not unique across rows
			if (!isset($catreqs[$row["standcat_id"]])) {
				$catreqs[$row["standcat_id"]]["cat"] = array ("catnum" => $row["standcat_num"], "catname" => $row["cat_name"]);
			}
			
			// Requirement_id is unique across rows
			$catreqs[$row["standcat_id"]]["reqs"][] = array("reqid" => $row["req_id"], "reqnum" => $row["req_num"], "reqdesc" => $row["req_simple_desc"]);
		} 
	} 
	else {
		exit('Could not find requirements for conditions you set. Try widening your parameters. 
			If you feel this is in error, contact your administrator for aassistance.');	
	}
	
	// Create report
	if (isset($sysreqresult) && $sysreqresult->num_rows > 0) {
		
		// Output data for each row
		while ($row = $sysreqresult->fetch_assoc()) {
			$sysreq[$row["req_id"]] = array("rating" => $row["rate_name"], "range" => $row["range_desc"]);
		}
	}
	
	// Output report table
	echo '
		<div id="report">
		<h2>'.$_POST["org_name"].': '.$_POST["sys_name"].' -- '.$_POST["stand_rev"].'</h2>
			<table class="table">
				<thead>
					<tr>
						<th>Requirement</th>
						<th>Rating</th>
						<th>Range</th>
					</tr>
				</thead>';
	foreach( $catreqs as $id => $arrays ) {
		echo '
				<tr><td colspan=3>'.$arrays["cat"]["catnum"].' -- '.$arrays["cat"]["catname"].'</td></tr>';
		foreach( $arrays["reqs"] as $reqarrays ) {
			echo '
				<tr>
					<td>&emsp;<a href="requirement.php?req='.$reqarrays["reqid"].'&sys='.$_POST["sys_id"].'&stand='.$_POST["stand_id"].'" target="_blank">'.$reqarrays["reqnum"].': '.$reqarrays["reqdesc"].'</a></td>';
				
				
			// Results
			if (isset($sysreq[$reqarrays["reqid"]])) {
				echo '
					<td>'.$sysreq[$reqarrays["reqid"]]["rating"].'</td>
					<td>'.$sysreq[$reqarrays["reqid"]]["range"].'</td>
				</tr>';
			} 
			
			// Requirement hasn't been reviewed against this system yet; Rating filter is All or Not Reviewed
			else if (strpos($_POST["rate_id"], ',') || empty($_POST["rate_id"])) {
				echo '
					<td>Not Reviewed</td>
					<td>No Range Set</td>
				</tr>';
			}
		}
	}
	
	// End of table and export link
	$sysdiv = '<input id="sys" style="display:none">'.$_POST["sys_id"].'</input>';
	$standdiv = '<input id="stand" style="display:none">'.$_POST["stand_id"].'</input>';
?>
		</table>
		<form method="post" action="php/export.php">
			<input type="hidden" name="sys" value="<?php echo $_POST["sys_id"]; ?>"></input>
			<input type="hidden" name="stand" value="<?php echo $_POST["stand_id"]; ?>"></input>
			<div class="text-center"> 
				<button class="btn btn-primary" type="submit">Export Report</button>
			</div>
		</form>