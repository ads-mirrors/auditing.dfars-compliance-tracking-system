<?php
	$customheader = '<title>Import Results from CSV File to MySQL</title>';
	include('php/header.php');
?>
	<h2>Import Results from CSV File to MySQL</h2>
            <form enctype="multipart/form-data" method="post" action="php/sysreq_import.php" class="text-center">
                <div class="form-group">
                    <label for="file">Select .CSV file to Import</label>
                    <center><input name="file" type="file"></center>
                </div>
				<div class="form-group">
					<input type="checkbox" name="headercheck">Does your file have headers?</input>
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
<?php
	include('php/footer.php');
?>