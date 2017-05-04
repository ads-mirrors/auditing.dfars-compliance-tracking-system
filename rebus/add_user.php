<?php
	$customheader = '
		<title>Add User</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script>
			$(document).on("change", "#orgid", function() {
				if ($("#orgid").val() == "") {
					$("#manid").selectedIndex=0;
					$("#manid").prop("disabled", true);
				}
				else {
					var org_id = $("#orgid").val();
					$("#manid").prop("disabled", false);
					$.ajax({
						url: "php/get_managers.php",
						type: "POST",
						data: ({org_id: org_id}),
						success: function (data) {
							$("#manid").html(data);
						},
						error: function(data) {
							$("#manid").html("<option value=\"\">NA</option>");
						}
					});
					$("#manid").selectedIndex=0;
				}
			});
		</script>';
	include('php/header.php');
?>

<h2>Add User</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="php/insert_user.php">  
	<div class="form-group required">
		<label class="control-label">First Name</label>
		<input class="form-control" name="first" id="first" pattern="[a-zA-Z-'` ]{1,60}" title="Only letters, hypthens (-), apostrophes ('), grave accents (`), and spaces" required></input>
	</div>
	<div class="form-group">
		<label class="control-label">Middle Name</label>
		<input class="form-control" name="middle" id="middle" pattern="[a-zA-Z-'` ]{1,60}" title="Only letters, hypthens (-), apostrophes ('), grave accents (`), and spaces"></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Last Name</label>
		<input class="form-control" name="last" id="last" pattern="[a-zA-Z-'` ]{1,60}" title="Only letters, hypthens (-), apostrophes ('), grave accents (`), and spaces" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Country Code</label>
		<input class="form-control" name="country" id="country" pattern="[A-Z]{3}" title="Three character Country abbreviation and only upper case letters" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Phone Number</label>
		<input class="form-control" type="tel" name="pnum" id="pnum" pattern="[0-9]{1,20}" title="Please enter only the phone number without spaces or dashes" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">E-mail</label>
		<input class="form-control" type="email" name="email" id="email" pattern="[^ @]*@[^ @]*" title="Please provide a valid email address" required></input>
	</div>
	<div class="form-group required">
		<label class="control-label">Organization</label>
		<select class="form-control" name="orgid" id="orgid" required>
            <?php                     
				$orgresult= get_orgs($_SESSION["user_id"], $conn);
				echo '<option value=""></option>';            
				while ($orgrow=mysqli_fetch_assoc($orgresult)) {
					$orgTitle=$orgrow["org_name"];
					$orgnid=$orgrow["org_id"];
					echo "<option value= $orgnid>$orgTitle</option>";
				}               
            ?>
        </select>
	</div>
	<div class="form-group required">
		<label class="control-label">Manager (N/A if none)</label>
		<select class="form-control" id="manid" name="manid" required>
			<option value=""></option>
			<?php
                     
            $manquery="SELECT user_id, CONCAT(user_lname,', ',user_fname) name FROM user";
            $manresult= $conn->query($manquery) or die ("Query to get data from firsttable failed: ".mysql_error());

            echo '
				<option value="">N/A</option>'; 
            while ($manrow=mysqli_fetch_array($manresult)) {
            $manTitle=$manrow["name"];
            $mangid=$manrow["user_id"];
                echo "<option value = $mangid>
                    $manTitle
                </option>";
            }   
            ?>
		</select>
	</div>
	<div class="text-center"> 
		<input class="btn btn-primary" type="submit" value="Submit">
	</div>
</form>

<?php
	include('php/footer.php');
?>


