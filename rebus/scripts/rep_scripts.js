var org_id ="";
var org_name = "";
var sys_id = "";
var sys_name = "";
var stand_id = "";
var stand_rev = "";

function search_basic_org() {
	// Render Organizations
	$.ajax({
		url: "php/search_basic.php",
		type: "POST",
		success: function (data){
			$("#step1").html(data);
			// If first character is open tag, no errors, so continue
			if ($.trim(data).charAt(0) == '<'){
				search_basic_rest();
			}
		},
		error: function (data){
			$("#step1").text("Could not communicate with the server. Please contact your administrator for assistance.");
		}
	});
} 

function search_basic_rest() {
	// Render Systems and standards
	org_id = $("#org_dropdown").val();
	$.ajax({
		url: "php/search_basic.php",
		type: "POST",
		data: ({org_id: org_id}),
		success: function (data){
			$("#step2").html(data);
			// If first character is open tag, no errors, so continue
			if ($.trim(data).charAt(0) == '<'){
				adv_search();
			}
		},
		error: function (data){
			$("#step2").text("Could not communicate with the server. Please contact your administrator for assistance.");
		}
	});
}

function adv_search() {
	org_name = $("#org_dropdown :selected").text();
	sys_id = $("#sys_dropdown").val();
	sys_name = $("#sys_dropdown :selected").text();
	stand_id = $("#stand_dropdown").val();
	$.ajax({
		url: "php/search_advanced.php",
		type: "POST",
		data: ({org_id: org_id,
				org_name: org_name,
				sys_id: sys_id,
				sys_name: sys_name,
				stand_id: stand_id}),
		success: function (data) {
			$("#adv_search").html(data);
		},
		error: function(data) {
			$("#adv_search").text("Could not communicate with the server. Please contact your administrator for assistance.");
		}
	});	
}

function report() {
	org_name = $("#org_dropdown :selected").text();
	sys_id = $("#sys_dropdown").val();
	sys_name = $("#sys_dropdown :selected").text();
	stand_id = $("#stand_dropdown").val();
	stand_rev = $("#stand_dropdown :selected").text();
	var standcat_id = $("#cat_dropdown").val();
	var rate_id = $("#rate_dropdown").val();
	var range_id = $("#range_dropdown").val();
		
	$.ajax({
		url: "php/create_report.php",
		type: "POST",
		data: ({user: id,
				org_id: org_id,
				org_name: org_name,
				sys_id: sys_id,
				sys_name: sys_name,
				stand_id: stand_id,
				stand_rev: stand_rev,
				standcat_id: standcat_id,
				rate_id: rate_id,
				range_id: range_id}),
		success: function (data) {
			$("#report").html(data);
		},
		error: function(data) {
			$("#report").text("Could not communicate with the server. Please contact your administrator for assistance.");
		}
	});	
}

$(document).ready(search_basic_org());

$(document).on("change", "#org_dropdown", function() {
	$("#step1").html($("#org_search"));
	$("#adv_search").html("");
	search_basic_rest();
});

$(document).on("change", "#stand_dropdown", function() {
	adv_search();
});

$(document).on("change", "#rate_dropdown", function() {
	if ($("#rate_dropdown").val() == "") {
		$("#range_dropdown").val("");
		$("#range_dropdown").prop('disabled', true);
	}
	else {
		$("#range_dropdown").prop('disabled', false);
		$("#range_dropdown").selectedIndex=0;
	}
});

$(document).on("click", "#submit", function() {
	report();
});