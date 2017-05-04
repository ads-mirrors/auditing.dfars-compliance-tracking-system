<?php
// This code ensures a session is started and active or you can not acces the page 
include('php_files_header.php');

if (isset($_POST['submit'])) {
	$allowed = array('csv');
    $filename = $_FILES['file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
	if (!in_array($ext, $allowed)) {
        // show error message
        $_SESSION["message"] = 'Invalid file type, please use .CSV file!';
		header('Location: ../standard_import_page.php');
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
				INTO TABLE standard_import
				FIELDS TERMINATED BY ','
				ENCLOSED BY '\"'
				LINES TERMINATED BY '\n'
				IGNORE 1 LINES
				(standname,standver,@date,catnum,catname,reqnum,reqdesc,reqsimple,ratename,ratedesc)
				SET standdate = STR_TO_DATE(@date, '%m/%d/%Y')";

			// If successful then the user is notified
			if ($result = mysqli_query($conn, $stmt)) {
				echo  'CSV file successfully imported!<br>';
			}
			else {
				echo 'There has been a problem with the import. Contact your administrator for assistant.';
			}
		}
	
		else
		{
			$stmt = 
				"LOAD DATA LOCAL INFILE '$file'
				INTO TABLE standard_import
				FIELDS TERMINATED BY ','
				LINES TERMINATED BY '\n'
				(standname,standver,@date,catnum,catname,reqnum,reqdesc,reqsimple)
				SET standdate = STR_TO_DATE(@date, '%m/%d/%Y');";

			// If successful then the user is notified
			if ($result = mysqli_query($conn, $stmt)) {
				echo  'CSV file successfully imported!<br>';	
			}
			else {
				echo 'There has been a problem with the import. Contact your administrator for assistant.';
			}
		}

$sql = "UPDATE standard_import SET username=?  WHERE username IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user);
$user = $_SESSION['username'];
$stmt->execute();
		
		$stmt = $conn->prepare('CALL standard_import()');
		$stmt->execute();
		
		// Error control
		if (!$conn->errno) {
			$_SESSION["message"] = 'Standard has imported successfully';
		}
		else if ($conn->errno == 1062) {
			$_SESSION["message"] = 'This standard\'s version already exists.';
		}
		else {
			$_SESSION["message"] = 'There was an unknown database error. Contact your administrator for assistance.';
		}

		// Closes all statements and the DB connection
		$stmt->close();
		$conn->close();

		header('Location: ../standard_import_page.php');
		exit;
	}
}

?>