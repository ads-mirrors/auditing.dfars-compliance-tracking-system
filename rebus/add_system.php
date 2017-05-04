<?php
	$customheader = '<title>Add System</title>';
	include('php/header.php');
?>

<h2>Add System</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="php/insert_system.php">  
	<div class="form-group required">
		<label class="control-label">System Name</label>
		<input class="form-control" name="sysname" id="sysname" pattern="[a-zA-Z0-9-_ ]{1,100}" title="Only letters, hypthens (-), numbers, underscores (_), and spaces" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">IP Address</label>
		<input class="form-control" name="ip" id="ip" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" title="Only well formed IP addresses with four octet's consisting of one to three numbers with a range of 0-255 and periods between each octet" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">System Type</label>
		<select class="form-control" name="typeid" id="typeid" required>
            <?php                     
				$typeresult= get_systype($conn);
				echo '<option value=""></option>';            
				while ($typerow=mysqli_fetch_array($typeresult)) {
				$typeTitle=$typerow["type_name"];
				$typeid=$typerow["type_id"];
					echo "<option value = $typeid>$typeTitle</option>";
				}         
            ?>
        </select>
	</div>
	<div class="form-group required">
		<label class="control-label">Associated Organization</label>
		<select class="form-control" name="orgid" id="orgid" required>
            <?php                     
				$orgresult= get_orgs($_SESSION["user_id"], $conn);
				echo '
					<option value=""></option>';
				while ($orgrow=mysqli_fetch_assoc($orgresult)) {
				$orgTitle=$orgrow["org_name"];
				$orgnid=$orgrow["org_id"];
					echo "<option value= $orgnid>$orgTitle</option>";
				}               
            ?>
        </select>
	</div>
	<div class="text-center"> 
		<button class="btn btn-primary" type="submit">Submit</button>
	</div>
</form>
<?php
	include('php/footer.php');
?>