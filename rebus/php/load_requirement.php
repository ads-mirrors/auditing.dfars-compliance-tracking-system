<?php
	include('connect/connect.php');
	
	$stmt = $conn->prepare('SELECT sysreq_notes, rate_id, range_id, range_desc, art_id, art_text FROM v_system_requirement WHERE sysreq_id = ?');
	$stmt->bind_param("i", $_POST["sysreq"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$selectedrow = $result->fetch_assoc();

	// Gather ratings for standard
	$stmt = $conn->prepare('SELECT rate_id, rate_abbv, rate_name FROM rating WHERE rate_root_stand = ? OR rate_root_stand = (SELECT stand_root FROM standard WHERE stand_id = ?)');
	$stmt->bind_param("ii", $_POST["stand"], $_POST["stand"]);
	$stmt->execute();
	$rateresult = $stmt->get_result();

	$conn->close();
	
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
				<textarea id="sysreq_notes" name="sysreq_notes" class="form-control">'.$selectedrow["sysreq_notes"].$_POST["sysreq"].'</textarea>
			</div>
			<div class="form-group required">
				<label class="control-label">Artifact</label>
				<textarea id="artifact_text" name="artifact_text" class="form-control">'.$selectedrow["art_text"].'</textarea>
			</div>';
		
	// End "changeable_details" div, static details div, end details row
	echo '
				<div class="text-center">	
					<button class="btn btn-primary" type="submit" disabled>Insert New Result</button>
				</div>';

?>