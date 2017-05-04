<?php
	$customheader = '<title>Add Organization</title>';
	include('php/header.php');
?>

<h2>Add Organization</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="php/insert_organization.php">  
	<div class="form-group required">
		<label class="control-label">Organization Name</label>
		<input class="form-control" name="oname" id="oname" pattern="[a-zA-Z0-9-'` ]{1,75}" title="Only letters, hypthens (-), apostrophes ('), numbers, grave accents (`), and spaces" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Street Address</label>
		<input class="form-control" name="addr" id="addr" pattern="[a-zA-Z0-9-'` ]{1,100}" title="Only letters, hypthens (-), apostrophes ('), numbers, grave accents (`), and spaces" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">City</label>
		<input class="form-control" name="city" id="city" pattern="[a-zA-Z0-9-'` ]{1,60}" title="Only letters, hypthens (-), apostrophes ('), numbers, grave accents (`), and spaces" required></input>
	</div>
	<div class="form-group">
		<label class="control-label">State (if in the United States)</label>
		<input class="form-control" name="usstate" id="usstate" pattern="[A-Z]{2}" title="Only 2 upper case letters"></input>
	</div>
	<div class="form-group">
		<label class="control-label">Province (if outside the United States)</label>
		<input class="form-control" name="otherstate" id="otherstate" pattern="[A-Z]{2,5}" title="Only upper case letters"></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Zip Code</label>
		<input class="form-control" name="zip" id="zip" pattern="[a-zA-Z0-9-]{1,15}" title="Only letters, hypthens (-) and numbers" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Country</label>
		<input class="form-control" name="country" id="country" pattern="[A-Z]{3}" title="Three character Country abbreviation and only upper case letters" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Parent Organization (N/A if none)</label>
		<select class="form-control" name="orgid" id="orgid" required>
            <?php                     
				$orgresult= get_orgs($_SESSION["user_id"], $conn);
				echo '<option value=""></option>';
				echo '<option value="">N/A</option>';
				while ($orgrow=mysqli_fetch_assoc($orgresult)) {
					$orgTitle=$orgrow["org_name"];
					$orgnid=$orgrow["org_id"];
						echo "<option value= $orgnid>$orgTitle</option>";
				}               
            ?>
        </select>
	</div>
	<?php
		if (strpos($_SESSION["username"], "g2-ops.com") != NULL) {
			echo '
			<div class="form-group required" id="man-dropdown" style="display: none">
				<label class="control-label">Managed Service Provider</label>
				<select class="form-control" name="manid" id="manid" required>
					<option value=""></option>
					<option value="N">No</option>
					<option value="Y">Yes</option>
				</select>
			</div>';
		}
	?>
	<div class="text-center"> 
		<button class="btn btn-primary" type="submit">Submit</button>
	</div>
</form>


<?php
	include('php/footer.php');
?>


