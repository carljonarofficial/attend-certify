<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Initialize Title Page variable
    $eventTitle = "";

    // Using database connection file here
    include 'dbConnection.php';

    // Current Date and Time
    $currentDateTime = date("Y-m-d H:i:s");

    // End Add Invitee Flag
    $endAddInviteeFlag = false;

    // Event ID and Cert Template
    $eventID = null;
    $certTemplate = null;

    // Check if it fetched correctly from the events page
    $eventDetailsErrorFlag = false;
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
                $eventDateEnd = $row["date_end"];
                $eventTimeInclusive = $row["time_inclusive"];
                $eventTimeConclusive = $row["time_conclusive"];
                $eventVenue = $row["venue"];
                $eventDesciption = $row["description"];
                $eventAgenda = $row["agenda"];
                $eventTheme = $row["theme"];
                $certTemplate = $row["certificate_template"];
                $dateTimeAdded = $row["datetime_added"];
                $dateTimeEdited = $row['datetime_edited'];
                $diffEndTimes = (strtotime($currentDateTime) - strtotime($row["date_end"]." ".$row["time_conclusive"])) / 60 / 60 / 24;
                // Check if Intervals are Allowed to Scan Attendance
                if ($diffEndTimes < 2){
                    $endAddInviteeFlag = true;
                }
            }
            $eventDetailsErrorFlag = true;    
        } else {
            $eventTitle = "ERROR!";
            $eventDetailsErrorFlag = false;
        }
    }else{
    	$eventTitle = "ERROR!";
        $eventDetailsErrorFlag = false;
    }

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $eventTitle;?> - Event Details | Attend and Certify</title>

    <?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Modified Page Style -->
    <style>
        @media only screen and (max-width: 280px) {
            .header-title {
                font-size: 2rem;
            }
        }
        .event-invitee-btn {
            border-width: 3px;
        }
        .event-invitee-btn.collapsed {
            background-color: #fff;
            color:  #000;
        }
    </style>
    <!-- Datatables Styles -->
    <link rel="stylesheet" type="text/css" href="style/datatables/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="style/datatables/jquery.dataTables-custom.css">
    <link rel="stylesheet" type="text/css" href="style/datatables/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="style/datatables/buttons.dataTables.min.css">
    <!-- Datatables Scripts -->
    <script src="scripts/datatables/jquery.dataTables.min.js"></script>
    <script src="scripts/datatables/dataTables.select.min.js"></script>
    <script src="scripts/datatables/dataTables.buttons.min.js"></script>
    <script src="scripts/jszip/jszip.min.js"></script>
    <script src="scripts/pdfmake/pdfmake.min.js"></script>
    <script src="scripts/pdfmake/vfs_fonts.js"></script>
    <script src="scripts/datatables/buttons.html5.min.js"></script>
    <script src="scripts/datatables/buttons.print.min.js"></script>
</head>
<body class="d-flex flex-column">
    <?php
        // Initialize Active Page for Navbar Highlight
        $activePage = "events";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>

    <div class="main-body container-fluid flex-grow-1 mt-5">
        <!-- The loading modal -->
        <div  id="loadingModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index: 1060;">
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
        <!-- Main Body -->
        <div class="container-fluid px-2 pt-2">
            <!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <input type="hidden" id="current-event-ID" value="<?php echo $eventID;?>">
                <h1 class="font-weight-bold header-title" id="current-event-title"><?php echo $eventTitle;?></h1>
                <h2 class="pl-3 font-weight-normal">View Event and Invitees Details.</h2>
            </div>
            <!-- Go Back to Events Button -->
            <div class="mt-3">
                <a href="events.php" class="ml-3 h4 btn btn-secondary btn-lg-add-event rounded-pill"><i class="fas fa-arrow-circle-left"></i> GO BACK</a>
            </div>
            <?php 
                // Check if fetch is successful
                if ($eventDetailsErrorFlag == "success") { ?>
                    <!-- Toggle Between Event and Invitee Details Buttons -->
                    <div class="container mt-2 mb-4 p-2 border-form-override event-invitee-parent">
                        <div class="row">
                            <!-- Event Details Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingOne">
                                <button class="btn btn-info btn-lg-add-event btn-lg-event-invitee event-invitee-btn" type="button" data-toggle="collapse" data-target="#collapseEventDetails" aria-expanded="true" aria-controls="collapseOne"><i class="fas fa-calendar"></i> EVENT DETAILS</button>
                            </div>
                            <!-- Invitees' Details Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingTwo">
                                <button class="btn btn-info btn-lg-add-event btn-lg-event-invitee event-invitee-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseInviteeDetails" aria-expanded="false" aria-controls="collapseTwo"><i class="fas fa-users"></i> INVITEES' DETAILS</button>
                            </div>
                        </div>
                    </div>
                    <!-- Event and Invitee Details Section --> 
                    <div class="container shadow-sm mt-2 mb-4 border-form-override event-invitee-parent">
                        <div class="accordion" id="accordionEventInviteeDetails">
                            <div class="event-invitee-details">
                                <div id="collapseEventDetails" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionEventInviteeDetails">
                                    <div class="card-body">
                                        <div class="mb-3 mr-3">
                                            <!-- Event Details -->
                                            <h4 style="font-size: 2rem;"><i class="fas fa-calendar"></i> Event Details</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col m-1">
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
                                                            <h5><i class="fa fa-certificate"></i> Certificate Template:</h5>
                                                            <input type="file" name="certAttachment" id="certAttachment">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m-1">
                                                <div class="card bg-info-event-modal h-100 pt-0">
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <h5><i class="fa fa-info-circle"></i> Description:</h5>
                                                            <p><?php echo $eventDesciption;?></p>
                                                        </div>
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
                                                        <?php
                                                            if (!is_null($dateTimeEdited)) {
                                                                echo '<div class="form-group"><h5>
                                                                        <i class="fas fa-edit"></i> Date and Time Edited:</h5>
                                                                        <p>'.date_format(date_create($dateTimeEdited),"M d, Y h:iA").'</p>
                                                                    </div>';
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="event-invitee-details">
                                <div id="collapseInviteeDetails" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionEventInviteeDetails">
                                    <div class="card-body">
                                        <div class="mb-3 mr-3">
                                            <!-- Invitees Table -->
                                            <h4 style="font-size: 2rem;"><i class="fas fa-users"></i> Invitees' Table</h4>
                                            <div class="w-100 p-3 mt-1 shadow-sm rounded bg-success text-light response" id="crud-successful">
                                                <h5>ADD INVITEE SUCCESSFULLY</h5>
                                            </div>
                                            <?php
                                                if ($endAddInviteeFlag) { ?>
                                                    <!-- Add Invitee -->
                                                    <div class="col-sm-6 mt-3">
                                                        <div class="input-group">
                                                            <button class="h4 btn btn-success btn-lg-add-invitee rounded-pill" id="addInvitee" data-toggle="modal" data-target="#addEditInviteeModal"><i class="fa fa-plus-circle"></i> ADD INVITEE</button>
                                                            <button class="h4 btn btn-primary btn-lg-add-invitee rounded-pill ml-1" id="shareRegistrationBtn"><i class="fas fa-share-alt"></i> SHARE</button>
                                                            <button class="h4 btn btn-danger btn-lg-add-invitee rounded-pill ml-1" id="deleteSelectedInvitations" style="display: none;"><i class="fas fa-trash-alt"></i> DELETE</button>
                                                        </div>
                                                    </div>
                                                <?php }
                                            ?>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="inviteeList" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th id="inviteeView"></th>
                                                        <th id="inviteeID">ID</th>
                                                        <th id="inviteeFName">First Name</th>
                                                        <th id="inviteeMName">Middle Initial</th>
                                                        <th id="inviteeLName">Last Name</th>
                                                        <th id="inviteeType">Type</th>
                                                        <th id="inviteeMore"></th>
                                                        <th id="inviteeCheckBox"><input type="checkbox" id="selectAllInvitees"></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm">
                                                <h5><i class="fas fa-users"></i> Total Invitees: <span id="totalInvitees">N</span></h5>
                                            </div>
                                        </div>
                                        <?php
                                            if ($endAddInviteeFlag) { ?>
                                                <!-- Share Registration Modal -->
                                                <div class="modal" id="shareRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="shareRegistrationModal" aria-hidden="true" data-backdrop="static">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 650px;">
                                                        <div class="modal-content border-form-override">
                                                            <div class="modal-header bg-primary add-edit-invitee-override">
                                                                <h4 class="modal-title text-light" id="shareRegistrationTitle"><i class="fas fa-share-alt"></i> Share this Event Registration</h5>
                                                                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Open Registration -->
                                                                <div class="form-group">
                                                                    <label class="label-add-edit-event">Open Registration?:</label>
                                                                    <div class="custom-control custom-switch ml-1">
                                                                        <input type="checkbox" class="custom-control-input" id="openRegs" name="openRegs">
                                                                        <label class="custom-control-label" for="openRegs" id="openRegsLabel">No</label>
                                                                    </div>
                                                                </div>
                                                                <hr style="border-top: 1px solid;">
                                                                <!-- Registration Config Container -->
                                                                <div id="regsConfigContainer" style="display: none;">
                                                                    <!-- Allowed Invitee Type/s or Require Approval -->
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label class="label-add-edit-event">Allowed Invitee Type/s:</label>
                                                                                <div class="p-2 time-event">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="allowEmployeeCheckbox" name='allowEmployeeCheckbox'>
                                                                                        <label class="custom-control-label" for="allowEmployeeCheckbox">Employee</label>
                                                                                    </div>
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="allowStudentCheckbox" name='allowStudentCheckbox'>
                                                                                        <label class="custom-control-label" for="allowStudentCheckbox">Student</label>
                                                                                    </div>
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="allowFacultyCheckbox" name='allowFacultyCheckbox'>
                                                                                        <label class="custom-control-label" for="allowFacultyCheckbox">Faculty</label>
                                                                                    </div>
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="allowGuestCheckbox" name='allowGuestCheckbox'>
                                                                                        <label class="custom-control-label" for="allowGuestCheckbox">Guest</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <!-- Allow Unified Registration -->
                                                                            <div class="form-group">
                                                                                <label class="label-add-edit-event">Unified Registration?:</label>
                                                                                <div class="custom-control custom-switch ml-1">
                                                                                    <input type="checkbox" class="custom-control-input" id="unifiedRegs" name="unifiedRegs">
                                                                                    <label class="custom-control-label" for="unifiedRegs" id="unifiedRegsLabel">No</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Share Link -->
                                                                    <div class="form-group">
                                                                        <label class="label-add-edit-event">Link/s:</label>
                                                                        <div class="p-2 time-event">
                                                                            <!-- Unified Link -->
                                                                            <div class="form-group" id="unifiedRegParent">
                                                                                <label class="label-add-edit-event">Unified Registration: <span id="unifiedRegLinkCopied"></span></label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control form-control-custom" id="unifiedRegLinkField" value="https://attend-certify.com/registration?type=all&reg=<?php echo base64_encode(base64_encode(base64_encode(base64_encode($eventID))));?>" readonly >
                                                                                    <div class="input-group-append">
                                                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('unifiedRegLink')"><i class="fas fa-copy"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- Employee Link -->
                                                                            <div class="form-group" id="empRegParent">
                                                                                <label class="label-add-edit-event">Employee: <span id="empRegLinkCopied"></span></label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control form-control-custom" id="empRegLinkField" value="https://attend-certify.com/registration?type=employee&reg=<?php echo base64_encode(base64_encode(base64_encode(base64_encode($eventID))));?>" readonly >
                                                                                    <div class="input-group-append">
                                                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('empRegLink')"><i class="fas fa-copy"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- Student Link -->
                                                                            <div class="form-group" id="stuRegParent">
                                                                                <label class="label-add-edit-event">Student: <span id="stuRegLinkCopied"></span></label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control form-control-custom" id="stuRegLinkField" value="https://attend-certify.com/registration?type=student&reg=<?php echo base64_encode(base64_encode(base64_encode(base64_encode($eventID))));?>" readonly >
                                                                                    <div class="input-group-append">
                                                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('stuRegLink')"><i class="fas fa-copy"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- Faculty Link -->
                                                                            <div class="form-group" id="fctRegParent">
                                                                                <label class="label-add-edit-event">Faculty: <span id="facultyRegLinkCopied"></span></label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control form-control-custom" id="facultyRegLinkField" value="https://attend-certify.com/registration?type=faculty&reg=<?php echo base64_encode(base64_encode(base64_encode(base64_encode($eventID))));?>" readonly >
                                                                                    <div class="input-group-append">
                                                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('facultyRegLink')"><i class="fas fa-copy"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- Guest Link -->
                                                                            <div class="form-group" id="gstRegParent">
                                                                                <label class="label-add-edit-event">Guest: <span id="gstRegLinkCopied"></span></label>
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control form-control-custom" id="gstRegLinkField" value="https://attend-certify.com/registration?type=guest&reg=<?php echo base64_encode(base64_encode(base64_encode(base64_encode($eventID))));?>" readonly >
                                                                                    <div class="input-group-append">
                                                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('gstRegLink')"><i class="fas fa-copy"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                                                <button type="submit" class="btn btn-success" id="saveRegsConfigBtn"><i class="fas fa-save"></i> Save</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add or Edit Invitee Modal -->
                                                <div class="modal" id="addEditInviteeModal" tabindex="-1" role="dialog" aria-labelledby="exampleAddEditInvitee" aria-hidden="true" data-backdrop="static">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content border-form-override">
                                                            <div class="modal-header bg-primary add-edit-invitee-override">
                                                                <h5 class="modal-title text-light add-edit-invitee-title" id="exampleAddEditInvitee"><i class="fa fa-plus-circle"></i> Add Invitee</h5>
                                                                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
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
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="selectedInviteeID" id="selectedInviteeID" value="" />
                                                                    <input type="hidden" name="inviteeAction" id="inviteeAction" value="" />
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                                                    <button type="submit" class="btn btn-success" name="inviteeSave" value="Save" id="inviteeSave"><i class="fas fa-save"></i> Save</button>
                                                                </div>
                                                            </form>                 
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Delete Selected Invitee Modal -->
                                                <div class="modal" id="deleteSelectedInviteeModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true" data-backdrop="static">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h4 class="modal-title text-light" ><i class="fas fa-exclamation-triangle"></i> Are you sure to delete these selected invitee/s?</h5>
                                                                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5><i class="fas fa-users"></i> Selected Invitees: </h5>
                                                                <h6>
                                                                    <ul id="selected-invitees-deletion">
                                                                        <!-- Selected Invitees Placeholder -->
                                                                    </ul>
                                                                </h6>
                                                                <p>After you delete them. You will not able to retrieve them, please be careful.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form method="post" id="inviteeSelectedDeleteForm">
                                                                    <button type="submit" class="btn btn-danger" name="inviteeDelete" value="Delete" id="inviteeDelete"><i class="fas fa-trash"></i> Yes</button>
                                                                </form>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-circle-left"></i> No</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Delete Invitee Modal -->
                                                <div class="modal" id="deleteInviteeModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true" data-backdrop="static">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h4 class="modal-title text-light" id="exampleModalDelete"><i class="fas fa-exclamation-triangle"></i> Are you sure to delete this invitee?</h5>
                                                                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5 id="invitee-delete-name"></h5>
                                                                <p>After you delete this. You will not able to retrieve this, please be careful.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form method="post" id="inviteeDeleteForm">
                                                                    <input type="hidden" name="inviteeDeleteID" id="inviteeDeleteID" />
                                                                    <input type="hidden" name="inviteeDeleteAction" id="inviteeDeleteAction" value="deleteInvitee" />
                                                                    <button type="submit" class="btn btn-danger" name="inviteeDelete" value="Delete" id="inviteeDelete"><i class="fas fa-trash"></i> Yes</button>
                                                                </form>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-circle-left"></i> No</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        ?>
                                        <!-- View Invitee Modal -->
                                        <div class="modal" id="viewInviteeModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h4 class="modal-title text-light" id="viewInviteeModalTitleName">First_Name Middle_Name Last_Name</h5>
                                                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col m-1">
                                                                <div class="card bg-info-event-modal h-100 pt-0">
                                                                    <div class="card-body">
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-envelope"></i> Email:</h5>
                                                                            <p id="viewInviteeEmail">example@email.com</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-phone-alt"></i> Phone No.:</h5>
                                                                            <p id="viewInviteePhoneNum">XXXXXXXXXXX</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-user-tag"></i> Type:</h5>
                                                                            <p id="viewInviteeType">Type</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-barcode"></i> Code:</h5>
                                                                            <p id="viewInviteeCode">IVT-YYYYMMDDHHMMSS-XXXXXX</p>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-hourglass-start"></i> Date and Time Added:</h5>
                                                                            <p id="viewDateTimeAdded">MMM DD, YYYY - HH:mmA</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col m-1">
                                                                <div class="card bg-info-event-modal h-100 pt-0">
                                                                    <div class="card-body d-flex align-items-center">
                                                                        <div class="form-group">
                                                                            <h5><i class="fas fa-barcode"></i> Barcode:</h5>
                                                                            <img class="w-100" id="viewInviteeBarcode" src="" alt="Barcode" onContextMenu="return false;" ondragstart="return false;">
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
                                    </div>
                                </div>
                            </div>      
                        </div>
                    </div>
            <?php } else { ?>
                    <!-- If fetched error, then the error message appear. -->
                    <div class="w-100 p-3 mt-3 mb-5 shadow-sm rounded bg-danger text-light response">
                        <h4>Fetch Error, Please try again.</h4>
                    </div>
            <?php } ?>
        </div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Invitee AJAX Script -->
    <script src="scripts/invitee-ajax.js"></script>
    <script>
        // File Upload Script
        var $js_certTemplate = "<?php echo $certTemplate;?>";
        var $certFileType = $js_certTemplate.substring($js_certTemplate.indexOf(".")+1);
        $("#certAttachment").fileinput({
            theme: 'fas',
            showRemove:false,
            showUpload: false,
            showZoom: true,
            showClose: false,
            dropZoneEnabled: false,
            allowedFileExtensions: ['docx', 'pdf', 'jpeg', 'jpg', 'png'],
            required:true,
            initialPreview: [// PDF DATA
                'https://attend-certify.com/certificate-templates/' + $js_certTemplate
            ],
            initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
            initialPreviewConfig: [{type: $certFileType, caption: $js_certTemplate, downloadUrl: 'https://attend-certify.com/certificate-templates/' + $js_certTemplate}, // disable download
                ],
            initialPreviewShowDelete: false,
            showBrowse: false,
            showCaption: false,
            showClose: false
        }).on('filepreupload', function(event, data, previewId, index) {
            alert('The description entered is:\n\n' + ($('#description').val() || ' NULL'));
        });

        // Change Open Registration Label
        $("input[name='openRegs']").click(function() {
            if ($(this).is(":checked")) {
                showRegConfigElement("openRegs", "openRegsLabel", "regsConfigContainer");
            } else {
                hideRegConfigElement("openRegs", "openRegsLabel", "regsConfigContainer");
            }
        });

        // Change Unified Registration Label
        $("input[name='unifiedRegs']").click(function() {
            if ($(this).is(":checked")) {
                showRegConfigElement("unifiedRegs", "unifiedRegsLabel", "unifiedRegParent");
            } else {
                hideRegConfigElement("unifiedRegs", "unifiedRegsLabel", "unifiedRegParent");
            }
        });

        // Employee Check
        $("input[name='allowEmployeeCheckbox']").click(function() {
            if ($(this).is(":checked")) {
                $("#empRegParent").show();
            } else {
                $("#empRegParent").hide();
            }
        });

        // Student Check
        $("input[name='allowStudentCheckbox']").click(function() {
            if ($(this).is(":checked")) {
                $("#stuRegParent").show();
            } else {
                $("#stuRegParent").hide();
            }
        });

        // Faculty Check
        $("input[name='allowFacultyCheckbox']").click(function() {
            if ($(this).is(":checked")) {
                $("#fctRegParent").show();
            } else {
                $("#fctRegParent").hide();
            }
        });

        // Guest Check
        $("input[name='allowGuestCheckbox']").click(function() {
            if ($(this).is(":checked")) {
                $("#gstRegParent").show();
            } else {
                $("#gstRegParent").hide();
            }
        });

        // Show Registration Config Element
        function showRegConfigElement(inputName, labelText, elementContainer) {
            $("input[name='" + inputName + "']").prop('checked', true);
            $("#" + labelText).html("Yes");
            $("#" + elementContainer).show();
        }

        // Hide Registration Config Element
        function hideRegConfigElement(inputName, labelText, elementContainer) {
            $("input[name='" + inputName + "']").prop('checked', false);
            $("#" + labelText).html("No");
            $("#" + elementContainer).hide();
        }

        // Copy to Clipboard Function
        function copyToClipboard(inputFieldID) {
            // Get the Text Field
            var copyLink = document.getElementById(inputFieldID + "Field");

            // Select the Text Field
            copyLink.select();
            copyLink.setSelectionRange(0, 99999); // For Mobile Devices

            // Copy the Text Inside the Text Field
            navigator.clipboard.writeText(copyLink.value);

            $('#' + inputFieldID + "Copied").html("Copied to clipboard.").css('color', '#28a745').show().delay(1000).fadeOut();
        }

        // Share Registration Open Modal
        $("#shareRegistrationBtn").click(function() {
            $.ajax({
                url:"model/registration-member",
                method:"POST",
                data:{ 
                    registrationAction:'getRegistrationConfig',
                    eventID: <?php echo (is_null($eventID)) ? "null" : $eventID;?>
                },
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                if (data.stmtResult === true) {
                    // Open Registration
                    if (data.regConfigArr.openRegistration == true) {
                        showRegConfigElement("openRegs", "openRegsLabel", "regsConfigContainer");
                    } else {
                        hideRegConfigElement("openRegs", "openRegsLabel", "regsConfigContainer");
                    }
                    // Unified Registration
                    if (data.regConfigArr.allowedAll == true) {
                        showRegConfigElement("unifiedRegs", "unifiedRegsLabel", "unifiedRegParent");
                    } else {
                        hideRegConfigElement("unifiedRegs", "unifiedRegsLabel", "unifiedRegParent");
                    }
                    // Allowed Employee
                    if (data.regConfigArr.allowedEmp == true) {
                        $("input[name='allowEmployeeCheckbox']").prop('checked', true);
                        $("#empRegParent").show();
                    } else {
                        $("input[name='allowEmployeeCheckbox']").prop('checked', false);
                        $("#empRegParent").hide();
                    }
                    // Allowed Student
                    if (data.regConfigArr.allowedStud == true) {
                        $("input[name='allowStudentCheckbox']").prop('checked', true);
                        $("#stuRegParent").show();
                    } else {
                        $("input[name='allowStudentCheckbox']").prop('checked', false);
                        $("#stuRegParent").hide();
                    }
                    // Faculty Student
                    if (data.regConfigArr.allowedFaculty == true) {
                        $("input[name='allowFacultyCheckbox']").prop('checked', true);
                        $("#fctRegParent").show();
                    } else {
                        $("input[name='allowFacultyCheckbox']").prop('checked', false);
                        $("#fctRegParent").hide();
                    }
                    // Allowed Guest
                    if (data.regConfigArr.allowedGuest == true) {
                        $("input[name='allowGuestCheckbox']").prop('checked', true);
                        $("#gstRegParent").show();
                    } else {
                        $("input[name='allowGuestCheckbox']").prop('checked', false);
                        $("#gstRegParent").hide();
                    }
                    $("#loadingModal").modal('hide');
                    $("#shareRegistrationModal").modal('show');
                } else {
                    $("#loadingModal").modal('hide');
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-danger");
                    $('#crud-successful').html("<h5>ERROR</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();    
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>ERROR</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            });
            
        });

        // Save Registration Button Click
        $("#saveRegsConfigBtn").click(function() {
            $.ajax({
                url:"model/registration-member",
                method:"POST",
                data:{ 
                    registrationAction:'saveRegistrationConfig',
                    eventID: <?php echo (is_null($eventID)) ? "null" : $eventID;?>,
                    openRegs: (($("input[name='openRegs']").is(":checked")) ? 1 : 0),
                    unifiedRegs: (($("input[name='unifiedRegs']").is(":checked")) ? 1 : 0),
                    allowEmployeeCheckbox: (($("input[name='allowEmployeeCheckbox']").is(":checked")) ? 1 : 0),
                    allowStudentCheckbox: (($("input[name='allowStudentCheckbox']").is(":checked")) ? 1 : 0),
                    allowFacultyCheckbox: (($("input[name='allowFacultyCheckbox']").is(":checked")) ? 1 : 0),
                    allowGuestCheckbox: (($("input[name='allowGuestCheckbox']").is(":checked")) ? 1 : 0)
                },
                dataType:"json",
                beforeSend: function(){
                    $("#loadingModal").modal('show');
                }
            })
            .done(function(data) {
                $("#loadingModal").modal('hide');
                if (data.saveStatus === true) {
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-succes");
                    $('#crud-successful').html("<h5>SAVE CONFIGURATION SUCCESS</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();
                } else {
                    $("#crud-successful").removeClass("bg-succes");
                    $("#crud-successful").removeClass("bg-warning");
                    $("#crud-successful").removeClass("bg-danger");
                    $("#crud-successful").addClass("bg-danger");
                    $('#crud-successful').html("<h5>SAVE CONFIGURATION FAILED</h5>");
                    $('#crud-successful').show().delay(1000).fadeOut();
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                $("#crud-successful").removeClass("bg-succes");
                $("#crud-successful").removeClass("bg-warning");
                $("#crud-successful").removeClass("bg-danger");
                $("#crud-successful").addClass("bg-danger");
                $('#crud-successful').html("<h5>ERROR</h5>");
                $('#crud-successful').show().delay(1000).fadeOut();
            });
            $("#shareRegistrationModal").modal('hide');
        });
    </script>
</body>
</html>