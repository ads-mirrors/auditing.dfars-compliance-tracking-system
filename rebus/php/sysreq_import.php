<?php
include('php_files_header.php');

/* Holds the path on the user's computer and the file name of the file being imported.  Also makes sure the file is CSV */
if (isset($_POST['submit'])) {
    $allowed = array('csv');
    $filename = $_FILES['file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
        // show error message
        $_SESSION["message"] = 'Invalid file type, please use .CSV file!';
		header('Location: ../sysreq_import_page.php');
		exit;
    } 
	else {

		// If it is a CSV file, it is uploaded to the files folder on the server  
        move_uploaded_file($_FILES["file"]["tmp_name"], "files/" . $_FILES['file']['name']);
        $file = "files/" . $_FILES['file']['name'];

		/* Runs the MySQL load data local infile process using the file placed in the files folder that the user uploaded */  
		// Ignore headers if user indicates they exist 
		if (isset($_POST["headercheck"])) {
			$stmt = 
				"LOAD DATA LOCAL INFILE '$file'
				INTO TABLE result_import
				FIELDS TERMINATED BY ','
				LINES TERMINATED BY '\n'
				IGNORE 1 LINES
				(orgname,sysname,standname,standver,catnum,reqnum,artifact,note,ratename,rangename)";

		}
		else {
			$stmt = 
				"LOAD DATA LOCAL INFILE '$file'
				INTO TABLE result_import
				FIELDS TERMINATED BY ','
				LINES TERMINATED BY '\n'
				(orgname,sysname,standname,standver,catnum,reqnum,artifact,note,ratename,rangename)";
		}
		
		// If unsuccessful then the user is notified
		if (!mysqli_query($conn, $stmt)) {
			$_SESSION["message"] = 'There was a problem importing your CSV. Check that it is formatted correctly.';
			header('Location: ../sysreq_import_page.php');
			exit;
		}
	}
}

/* Updates the user_name column for all new rows in the requirement import table with the user name of the person who is uploading the CSV file */ 
$sql = "UPDATE result_import SET username=?  WHERE username IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user);
$user = $_SESSION['username'];
$stmt->execute();
$stmt->close();

/* Runs the insert requirements procedure that inserts all new rows from the requirement import table into the main requirement table */
$stmt = "CALL results_import()";
$stmt = $conn->prepare($stmt);
$stmt->execute();

// Error control
if (!$conn->errno) {
	$_SESSION["message"] = 'Report has imported successfully';
}
else if ($conn->errno == 1062) {
	$_SESSION["message"] = 'This standard\'s version already exists.';
}
else {
	$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.'.$conn->error;
}

// Closes all statements and the DB connection
$stmt->close();
$conn->close();

header('Location: ../sysreq_import_page.php');
exit;
		
// Deletes the CSV file the user uploaded
unlink($file);

?>