<?php
include('php_files_header.php');

// Headers to enable download of a .csv file
header('Content-Type: text/csv; charset=utf-8');  
header('Content-Disposition: attachment; filename=data.csv');  
$output = fopen("php://output", "w");

// Headers for .csv file
fputcsv($output, array('Organization', 'System', 'Standard', 'Version Number', 'Category Number', 'Requirement Number', 'Artifact', 'Comment', 'Rating', 'Range'));  

// Set system session variable
$stmt = $conn->prepare('SET @sys = ?');
$stmt->bind_param("i", $sysid);
$sysid = $_POST["sys"];
$stmt->execute();

// Set user session variable
$stmt = $conn->prepare('SET @user = ?');
$stmt->bind_param("i", $userid);
$userid = $_SESSION['user_id'];
$stmt->execute();
	
/* Retrieve export data */  
$stmt = 
	"SELECT DISTINCT org_name, sys_name, stand_name, stand_version_rev_num, standcat_num, req_num, art_text, sysreq_notes, rate_name, range_desc
		FROM v_system_requirement sr
        JOIN v_user_reference USING (sys_id)
        JOIN v_standard USING (req_id)
        WHERE stand_id = ".$_POST["stand"];

// Save export data to this file, which then is downloaded when this file ends
if ($result = mysqli_query($conn, $stmt)) {
	      while($row = mysqli_fetch_assoc($result))  
      {  
           fputcsv($output, $row);  
	  }
}

// Error message
else {
	print_r($_POST);
	$_SESSION["message"] = "There was a problem exporting this report. Check with your administrator for assistance. ".mysqli_error($conn)."</br>".$stmt."</br>".$post;
	$conn->close();
	fclose($output); 
	//header('Location: ../report.php');
	//exit;
}

// Close connection and file stream
$conn->close();
fclose($output); 
?>