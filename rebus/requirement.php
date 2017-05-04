<?php
	$customheader = '
		<script src="scripts/jquery.AreYouSure-master/jquery.are-you-sure.js"></script>
		<script src="scripts/jquery.AreYouSure-master/ays-beforeunload-shim.js"></script>
		<script>
			$(function() {
				$("#data").areYouSure(
					{
						message: "Are you sure you which to leave this page? Your result information will be lost."
					}
				);
				$("form").bind("dirty.areYouSure", function() {
					$(this).find("button").removeAttr("disabled");
				});
				$("form").bind("clean.areYouSure", function() {
					$(this).find("button").attr("disabled", "disabled");
				});
			});
		</script>
		<title>Requirement Detail View</title>';
	include('php/header.php');
	$_SESSION["sys"] = $_GET["sys"];
	$_SESSION["req"] = $_GET["req"];
	$_SESSION["stand"] = $_GET["stand"];

	// Set system session variable
	$stmt = $conn->prepare('SET @sys = ?');
	$stmt->bind_param("i", $_GET["sys"]);
	$stmt->execute();
	
	// Regather basic identifying information of the requirement
	$stmt = $conn->prepare('SELECT stand_name, stand_version_rev_num, standcat_num, cat_name, req_num, req_desc, req_simple_desc FROM v_standard WHERE req_id = ?');
	$stmt->bind_param("i", $_GET["req"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$reqrow = $result->fetch_assoc();


	// Gather each time this requirement was run
	$stmt = $conn->prepare('SELECT sysreq_id, sysreq_add_date FROM v_system_requirement WHERE req_id = ? ORDER BY sysreq_id DESC');
	$stmt->bind_param("i", $_GET["req"]);
	$stmt->execute();
	$sysreqresult = $stmt->get_result();

	// Gather results; potentially multiple entries for system-requirement pair
	while ($row = $sysreqresult->fetch_assoc()) {
		$sysreqs[] = array("id"=>$row["sysreq_id"], "date"=>$row["sysreq_add_date"]);
	}

	if ($sysreqresult->num_rows > 0) {
		
		// Gather result details (default to most recent)
		$stmt = $conn->prepare('SELECT sysreq_notes, rate_id, range_id, range_desc, art_id, art_text FROM v_system_requirement WHERE sysreq_id = ?');
		$stmt->bind_param("i", $sysreq);
		$sysreq = $sysreqs[0]["id"];
		$stmt->execute();
		$result = $stmt->get_result();
		$selectedrow = $result->fetch_assoc();	
	}

	// Gather ratings for standard
	$stmt = $conn->prepare('SELECT rate_id, rate_abbv, rate_name FROM rating WHERE rate_root_stand = ? OR rate_root_stand = (SELECT stand_root FROM standard WHERE stand_id = ?)');
	$stmt->bind_param("ii", $_GET["stand"], $_GET["stand"]);
	$stmt->execute();
	$rateresult = $stmt->get_result();
	
	mysqli_close($conn);
	
	// Begin Details row and "changeable details" div
	echo '
	<h2>Requirement Details: '.$reqrow["standcat_num"].'.'.$reqrow["req_num"].'</h2>
	<div id="details_row" class="row">
		<div id="changeable_details" class="col-md-6">';
		echo '
			<label>Result Date</label>
			<select id="sysreq_dropdown">';
		// Dropdown of results that will populate with corresponding date's result information when clicked
		foreach ($sysreqs as $sysreq=>$array) {
			echo '
				<option value = "'.$array["id"].'">'.$array["date"].'</option>';
		}
		echo '
			</select>
			<form action = "php/insert_requirement.php" method ="post" id="data">';
		
		// Create Rating and Range dropdown
		echo '
			<div class="form-group required">
				<label class="control-label">Rating</label>
				<select name="rate_dropdown" id="rate_dropdown" class="form-control">';
					while($row = $rateresult->fetch_assoc())  {
						$option = '<option value="'.$row["rate_id"].'"';
						if ($row["rate_id"] == $selectedrow["rate_id"]) {
							$option .= ' selected';
						}  
						$option .= '>'.$row["rate_abbv"].' -- '.$row["rate_name"].'</option><br/>';
						echo $option;
					}
		echo '
				</select>
			</div>
			<div class="form-group">
				<label class="control-label">Range</label>
				<select name="range_dropdown" id="range_dropdown" class="form-control">
					<option value="NULL">No Range Set</option>';
					if (!empty($selectedrow["range_id"])) {
						$ranges = array(1=>"Short-Term: 0-6 Months", 2=>"Mid-Term: 6-12 Months", 3=>"Long-Term: 12+ Months");
						foreach($ranges as $rangeid=>$text) {
							$option = ' <option value='.$rangeid;
							if ($rangeid == $selectedrow["rate_id"]) {
								$option .= ' selected';
							}
							$option .= '>'.$text.'</option>';
							echo $option;
						}
					}
					else {
						echo '					
					<option value=1>Short-Term: 0-6 Months</option>
					<option value=2>Mid-Term: 6-12 Months</option>
					<option value=3>Long-Term: 12+ Months</option>';
					}
		// Create Notes and Artifact Text Areas			
		echo '
				</select>
			</div>
			<div class="form-group required">
				<label class="control-label">Notes</label>
				<textarea id="sysreq_notes" name="sysreq_notes" class="form-control">'.$selectedrow["sysreq_notes"].'</textarea>
			</div>
			<div class="form-group required">
				<label class="control-label">Artifact</label>
				<textarea id="artifact_text" name="artifact_text" class="form-control">'.$selectedrow["art_text"].'</textarea>
			</div>';
		
	// End "changeable_details" div, static details div, end details row
	echo '
				<div class="text-center">	
					<button class="btn btn-primary" type="submit" disabled>Insert New Result</button>
				</div>
			</form>
		</div>
		<div id="static_details" class="col-md-6">
			<div><strong>Category: </strong>'.$reqrow["cat_name"].'</div></br>
			<div id="simple_desc"><strong>Simple Description: </strong>'.$reqrow["req_simple_desc"].'</div></br>
			<div id="full_desc"><strong>Full Description: </strong>'.$reqrow["req_desc"].'</div>
		</div>
	</div>';
		
	include('php/footer.php');
?>