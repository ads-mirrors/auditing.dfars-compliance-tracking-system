<?php
	$customheader = '
    <title>G2 Ops Home</title>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
    
    // Load the Visualization API and the piechart package.
    google.charts.load("current", {"packages":["corechart", "table"]});
      
    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChartLoad);
      
  function drawChartLoad() {

    	<!-- Range Line Chart -->
	var jsonRangeData = $.ajax({
      url: "php/getData.php",
	type: "POST",
	data: ({
			sys: "",
			org: "",
			chart: "line"}),
      dataType:"json",
      async: false
    }).responseText;

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonRangeData);

    var rangeOptions = {
        hAxis: {
          title: "Range"
        },
        vAxis: {
          title: "Requirements",
		  format: "0",
		  gridlines: { count: -1},
		  minValue: 0
        }
      };

    var linechart = new google.visualization.LineChart(document.getElementById("linechart"));
    linechart.draw(data, rangeOptions);
	
	<!-- Systems-Ratings Column Chart -->
	var jsonSysRatData = $.ajax({
      url: "php/getData.php",
	type: "POST",
	data: ({
		   sys: "",
		   org: "",
		   chart: "col"}),
      dataType:"json",
      async: false
    }).responseText;

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonSysRatData);

    var sysRatOptions = {
        hAxis: {
          title: "Range"
        },
        vAxis: {
          title: "Requirements",
		  format: "0",
		  gridlines: { count: -1},
		  minValue: 0
        }
      };

	var colchart = new google.visualization.ColumnChart(document.getElementById("colchart"));
    colchart.draw(data, sysRatOptions);

	<!-- Organization-Ratings Table -->
	var jsonOrgRatData = $.ajax({
      url: "php/getData.php",
	type: "POST",
	data: ({
		   sys: "",
		   org: "",
		   chart: "table"}),
      dataType:"json",
      async: false
    }).responseText;

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonOrgRatData);

    var orgRatOptions = {
        hAxis: {
          title: "Range"
        },
        vAxis: {
          title: "Requirements",
		  format: "0",
		  gridlines: { count: -1},
		  minValue: 0
        },
		showRowNumber: true, width: "100%"
      };

      var table = new google.visualization.Table(document.getElementById("table"));
      table.draw(data, orgRatOptions);
	}

    </script>';

	include('php/header.php');
	
/* Standard dropdown for filtering table results
	$sql = 'SELECT DISTINCT stand_name, stand_version_rev_num, stand_id FROM v_standard WHERE stand_version_rev_num IN (SELECT max(stand_version_rev_num) from standard GROUP BY stand_name)';
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	mysqli_close($conn);
	
	if (mysqli_num_rows($result) > 0) {
		// output standard data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$values["standard"][] = $row["stand_name"];
			$values["standrev"][] = $row["stand_version_rev_num"];
			$values["standid"][] = $row["stand_id"];
		}
		
		echo '
			<label>Standard</label>
			<select name="stand_dropdown" id="stand_dropdown">';
				for( $i = 0; $i<sizeof($values["standard"]); $i++ )  {
					echo '<option value="'.$values["standid"][$i].'">'.$values["standard"][$i].' ver. '.$values["standrev"][$i].'</option><br/>';
				}
				echo '
			</select>
		</fieldset></br>';			
	}
	else {
		exit('Standards could not be retrieved. Contact your administrator for assistance.');
	}*/	
?>
	<h2>G2 Ops Reporting System Dashboard</h2>
	<div class="row"> 
		<div class="col-md-6">
			<h3>Total Ranges for all Your Systems</h3>
			<div class="chart" id="linechart"></div>
		</div>
		<div class="col-md-6">
			<h3>Total Ratings for all Your Systems</h3>
			<div class ="chart"	id="colchart"></div>
		</div>
	</div></br>

	<h3>Ratings and Range Breakdown for Stand1</h3>
	<div id="table"></div>

<?php	
	include('php/footer.php');
?>