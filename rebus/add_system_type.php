<?php
$customheader ='<title>Add System Type</title>';
include('php/header.php');

if (!strpos($_SESSION["username"], "g2-opts.com")) {
	$_SESSION["message"] = 'You are not authorized to view that page.';
	header('Location: index.php');
	exit;
}
?>

<h2>Add System Type</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="php/insert_system_type.php">  
	<div class="form-group required">
		<label class="control-label">Type of System</label>
		<input class="form-control" name="typename" id="typeame" pattern="[a-zA-Z0-9-_ ]{1,75}" title="Only letters, hypthens (-), numbers, underscores (_), and spaces"></input>
	</div>
	<div class="text-center"> 
		<button class="btn btn-primary" type="submit">Submit</button>
	</div>  
</form>

<?php
	include('php/footer.php');
?>


