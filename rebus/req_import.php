<?php
	$customheader = '<title>Import Requirements from CSV File to MySQL</title>';
	include('php/header.php');
?>
 
	<h2>Import Requirements from CSV File to MySQL</h2>
            <form enctype="multipart/form-data" method="post" action="php/req_import.php" class="text-center">
                <div class="form-group">
                    <label for="file">Select .CSV file to Import</label>
                    <center><input name="file" type="file"></center>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit"/>
                </div>
            </form>
<?php
	include('php/footer.php');
?>