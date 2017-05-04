<?php
	include('php_files_header.php');
	
	// Set user session variable
	$stmt = $conn->prepare("SET @user = ?");
	$stmt->bind_param("i", $_SESSION["user_id"]);
	$stmt->execute() or die($conn->error);
	
	// Get the org_id
	$stmt = $conn->prepare("SELECT org_id FROM v_user_reference WHERE sys_id = ?");
	$stmt->bind_param("i", $_SESSION["sys"]);
	$stmt->execute() or die('1: '.$conn->error);
	$stmt->bind_result($org);
	$stmt->fetch();

	// Insert artifact if new
	$stmt = 'INSERT IGNORE INTO artifact(art_text, art_add_uname, org_id) VALUES ("'.$_POST["artifact_text"].'", "'.$_SESSION["username"].'", '.$org.')';
	mysqli_query($conn, $stmt) or die('2: '.$conn->error);
	
	// Find art id just inserted or ignored
	if($conn->affected_rows == 1) {
		$art = $conn->insert_id;
	}
	else {
		$stmt = $conn->prepare("SELECT art_id FROM artifact WHERE art_text = ?");
		$stmt->bind_param("s", $_POST["artifact_text"]);
		$stmt->execute() or die ('4'.$conn->error);
		$stmt->bind_result($art);
		$stmt->fetch();
	}
	
	// Insert result, using id of artifact inserted or skipped above
	$stmt = 'INSERT INTO system_requirement (sys_id, req_id, rate_id, range_id, art_id, sysreq_notes, sysreq_add_uname) VALUES ('.$_SESSION["sys"].', '.$_SESSION["req"].', '
	.$_POST["rate_dropdown"].', '.$_POST["range_dropdown"].', '.$art.', "'.$_POST["sysreq_notes"].'", "'.$_SESSION["username"].'")';
	mysqli_query($conn, $stmt) or die('4: '.$stmt.'!!! '.$conn->error);
	
	
	$_SESSION["message"] = "New result added.";
	header('Location: ../requirement.php?sys='.$_SESSION["sys"].'&req='.$_SESSION["req"].'&stand='.$_SESSION["stand"]);
?>