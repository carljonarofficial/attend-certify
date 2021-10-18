<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Validate Edit Event Session
    $eventcrud_msg = "";
    session_start();
    if (isset($_SESSION["event-crud-validation-msg"])) {
        $eventcrud_msg = $_SESSION["event-crud-validation-msg"];
    }
    unset($_SESSION["event-crud-validation-msg"]);
    session_write_close();

    // Using database connection file here
    include 'dbConnection.php';

    // Check if it fetched correctly from the events page
    $editErrorMsg = "error";
    if (isset($_GET['eventID'])) {
    	$eventID = $_GET['eventID'];
    	$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ? AND `status` = 1");
	    $eventStmt->bind_param('ii', $id, $eventID);
	    $eventStmt->execute();
	    $eventInfo =  $eventStmt->get_result();
	    $eventStmt->close();
	    if ($eventInfo->num_rows > 0) {
		    while ($row = $eventInfo->fetch_assoc()) {
	    		$eventID = $row['ID'];
				$eventTitle = $row["event_title"];
				$eventDate = $row["date"];
				$eventTimeInclusive = $row["time_inclusive"];
				$eventTimeConclusive = $row["time_conclusive"];
				$eventVenue = $row["venue"];
				$eventDesciption = $row["description"];
				$eventAgenda = $row["agenda"];
				$eventTheme = $row["theme"];
				$certTemplate = $row["certificate_template"];
				$certTemplateFileSize = filesize("./certificate-templates/".$certTemplate);
			}
			$editErrorMsg = "success";
	    } else {
	    	$editErrorMsg = "error";
	    }
    }else{
		$editErrorMsg = "error";
    }

    // If the admin submits form, then it will proceed here
    use Eventpot\EventMember;
    if (! empty($_POST["editevent-btn"])) {
	    require_once './model/event-member.php';
	    $member = new EventMember();
	    $addResponse = $member->editEvent();
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit Event | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
</head>
<body class="d-flex flex-column">
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
                <h1 class="font-weight-bold">EDIT EVENT</h1>
                <h2 class="pl-3 font-weight-normal">Please edit up the following details below if needed.</h2>
            </div>
            <!-- Validate if edit event is failed -->
            <?php
            	if (!empty($eventcrud_msg)) { ?>
            		<div class="w-100 p-3 mt-3 shadow-sm rounded bg-danger text-light response">
	                    <h4>EDIT EVENT FAILED</h4>
	                </div>
            	<?php }
            ?>
            <!-- Cancel Add Event Button -->
            <div class="mt-3">
                <a href="events.php" class="ml-3 h4 btn btn-danger btn-lg-add-event rounded-pill"><i class="fa fa-ban"></i> CANCEL</a>
            </div>
            <div class="container shadow-sm p-3 my-2 border-form-override">
            	<?php 
            		// Check if fetch is successful
            		if ($editErrorMsg == "success") { ?>
						<!-- Edit Event Form -->
	                	<form name="add-event" action="" method="post" enctype="multipart/form-data" onsubmit="return addEventValidation()" class="justify-content-center">
							<div class="row">
		                        <div class="col-sm-6">
		                        	<input type="hidden" name='eventId' value="<?php echo $eventID; ?>" />
		                        	<div class="form-group">
					                    <label for="title" class="label-add-edit-event">
					                    	Title: <span class="required error" id="title-info"></span>
					                	</label>
					                    <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?php echo $eventTitle;?>">
					                </div>
							 		<div class="form-group">
					                    <label for="eventDate" class="label-add-edit-event">
				                    		Date: <span class="required error" id="date-info"></span>
				                    	</label>
					                    <input type="date" class="form-control col-sm-7" name="eventDate" id="eventDate" value="<?php echo $eventDate;?>">
					                </div>
					                <div class="form-group">
					                	<label for="time" class="label-add-edit-event">Time:</label>
						                <div class="col-sm-6 p-2 time-event">
							                <div class="form-group">
							                    <label for="inclusiveTime" class="label-add-edit-event">
							                    	From: <span class="required error" id="inclusiveTime-info"></span>
							                    </label>
							                    <input type="time" class="form-control event-time w-100" name="inclusiveTime" id="inclusiveTime" value="<?php echo $eventTimeInclusive;?>" onchange="timeValidationInclusive()" oninput="timeValidationInclusive()">
							                </div>
							                <div class="form-group">
							                	<label for="conclusiveTime" class="label-add-edit-event">
							                		To: <span class="required error" id="conclusiveTime-info"></span>
							                	</label>
							                	<input type="time" class="form-control event-time w-100" name="conclusiveTime" id="conclusiveTime" value="<?php echo $eventTimeConclusive;?>" onchange="timeValidationConclusive()" oninput="timeValidationConclusive()">
							                </div>
						                </div>
					                </div>
					                <div class="form-group">
					                    <label for="venue" class="label-add-edit-event">
					                    	Venue: <span class="required error" id="venue-info"></span>
					                    </label>
					                    <input type="text" class="form-control" name="venue" id="venue" placeholder="Venue" value="<?php echo $eventVenue;?>">
					                </div>
		                        </div>
		                        <div class="col-sm-6">
		                        	<div class="form-group">
					                    <label for="description" class="label-add-edit-event">
					                    	Description:  <span class="required error" id="description-info"></span>
					                    </label>
					                    <textarea class="form-control" rows="3" name="description" id="description" placeholder="Description"><?php echo $eventDesciption;?></textarea>
					                </div>
					                <div class="form-group">
					                    <label for="agenda" class="label-add-edit-event">
					                    	Agenda:  <span class="required error" id="agenda-info"></span>
					                    </label>
					                    <textarea class="form-control" rows="3" name="agenda" id="agenda" placeholder="Agenda"><?php echo $eventAgenda;?></textarea>
					                </div>
					                <div class="form-group">
					                    <label for="theme" class="label-add-edit-event">
				                    		Theme:  <span class="required error" id="theme-info"></span>
					                    </label>
					                    <input type="text" class="form-control" name="theme" id="theme" placeholder="Theme"  value="<?php echo $eventTheme;?>">
					                </div>
					                <div class="form-group">
					                    <label for="cert-attachment" class="label-add-edit-event">Certificate Template Attachment</label>
					                    <input type="file" name="certAttachment" id="certAttachment">
					                </div>
					                <div class="form-group text-center">
					                	<button type="submit" name="editevent-btn" id="editevent-btn" value="Edit Event" class="btn btn-success btn-lg rounded-pill"><i class="fas fa-save"></i> Save Event</button>
					                </div>
		                        </div>
		                    </div>
        				</form>
        			<!-- If not, then it will display an error -->
            		<?php } else {?>
            			<!-- If fetched error, then the error message appear. -->
                        <div class="w-100 p-3 mt-3 mb-5 shadow-sm rounded bg-danger text-light response">
                            <h4>Fetch Error, Please try again.</h4>
                        </div>
            		<?php }
            	?>
            </div>
		</div>
	</div>
	<?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
	<!-- Scripts -->
	<script>
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
		// var $js_array = "<?php echo $certTemplate;?>";
		// var $certAttach = "#certAttachment";
  //       var $certFileType = $js_array.substring($js_array.indexOf(".")+1);
  //       var $initialFileSize = "<?php echo $certTemplateFileSize;?>";
		$("#certAttachment").fileinput({
	        theme: 'fas',
	        showClose: false,
	        showRemove:true,
	        showUpload: false,
	        showZoom: true,
	        dropZoneEnabled: false,
	        maxFileSize: 20000,
	        initialPreviewShowDelete: false,
	        allowedFileExtensions: ['pdf'],
	        required:true
	     //    overwriteInitial: false,
	     //    initialPreview: [// PDF DATA
      //           'http://localhost/attend-certify/certificate-templates/' + $js_array
      //       ],
      //       initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
      //       initialPreviewConfig: [
      //       	{
      //       		type: $certFileType,
      //       		size: $initialFileSize,
      //       		caption: $js_array, 
      //       		downloadUrl: 'http://localhost/attend-certify/certificate-templates/' + $js_array        		}
    		// ]
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