<?php 
include('php_files_header.php');

$stmt = $conn->prepare("SET @user = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

if($_POST["chart"] == "line") {
	// Collect range descriptions and count for each for all user's associated systems
	$stmt = $conn->prepare("SELECT IFNULL(range_desc, 'No Range') range_desc, COUNT(*) requirements FROM system_requirement LEFT JOIN range_time USING (range_id) WHERE sys_id IN (SELECT sys_id from v_user_reference) GROUP BY range_desc ORDER BY range_id");
	$stmt->execute();
	$result = $stmt->get_result();

	$conn->close();

	$table = array();
	$table['cols'][] = array('id' => '', 'label' => 'Range', 'type' => 'string');
	$table['cols'][] = array('id' => '', 'label' => 'Requirements', 'type' => 'number'); 

	$rows = array();
	foreach($result as $row){
		$temp = array();
		
		//Values
		$temp[] = array('v' => (string) $row['range_desc']);
		$temp[] = array('v' => (float) $row['requirements']); 
		$rows[] = array('c' => $temp);
	}
}

else if($_POST["chart"] == "col") {
	// Collect Rating descriptions and count for each for all user's associated systems
	$stmt = $conn->prepare("SELECT rate_name, COUNT(*) ratings FROM system_requirement JOIN rating USING (rate_id) WHERE sys_id IN (SELECT sys_id from v_user_reference) GROUP BY rate_desc ORDER BY rate_id");
	$stmt->execute();
	$result = $stmt->get_result();

	$conn->close();

	$table = array();
	$table['cols'][] = array('id' => '', 'label' => 'Rating', 'type' => 'string');
	$table['cols'][] = array('id' => '', 'label' => 'Requirements', 'type' => 'number'); 

	$rows = array();
	foreach($result as $row){
		$temp = array();
		
		//Values
		$temp[] = array('v' => (string) $row['rate_name']);
		$temp[] = array('v' => (float) $row['ratings']); 
		$rows[] = array('c' => $temp);
	}
}

else if($_POST["chart"] == "table") {
	// Collect Rated, Non-Rated, and Range counts for associated organization(s) for select standard
	$stmt = $conn->prepare("SELECT org_name, sys_name, count(req_id) total, COUNT(sysreq_id) rated, COUNT(req_id)-COUNT(sysreq_id) nonrated,
							COUNT(CASE WHEN range_id=1 THEN 1 END) shortrange, COUNT(CASE WHEN range_id=2 THEN 1 END) mediumrange,
							COUNT(CASE WHEN range_id=3 THEN 1 END) longrange, COUNT(CASE WHEN range_id IS NULL THEN 1 END) norange 
							FROM v_standard CROSS JOIN (SELECT org_name, sys_name, sys_id FROM v_user_reference WHERE sys_id IS NOT NULL) s 
							LEFT JOIN system_requirement USING (req_id, sys_id) WHERE stand_id=(SELECT MAX(stand_id) from standard WHERE stand_name='stand1') GROUP BY org_name, sys_name ORDER BY org_name, sys_name");
	//$stmt->bind_param("i", $_POST["stand_id"]);
	$stmt->execute();
	$result = $stmt->get_result();

	$conn->close();

	$table = array();
	$table['cols'][] = array('id' => '', 'label' => 'Organization', 'type' => 'string');
	$table['cols'][] = array('id' => '', 'label' => 'System', 'type' => 'string'); 
	$table['cols'][] = array('id' => '', 'label' => 'Total', 'type' => 'number'); 
	$table['cols'][] = array('id' => '', 'label' => 'Rated', 'type' => 'number');
	$table['cols'][] = array('id' => '', 'label' => 'Short Range', 'type' => 'number'); 
	$table['cols'][] = array('id' => '', 'label' => 'Medium Range', 'type' => 'number'); 
	$table['cols'][] = array('id' => '', 'label' => 'Long Range', 'type' => 'number'); 
	$table['cols'][] = array('id' => '', 'label' => 'No Range', 'type' => 'number'); 	
	
	$rows = array();
	foreach($result as $row){
		$temp = array();
		
		//Values
		$temp[] = array('v' => (string) $row['org_name']);
		$temp[] = array('v' => (string) $row['sys_name']);
		$temp[] = array('v' => (float) $row['total']); 		
		$temp[] = array('v' => (float) $row['rated']); 
		$temp[] = array('v' => (float) $row['nonrated']); 
		$temp[] = array('v' => (float) $row['shortrange']); 
		$temp[] = array('v' => (float) $row['mediumrange']); 
		$temp[] = array('v' => (float) $row['longrange']); 
		$temp[] = array('v' => (float) $row['norange']); 
		$rows[] = array('c' => $temp);
	}	
}

$result->free();

$table['rows'] = $rows;
$jsonTable = json_encode($table, JSON_PRETTY_PRINT);
echo $jsonTable;
?>