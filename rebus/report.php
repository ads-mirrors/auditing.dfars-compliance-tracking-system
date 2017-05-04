<?php
	$customheader = '<title>Report</title>';
	include('php/header.php');
?>
	<h2>Report</h2>

	<!-- Search Form -->
	<div class="row">
		<div class="col-md-6">
			<fieldset id ="basic_search">
				<legend>Basic Search</legend>
				<div id="step1"></div>
				<div id="step2"></div>
			</fieldset>
		</div>
		<div class="col-md-6" id ="adv_search"></div>
	</div>
	<div class="text-center">
		<button type="submit" name="submit" id="submit" class="btn btn-primary">Create Report</button>
	</div>
	<div id="report"></div>
<?php
	$customfooter = '
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script>var id = '.$_SESSION["user_id"].';</script>
		<script src="scripts/rep_scripts.js"></script>';
	include('php/footer.php');
?>