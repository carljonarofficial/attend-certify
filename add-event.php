<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Validate Add Event Session
    $eventcrud_msg = "";
    session_start();
    if (isset($_SESSION["event-crud-validation-msg"])) {
        $eventcrud_msg = $_SESSION["event-crud-validation-msg"];
    }
    unset($_SESSION["event-crud-validation-msg"]);
    session_write_close();

    // If the admin submits form, then it will proceed here
    use Eventpot\EventMember;
    if (! empty($_POST["addevent-btn"])) {
	    require_once './model/event-member.php';
	    $member = new EventMember();
	    $addResponse = $member->addEvent();
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Add Event | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
</head>
<body style="background-image: url('style/img/background_add.jpeg')"  class="d-flex flex-column" onload="defaultDate()">
	<?php 
        // Initialize Active Page for Navbar Highlight
        $activePage = "events";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>

	<!-- Main -->
	<div class="main-body container-fluid flex-grow-1 mt-5">
		<div class="container-fluid px-2 pt-2">
			<!-- Title Tab -->
			<div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold">ADD EVENT</h1>
                <h2 class="pl-3 font-weight-normal">Please fill up the following details below.</h2>
            </div>
            <!-- Validate if add event is failed -->
            <?php
            	if (!empty($eventcrud_msg)) { ?>
            		<div class="w-100 p-3 mt-3 shadow-sm rounded bg-danger text-light response">
	                    <h4>ADD EVENT FAILED</h4>
	                </div>
            	<?php }
            ?>
            <!-- Cancel Add Event Button -->
            <div class="mt-3">
                <a href="events.php" class="ml-3 h4 btn btn-danger btn-lg-add-event rounded-pill"><i class="fa fa-ban"></i> CANCEL</a>
            </div>
            <div class="container shadow-sm p-3 my-2 border-form-override">
            	<!-- Add Event Form -->
            	<form name="add-event" action="" method="post" enctype="multipart/form-data" onsubmit="return addEventValidation()" class="justify-content-center">
					<div class="row">
                        <div class="col-sm-6">
                        	<div class="form-group">
			                    <label for="title" class="label-add-edit-event">
			                    	Title: <span class="required error" id="title-info"></span>
			                	</label>
			                    <input type="text" class="form-control" name="title" id="title" placeholder="Title">
			                </div>
					 		<div class="form-group">
			                    <label for="eventDate" class="label-add-edit-event">
		                    		Date: <span class="required error" id="date-info"></span>
		                    	</label>
			                    <input type="date" class="form-control col-sm-7" name="eventDate" id="eventDate" value="2021-01-01">
			                </div>
			                <div class="form-group">
			                	<label for="time" class="label-add-edit-event">Time:</label>
				                <div class="col-sm-6 p-2 time-event">
					                <div class="form-group">
					                    <label for="inclusiveTime" class="label-add-edit-event">
					                    	From: <span class="required error" id="inclusiveTime-info"></span>
					                    </label>
					                    <input type="time" class="form-control event-time w-100" name="inclusiveTime" id="inclusiveTime" value="00:00" onchange="timeValidationInclusive()" oninput="timeValidationInclusive()">
					                </div>
					                <div class="form-group">
					                	<label for="conclusiveTime" class="label-add-edit-event">
					                		To: <span class="required error" id="conclusiveTime-info"></span>
					                	</label>
					                	<input type="time" class="form-control event-time w-100" name="conclusiveTime" id="conclusiveTime" value="01:00" onchange="timeValidationConclusive()" oninput="timeValidationConclusive()">
					                </div>
				                </div>
			                </div>
			                <div class="form-group">
			                    <label for="venue" class="label-add-edit-event">
			                    	Venue: <span class="required error" id="venue-info"></span>
			                    </label>
			                    <input type="text" class="form-control" name="venue" id="venue" placeholder="Venue">
			                </div>
                        </div>
                        <div class="col-sm-6">
                        	<div class="form-group">
			                    <label for="description" class="label-add-edit-event">
			                    	Description:  <span class="required error" id="description-info"></span>
			                    </label>
			                    <textarea class="form-control" rows="3" name="description" id="description" placeholder="Description"></textarea>
			                </div>
			                <div class="form-group">
			                    <label for="agenda" class="label-add-edit-event">
			                    	Agenda:  <span class="required error" id="agenda-info"></span>
			                    </label>
			                    <textarea class="form-control" rows="3" name="agenda" id="agenda" placeholder="Agenda"></textarea>
			                </div>
			                <div class="form-group">
			                    <label for="theme" class="label-add-edit-event">
		                    		Theme:  <span class="required error" id="theme-info"></span>
			                    </label>
			                    <input type="text" class="form-control" name="theme" id="theme" placeholder="Theme">
			                </div>
			                <div class="form-group">
			                    <label for="cert-attachment" class="label-add-edit-event">Certificate Template Attachment</label>
			                    <input type="file" name="certAttachment" id="certAttachment">
			                </div>
			                <div class="form-group text-center">
			                	<button type="submit" name="addevent-btn" id="addevent-btn" value="Add Event" class="btn btn-success btn-lg rounded-pill"><i class="fa fa-plus-circle"></i> Add Event</button>
			                </div>
                        </div>
                    </div>
            	</form>
            </div>
		</div>
	</div>
	<?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
	<!-- Scripts -->
	<script>
		// Default date value
		function defaultDate() {
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; //January is 0!
			var yyyy = today.getFullYear();

			if(dd<10) {
			    dd = '0'+ dd;
		  	} 
			if(mm<10) {
			    mm = '0'+ mm;
			} 
			today = yyyy + '-' + mm + '-' + dd;

			document.getElementById("eventDate").defaultValue = today;
		}
		// Time input validation for inclusive
		function timeValidationInclusive() {
			var time = document.getElementById("inclusiveTime").value;
			var timeConclusive = document.getElementById("conclusiveTime").value;
			var arrTime = time.split(":").join("");
			if(arrTime != "2359") {
				if(time == ""){
					document.getElementById("inclusiveTime").value = subtractMinutes(timeConclusive, '1');
				}else{
					document.getElementById("conclusiveTime").value = addMinutes(time, '1');	
				}
			}else {
				document.getElementById("inclusiveTime").value = "23:58";
			}
		}
		// Time inpu validation for conclusive
		function timeValidationConclusive(){
			var time = document.getElementById("conclusiveTime").value;
			var timeInclusive = document.getElementById("inclusiveTime").value;
			var arrTime = time.split(":").join("");
			if(arrTime != "0000"){
				if(time > timeInclusive){
					//nothing
				}else{
					document.getElementById("conclusiveTime").value = addMinutes(timeInclusive, '1');
				}
			}else{
				document.getElementById("conclusiveTime").value = "00:01";
			}
		}
		// Add Minutes
		function addMinutes(time, minsToAdd) {
		  	function D(J){ 
		  		return (J<10? '0':'') + J
		  	};
		  	var piece = time.split(':');
	  		var mins = piece[0]*60 + + piece[1] + + minsToAdd;
	  		
		  	return D(mins % (24 * 60) / 60 | 0) + ':' + D(mins % 60);  
		}  
		// Subtract Minutes
		function subtractMinutes(time, minsToSubtract) {
		  	function D(J){ 
		  		return (J<10? '0':'') + J
		  	};
		  	var piece = time.split(':');
		  	var mins = piece[0]*60 + + piece[1] - minsToSubtract;

		  	return D(mins % (24 * 60) / 60 | 0) + ':' + D(mins % 60);  
		}  
		// File Upload Script
		$("#certAttachment").fileinput({
	        theme: 'fas',
	        showRemove:true,
	        showUpload: false,
	        showZoom: true,
	        showClose: false,
	        dropZoneEnabled: false,
	        initialPreviewShowDelete: false,
	        allowedFileExtensions: ['docx', 'pdf', 'jpeg', 'jpg', 'png'],
	        required:true
	    }).on('filepreupload', function(event, data, previewId, index) {
	        alert('The description entered is:\n\n' + ($('#description').val() || ' NULL'));
	    });
	    // Add event validation
	    function addEventValidation() {
	    	var valid = true;

	    	$("#title").removeClass("error-field");
			$("#eventDate").removeClass("error-field");
			$("#inclusiveTime").removeClass("error-field");
			$("#conclusiveTime").removeClass("error-field");
			$("#venue").removeClass("error-field");
			$("#description").removeClass("error-field");
			$("#agenda").removeClass("error-field");
			$("#theme").removeClass("error-field");

			var eventTitle = $("#title").val();
			var eventDate = $("#eventDate").val();
			var eventInclusiveTime = $("#inclusiveTime").val().split(":").join("");;
			var eventConclusiveTime = $("#conclusiveTime").val();
			var eventVenue = $("#venue").val();
			var eventDescription = $("#description").val();
			var eventAgenda = $("#agenda").val();
			var eventTheme = $("#theme").val();

			$("#title-info").html("").hide();
			$("#date-info").html("").hide();
			$("#inclusiveTime-info").html("").hide();
			$("#conclusiveTime-info").html("").hide();
			$("#venue-info").html("").hide();
			$("#description-info").html("").hide();
			$("#agenda-info").html("").hide();
			$("#theme-info").html("").hide();

			if(eventTitle.trim() == ""){
				$("#title-info").html("* Required.").css("color", "#ee0000").show();
				$("#title").addClass("error-field");
				valid = false;
			}
			if (eventDate == "") {
				$("#date-info").html("* Required.").css("color", "#ee0000").show();
				$("#eventDate").addClass("error-field");
				valid = false;
			} 
			if (eventVenue.trim() == "") {
				$("#venue-info").html("* Required.").css("color", "#ee0000").show();
				$("#venue").addClass("error-field");
				valid = false;
			} 
			if (eventDescription.trim() == "") {
				$("#description-info").html("* Required.").css("color", "#ee0000").show();
				$("#description").addClass("error-field");
				valid = false;
			} 
			if (eventAgenda.trim() == "") {
				$("#agenda-info").html("* Required.").css("color", "#ee0000").show();
				$("#agenda").addClass("error-field");
				valid = false;
			} 
			if (eventTheme.trim() == "") {
				$("#theme-info").html("* Required.").css("color", "#ee0000").show();
				$("#theme").addClass("error-field");
				valid = false;
			} 
			if (valid == false) {
				$('.error-field').first().focus();
				valid = false;
			}
			return valid;
	    }
	</script>
</body>
</html>