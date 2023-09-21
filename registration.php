<?php
	// Initialize Title Page variable
    $eventTitle = "";

    // Using database connection file here
    include 'dbConnection.php';

    // Current Date and Time
    $currentDateTime = date("Y-m-d H:i:s");

    // End Add Invitee Flag
    $endAddInviteeFlag = false;

    // Reg Type
    $regType = null;

    // Event Reg and ID
    $eventReg = null;
    $eventID = null;

    // Event Title
    $eventTitle = "ERROR!";

    // Allowed Reg Exist
    $allowedRegExists = false;

    // Check if it fetched correctly from the events page
    $eventDetailsErrorFlag = false;
    $eventRegExistsFlag = false;
    $openRegFlag = false;
    $allowedRegExists = false;
    if (isset($_GET['type'])) {
    	$regType = $_GET['type'];

    	// Check if is allowed reg types
    	if ($regType == "all" || $regType == "employee" || $regType == "student"  || $regType == "faculty"|| $regType == "guest") {
    		if (isset($_GET['reg'])) {
	    		$eventReg = $_GET['reg'];
		        $eventID = base64_decode(base64_decode(base64_decode(base64_decode($_GET['reg']))));

	    		// Check if is registration exists
	    		$regConfigStmt = $conn->prepare("SELECT * FROM `registration` WHERE `event_ID` = ?");
				$regConfigStmt->bind_param("i", $eventID);
				if ($regConfigStmt->execute()){
					$regConfigResult = $regConfigStmt->get_result();
					$regConfigStmt->close();
					if ($regConfigResult->num_rows > 0) {
						// Get Reg Statuses
						while ($regRow = $regConfigResult->fetch_assoc()) {
							$openRegFlag = ($regRow["openRegistration"] == 1) ? true : false;
							switch ($regType) {
								case 'all':
									$allowedRegExists = ($regRow["allowedAll"] == 1) ? true : false;
									break;
								case 'employee':
									$allowedRegExists = ($regRow["allowedEmp"] == 1) ? true : false;
									break;
								case 'student':
									$allowedRegExists = ($regRow["allowedStud"] == 1) ? true : false;
									break;
								case 'faculty':
									$allowedRegExists = ($regRow["allowedFaculty"] == 1) ? true : false;
									break;
								case 'guest':
									$allowedRegExists = ($regRow["allowedGuest"] == 1) ? true : false;
									break;
							}
						}

						// Get Event Information
						$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `ID` = ? AND `status` = 1");
				        $eventStmt->bind_param('i', $eventID);
				        $eventStmt->execute();
				        $eventInfo =  $eventStmt->get_result();
				        $eventStmt->close();
				        if ($eventInfo->num_rows > 0) {
				            while ($row = $eventInfo->fetch_assoc()) {
				                $eventID = $row['ID'];
				                $eventTitle = $row["event_title"];
				                $eventDate = $row["date"];
								$eventDateEnd = $row["date_end"];
				                $eventTimeInclusive = $row["time_inclusive"];
				                $eventTimeConclusive = $row["time_conclusive"];
				                $eventVenue = $row["venue"];
				                $eventDesciption = $row["description"];
				                $eventAgenda = $row["agenda"];
				                $eventTheme = $row["theme"];
				                $dateTimeAdded = $row["datetime_added"];
				                $diffEndTimes = (strtotime($currentDateTime) - strtotime($row["date_end"]." ".$row["time_conclusive"])) / 60 / 60 / 24;
				                // Check if Intervals are Allowed to Scan Attendance
				                if ($diffEndTimes < 2){
				                    $endAddInviteeFlag = true;
				                }
				            }
				        }
				        $eventRegExistsFlag = true;
					}
				}
		    }
		    $eventDetailsErrorFlag = true;
    	}

    }
    
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $eventTitle;?> - Registration 
		<?php
			if ($regType == "all" || $regType == "employee" || $regType == "student" || $regType == "faculty"  || $regType == "guest") {
				echo "for " . ucfirst($regType);
			} else {
				echo "Error";
			}
		?>
	 | Attend and Certify</title>
	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
    <style>
    	#snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            /*background-color: green;*/
            color: #fff;
            text-align: center;
            border-radius: 10px;
            padding: 16px;
            position: fixed;
            z-index: 1060;
            left: 50%;
            right: 50%;
            top: 100px;
            font-size: 17px;
        }
        #snackbar.show {
            visibility: visible;
        }
    </style>
</head>
<body class="d-flex flex-column" >
	<!-- Navbar -->
	<div class="container-fluid mb-5">
		<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
			<!-- Logo -->
            <a class="navbar-brand" href="#" style="margin-right: 0.5rem;">
                <img src="img/logo_circle.svg" alt="Logo" width="50" onContextMenu="return false;"  ondragstart="return false;">
            </a>
            <!-- Button for Collapsible Navbar -->
            <button id="navbar-button" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span id="navbar-button-icon" class="navbar-toggler-icon"></span>
            </button>
            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
            	<!-- Logo Text -->
            	<div class="justify-content-center text-dark mt-2">
                    <img src="img/logo_text.svg" alt="ATTEND and CERTIFY" height="32.5" onContextMenu="return false;" ondragstart="return false;">
                </div>
            </div>
		</nav>
	</div>
	<div class="main-body container-fluid flex-grow-1 mt-5">
		<!-- The loading modal -->
	    <div id="loadingModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index: 1060;">
	        <div class="modal-dialog modal-dialog-centered modal-sm " >
	            <div class="modal-content border-form-override p-3 text-center" id="myModal" style="max-height: 190px;">
	                <div class="text-center">
	                    <div class="d-flex justify-content-center">
	                        <div class="loadingio-spinner-rolling-lefr816gl">
	                            <div class="ldio-eeg8hrr2lac">
	                                <div></div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <p class="mb-0 mt-2 disable-select" style="font-size: 1.25rem;" unselectable="on"><strong>Please Wait</strong></p>
	            </div>
	        </div>
	    </div>
	    <!-- The setting status snackbar -->
        <div id="snackbar"></div>
        <!-- Main Body -->
        <div class="container-fluid px-2 pt-2">
        	<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold header-title" style="font-size: 2.25rem;">REGISTRATION
                	<?php
						if ($regType == "all" || $regType == "employee" || $regType == "student" || $regType == "faculty" || $regType == "guest") {
							echo "for " . strtoupper($regType);
						} else {
							echo "ERROR";
						}
					?>
                </h1>
                <h2 class="pl-3 font-weight-normal">Please fill up the required information.</h2>
            </div>
            <div class="container shadow-sm p-0 my-3 mt-4 border-form-override" id="registrationContainer" style="max-width: 800px;">
                <?php
                	if ($eventDetailsErrorFlag) {
                		if ($endAddInviteeFlag) {
                			if ($eventRegExistsFlag) {
                				if ($openRegFlag) {
                					if ($allowedRegExists) { ?>
                						<div class="modal-header bg-primary add-edit-invitee-override">
						                    <h5 class="modal-title text-light add-edit-invitee-title" id="exampleAddEditInvitee"><i class="fas fa-calendar"></i> <a href="#eventModal" data-toggle="modal" data-target="#eventModal" style="color: #fff;"><?php echo $eventTitle;?></a></h5>
						                </div>
						                <form method="post" id="inviteeForm">
						                    <div class="modal-body">
						                        <div class="row">
						                            <div class="col-sm-6">
						                                <div class="form-group">
						                                    <label for="addFirstName" class="label-add-edit-event">
						                                        First Name: <span class="required error" id="firstName-info"></span>
						                                    </label>
						                                    <input type="text" class="form-control" name="inviteeFirstName" id="inviteeFirstName" placeholder="First Name">
						                                </div>
						                                <div class="form-group">
						                                    <label for="addMiddleName" class="label-add-edit-event">
						                                        Middle Name: <span class="required error" id="middleName-info"></span>
						                                    </label>
						                                    <input type="text" class="form-control" name="inviteeMiddleName" id="inviteeMiddleName" placeholder="Middle Name">
						                                </div>
						                                <div class="form-group">
						                                    <label for="addLastName" class="label-add-edit-event">
						                                        Last Name: <span class="required error" id="lastName-info"></span>
						                                    </label>
						                                    <input type="text" class="form-control" name="inviteeLastName" id="inviteeLastName" placeholder="Last Name">
						                                </div>
						                            </div>
						                            <div class="col-sm-6">
						                                <div class="form-group">
						                                    <label for="addEmail" class="label-add-edit-event">
						                                        Email: <span class="required error" id="email-info"></span>
						                                    </label>
						                                    <input type="text" class="form-control" name="inviteeEmail" id="inviteeEmail" placeholder="example@example.com">
						                                </div>
						                                <div class="form-group">
						                                    <label for="addPhoneNum" class="label-add-edit-event">
						                                        Phone No.: <span class="required error" id="phoneNum-info"></span>
						                                    </label>
						                                    <input type="text" class="form-control" name="inviteePhoneNum" id="inviteePhoneNum" placeholder="XXXXXXXXXXX">
						                                </div>
						                                <?php
						                                	if ($regType == "all") { ?>
						                                		<div class="form-group">
								                                    <label for="addType" class="label-add-edit-event">
								                                        Type: <span class="required error" id="type-info"></span>
								                                    </label>
								                                    <select class="form-control" name="inviteeTypeForm" id="inviteeTypeForm">
								                                        <option value='Employee' selected>Employee</option>
								                                        <option value='Student'>Student</option>
								                                        <option value='Faculty'>Faculty</option>
								                                        <option value='Guest'>Guest</option>
								                                    </select>
								                                </div>
						                                	<?php }
						                                ?>
						                            </div>
						                        </div>
						                    </div>
						                    <div class="modal-footer">
						                        <button type="submit" class="btn btn-success" name="inviteeSave" id="inviteeSave"><i class="fas fa-save"></i> Submit</button>
						                    </div>
						                </form>
						                <!-- Event Modal -->
	                                    <div class="modal" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle" aria-hidden="true" data-backdrop="static">
	                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
	                                            <div class="modal-content">
	                                                <div class="modal-header bg-primary">
	                                                    <h4 class="modal-title text-light" id="exampleModalLongTitle"><?php echo $eventTitle;?></h5>
	                                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
	                                                        <span aria-hidden="true">&times;</span>
	                                                    </button>
	                                                </div>
	                                                <div class="modal-body">
	                                                    <div class="row">
	                                                        <div class="col-sm-6">
	                                                            <div class="card bg-info-event-modal h-100 pt-0">
	                                                                <div class="card-body">
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-calendar"></i> Date:</h5>
	                                                                        <p>
																				<?php
																					if ($eventDate == $eventDateEnd) {
																						echo date_format(date_create($eventDate),"F d, Y");
																					} else {
																						if (date_format(date_create($eventDate),"Y") == date_format(date_create($eventDateEnd),"Y")) {
																							$yearStr = date_format(date_create($eventDate),"Y");
																							if (date_format(date_create($eventDate),"F") == date_format(date_create($eventDateEnd),"F")) {
																								$monthStr = date_format(date_create($eventDate),"F ").date_format(date_create($eventDate),"d-").date_format(date_create($eventDateEnd),"d");
																							} else {
																								$monthStr = date_format(date_create($eventDate),"F d").'-'.date_format(date_create($eventDateEnd),"F d");
																							}
																							echo $monthStr.", ".$yearStr;
																						} else {
																							echo date_format(date_create($eventDate),"F d, Y")." - ".date_format(date_create($eventDateEnd),"F d, Y");
																						}
																					}
																				?>
																			</p>
	                                                                    </div>
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-clock-o"></i> Time:</h5>
	                                                                        <p><?php echo date_format(date_create($eventTimeInclusive),"h:iA");?> - <?php echo date_format(date_create($eventTimeConclusive),"h:iA");?></p>
	                                                                    </div>
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-map-marker"></i> Venue:</h5>
	                                                                        <p><?php echo $eventVenue;?></p>
	                                                                    </div>
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-info-circle"></i> Description:</h5>
	                                                                        <p><?php echo $eventDesciption;?></p>
	                                                                    </div>
	                                                                </div>
	                                                            </div>
	                                                        </div>
	                                                        <div class="col-sm-6">
	                                                            <div class="card bg-info-event-modal h-100 pt-0">
	                                                                <div class="card-body">
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-tasks"></i> Agenda:</h5>
	                                                                        <p><?php echo $eventAgenda;?></p>
	                                                                    </div>
	                                                                    <div class="form-group">
	                                                                        <h5><i class="fa fa-quote-left"></i> Theme:</h5>
	                                                                        <p><?php echo $eventTheme;?></p>
	                                                                    </div>
	                                                                    <div class="form-group">
	                                                                         <h5><i class="fas fa-hourglass-start"></i> Date and Time Added:</h5>
	                                                                         <p><?php echo date_format(date_create($dateTimeAdded),"M d, Y h:iA");?></p>
	                                                                    </div>
	                                                                </div>
	                                                            </div>
	                                                        </div>
	                                                    </div>
	                                                </div>
	                                                <div class="modal-footer">
	                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
		                			<?php } else { ?>
		                				<div class="mt-2 mb-2 p-3">
						                    <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> The registration is not yet allowed, please try contacting the event owner.</h4>
						                </div>
		                			<?php }
                				} else { ?>
					                <div class="mt-2 mb-2 p-3">
					                    <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> The registration is close, please try contacting the event owner.</h4>
					                </div>
			                	<?php }
                			} else { ?>
                				<div class="mt-2 mb-2 p-3">
				                    <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> The registration doesn't exists, please try again.</h4>
				                </div>
		                	<?php }
	                	} else { ?>
	                		<div class="mt-2 mb-2 p-3">
			                    <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> The event is now concluded, it means it is no longer accepting any registration.</h4>
			                </div>
	                	<?php }
                	} else {?>
                		<div class="mt-2 mb-2 p-3">
		                    <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> An error has occured, please try again.</h4>
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
    <script>
    	// Get Attendance Status Snackbar
        var statusSnackBar = document.getElementById("snackbar");

    	// Get Registration Type
    	var regType = "<?php echo (!is_null($regType)) ? (($regType != 'all') ? ucfirst($regType) : 'Employee') : "undefined";?>";

    	// Invitee Type Change
    	$("#inviteeTypeForm").change(function() {
    		regType = $(this).val();
    	});

    	// Submit Registration Form
    	$("#inviteeForm").submit(function(event) {
    		event.preventDefault();
    		if (inviteeRegistrationValidation() == true) {
    			$.ajax({
    				url:"model/registration-member",
	                method:"POST",
	                data:{ 
	                    registrationAction:'addInviteeRegistration',
	                    eventID: <?php echo (is_null($eventID)) ? "null" : $eventID;?>,
	                    inviteeFirstNameInput: $("#inviteeFirstName").val().trim(),
                        inviteeMiddleNameInput: $("#inviteeMiddleName").val().trim(),
                        inviteeLastNameInput: $("#inviteeLastName").val().trim(),
                        inviteeEmailInput: $("#inviteeEmail").val().trim(),
                        inviteePhoneNumInput: $("#inviteePhoneNum").val().trim(),
                        inviteeTypeSelect: regType
	                },
	                dataType:"json",
	                beforeSend: function(){
	                    $("#loadingModal").modal('show');
	                }
    			})
    			.done(function(data) {
    				$("#loadingModal").modal('hide');
    				if (data.Status == "nameAlreadyExists") {;
                        $("#firstName-info").html("* Name of Invitee Already Exists.").css("color", "#ee0000").show().delay(1000).fadeOut();
                    } else if (data.Status == "emailAlreadyExists") {
                    	$("#email-info").html("* Email of Invitee Already Exists.").css("color", "#ee0000").show().delay(1000).fadeOut();
                    } else if (data.Status == "phoneNumAlreadyExists") {
                    	$("#phoneNum-info").html("* Phone of Invitee Already Exists.").css("color", "#ee0000").show().delay(1000).fadeOut();
                    } else if (data.Status == "error") {
                    	statusSnackBar.style.backgroundColor = "#d9534f";
		                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Registration Failed';
		                barcodeReader.style.borderColor = "#dc3545";
		                displayStatusSnackBar();
                    } else {
                        $("#registrationContainer").html(
                        	'<div class="mt-2 mb-2 p-3">' + 
			                    '<h4><i style="color: green;" class="fas fa-check"></i> Your registration has been recorded, please wait for your invitation email.</h4>' + 
			                '</div>'
                        );
                    }
    			})
    			.fail(function() {
    				$("#loadingModal").modal('hide');
    				statusSnackBar.style.backgroundColor = "#d9534f";
	                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Registration Error';
	                displayStatusSnackBar();
    			});
    			
    		}
    	});

    	// Show status snackback
	    function displayStatusSnackBar() {
	        // Add the "show" class to DIV
	        statusSnackBar.className = "show";

	        // After 2 seconds, remove the show class from DIV
	        setTimeout(function(){ 
	            statusSnackBar.className = statusSnackBar.className.replace("show", "");
	            statusSnackBar.style.backgroundColor = "";
	            statusSnackBar.innerHTML = '';
	        }, 2000);
	    }

    	// Invitee Registration validation
	    function inviteeRegistrationValidation() {
	        var valid = true;

	        $("#inviteeFirstName").removeClass("error-field");
	        $("#inviteeMiddleName").removeClass("error-field");
	        $("#inviteeLastName").removeClass("error-field");
	        $("#inviteeEmail").removeClass("error-field");
	        $("#inviteePhoneNum").removeClass("error-field");

	        var inviteeFirstName = $("#inviteeFirstName").val();
	        var inviteeMiddleName = $("#inviteeMiddleName").val();
	        var inviteeLastName = $("#inviteeLastName").val();
	        var inviteeEmail = $("#inviteeEmail").val();
	        var inviteePhoneNum = $("#inviteePhoneNum").val();
	        var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
	        var phoneNumRegex = /^\(?([0-9]{4})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;

	        $("#firstName-info").html("").hide();
	        $("#middleName-info").html("").hide();
	        $("#lastName-info").html("").hide();
	        $("#email-info").html("").hide();
	        $("#phoneNum-info").html("").hide();

	        if(inviteeFirstName.trim() == ""){
	            $("#firstName-info").html("* Required.").css("color", "#ee0000").show();
	            $("#inviteeFirstName").addClass("error-field");
	            valid = false;
	        }
	        if(inviteeMiddleName.trim() == ""){
	            $("#middleName-info").html("* Required.").css("color", "#ee0000").show();
	            $("#inviteeMiddleName").addClass("error-field");
	            valid = false;
	        }
	        if(inviteeLastName.trim() == ""){
	            $("#lastName-info").html("* Required.").css("color", "#ee0000").show();
	            $("#inviteeLastName").addClass("error-field");
	            valid = false;
	        }
	        if (inviteeEmail == "") {
	            $("#email-info").html("* Required.").css("color", "#ee0000").show();
	            $("#inviteeEmail").addClass("error-field");
	            valid = false;
	        } else if (inviteeEmail.trim() == "") {
	            $("#email-info").html("* Invalid.").css("color", "#ee0000").show();
	            $("#inviteeEmail").addClass("error-field");
	            valid = false;
	        } else if (!emailRegex.test(inviteeEmail)) {
	            $("#email-info").html("* Invalid.").css("color", "#ee0000").show();
	            $("#inviteeEmail").addClass("error-field");
	            valid = false;
	        }
	        if (inviteePhoneNum == "") {
	            $("#phoneNum-info").html("* Required.").css("color", "#ee0000").show();
	            $("#inviteePhoneNum").addClass("error-field");
	            valid = false;
	        } else if (inviteePhoneNum.trim() == "") {
	            $("#phoneNum-info").html("* Invalid.").css("color", "#ee0000").show();
	            $("#inviteePhoneNum").addClass("error-field");
	            valid = false;
	        } else if (!phoneNumRegex.test(inviteePhoneNum)) {
	            $("#phoneNum-info").html("* Invalid.").css("color", "#ee0000").show();
	            $("#inviteePhoneNum").addClass("error-field");
	            valid = false;
	        }
	        return valid;
	    }
    </script>
</body>
</html>