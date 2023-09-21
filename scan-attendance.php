<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Initialize Title Page variable
    $eventTitle = "";

    // Using database connection file here
    include 'dbConnection.php';

    // Current Date and Time
    $currentDateTime = date("Y-m-d H:i:s");
    $currentDate = date("Y-m-d");
    $currentAMPM = date("a");

    // Before Start Event and End Scan Attendance Flag
    $beforeStartScanFlag = false;
    $endScanFlag = true;

    // Check if it fetched correctly from the events page
    $scanAttendanceErrorFlag = false;
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
                $eventStartTime = date_format(date_create((($currentDate >= $row['date'] && $currentDate <= $row['date_end']) ? $currentDate : $row['date'])),"M d, Y")." - ".date_format(date_create($row['time_inclusive']),"h:iA");
                // Get Before Start Time of Event 1 Hour
                $beforeStartTime = date("Y-m-d H:i:s", strtotime((($currentDate >= $row['date'] && $currentDate <= $row['date_end']) ? $currentDate : $row['date'])." ".$row['time_inclusive']) - (60 * 60));
                // Difference Between Current and Before Start
                $diffDaysBefore = (strtotime($currentDateTime) - strtotime($beforeStartTime)) / 60 / 60 / 24;
                // Get End Event Date and Time
                $endDateTime = (($currentDate >= $row['date'] && $currentDate <= $row['date_end']) ? $currentDate : $row['date_end'])." ".$row['time_conclusive'];
                // Difference Between Current and End Time Scan
                $diffEndTimes = (strtotime($currentDateTime) - strtotime($endDateTime)) / 60 / 60 / 24;
                // Check if Intervals are Allowed to Scan Attendance
                if ($diffDaysBefore >= 0){
                    $beforeStartScanFlag = true;
                    if ($diffEndTimes >= 2) {
                        $endScanFlag = false;
                    }
                }
            }
            $scanAttendanceErrorFlag = true;
        } else {
            $eventTitle = "ERROR!";
            $scanAttendanceErrorFlag = false;
        }
    }else{
    	$eventTitle = "ERROR!";
        $scanAttendanceErrorFlag = false;
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $eventTitle;?> - 
        <?php
            if ($endScanFlag) {
                echo "Scan Attendance";
            } else {
                echo "List of Attendance";
            }
        ?>
         | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Custom Styles -->
    <style>
        @media only screen and (max-width: 280px) {
            .header-title {
                font-size: 2rem;
            }
        }
    	.container-scan-barcode {
            /*background-color: white;
            border: 10px solid #929eaa!important;*/
            border-radius: 25px;
    	}
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
        .form-control-custom {
            display: initial;
            width: 5rem;
        }
        .modal {
            overflow-y: auto;
        }
        .btn-lg-event-invitee {
            border-width: 3px;
        }
        .btn-lg-event-invitee.collapsed {
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
        <!-- The attendance status snackbar -->
        <div id="snackbar"></div>
        <!-- Main Body -->
    	<div class="container-fluid px-2 pt-2">
    		<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <input type="hidden" id="current-event-ID" value="<?php echo $eventID;?>">
                <h1 class="font-weight-bold header-title" id="current-event-title"><?php echo $eventTitle;?></h1>
                <h2 class="pl-3 font-weight-normal">
                    <?php
                        if ($endScanFlag) {
                            echo "Scan Attendance and Generate Certificate Immediately.";
                        } else {
                            echo "Check the List of Attendance";
                        }
                    ?>
                </h2>
            </div>
            <!-- Go Back to Events Button -->
            <div class="mt-3">
                <a href="events.php" class="ml-3 h4 btn btn-secondary btn-lg-add-event rounded-pill"><i class="fas fa-arrow-circle-left"></i> GO BACK</a>
            </div>
            <?php 
                // Check if the fetch is successful
                if ($scanAttendanceErrorFlag == true) { 
                    // Check if is allowed to scan on before event 1 hour
                    if ($beforeStartScanFlag) {
                        if ($endScanFlag) { ?>
                            <!-- Barcode Reader -->
                            <div class="mx-auto text-center">
                                <button class="btn btn-success btn-lg-add-event" id="scanAttendanceBtn" data-toggle="modal" data-target="#scanAttendanceModal"><i class="fas fa-check"></i> CLICK HERE TO SCAN ATTENDANCE</button>
                            </div>
                        <?php } else { ?>
                            <div class="container mt-2 mb-2 p-3 border-form-override event-invitee-parent">
                                <h4><i style="color: red;" class="fas fa-exclamation-triangle"></i> This event is now concluded, you are no longer to accept or scan attendance.</h4>
                            </div>
                        <?php } ?>
                        <!-- Toggle Between Attendance and Invitee List Buttons -->
                        <div class="container mt-2 mb-2 p-2 border-form-override event-invitee-parent">
                            <div class="row">
                                <!-- Event Details Button -->
                                <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingOne">
                                    <button class="btn btn-success btn-lg-add-event btn-lg-event-invitee collapsed" type="button" data-toggle="collapse" data-target="#collapseAttendanceList" aria-expanded="true" aria-controls="collapseOne" onclick="buttonFlagFunc(0)"><i class="fas fa-clock"></i> ATTENDANCE LIST</button>
                                </div>
                                <!-- Invitees' Details Button -->
                                <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingTwo">
                                    <button class="btn btn-primary btn-lg-add-event btn-lg-event-invitee collapsed" type="button" data-toggle="collapse" data-target="#collapseInviteesList" aria-expanded="false" aria-controls="collapseTwo" onclick="buttonFlagFunc(1)"><i class="fas fa-users"></i> INVITEES LIST</button>
                                </div>
                            </div>
                        </div>
                        <!-- Attendance and Invitees List Accordion -->
                        <div class="container shadow-sm mt-2 mb-4 p-3 border-form-override event-invitee-parent">
                            <h6 id="accordionInfo"><i class="fas fa-info-circle"></i> Please click Attendance or Invitees List Button above to display the table.</h6>
                            <div class="accordion" id="accordionAttendance">
                                <!-- Attendance Part -->
                                <div class="event-attendance-details">
                                    <div id="collapseAttendanceList" class="collapse" aria-labelledby="headingOne" data-parent="#accordionAttendance">
                                        <div class="card-body">
                                            <div class="mb-3 mr-3">
                                                <!-- Attendance List -->
                                                <h4 style="font-size: 2rem;"><i class="fas fa-clock"></i> Attendance List Table</h4>
                                                <div class="col-sm-8 mt-3">
                                                    <select id="dateSelect" class="form-control col-3 d-inline-block">
                                                        <?php
                                                            $iStart = new DateTime($eventDate);
                                                            $iEnd = new DateTime($eventDateEnd);
                                                            $iEnd->modify('+2 day');
                                                            for ($i = $iStart; $i <= $iEnd; $i->modify("+1 day")) {
                                                                echo '<option value="'.$i->format("Y-m-d").'" '.(($i->format("Y-m-d") == $currentDate) ? "selected":"").'>'.$i->format("M d, Y").'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                    <select id="amfmSelect" class="form-control col-2 d-inline-block">
                                                        <option value="am" <?php echo ($currentAMPM == "am") ? "selected":"";?>>AM</option>
                                                        <option value="pm" <?php echo ($currentAMPM == "pm") ? "selected":"";?>>PM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="attendanceList" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th id="attendanceNum">No.</th>
                                                            <th id="attendanceName">Name</th>
                                                            <th id="attendanceInviteeCode">Invitee Code</th>
                                                            <th id="attendanceType">Type</th>
                                                            <th id="attendanceDateTime">Date and Time</th>
                                                            <th id="attendanceSend">
                                                                <button class="btn btn-info btn-lg-add-invitee" id="sendSelectedCertificates" style="display: none; margin-right: 0.5rem;"><i class="fas fa-envelope"></i> SEND</button>
                                                                <input type="checkbox" id="selectAllInvitees">
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-sm">
                                                    <h5><i class="fas fa-user-check"></i> Attendance Checked: <span class="totalPresent">N</span>/<span class="totalInvitees">N</span></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Invitees List Part -->
                                <div class="event-attendance-details">
                                    <div id="collapseInviteesList" class="collapse" aria-labelledby="headingOne" data-parent="#accordionAttendance">
                                        <div class="card-body">
                                            <div class="mb-3 mr-3">
                                                <!-- Invitees List -->
                                                <h4 style="font-size: 2rem;"><i class="fas fa-users"></i> Invitees List Table</h4>
                                                <div class="col-sm-8 mt-3">
                                                    <select id="dateIvtSelect" class="form-control col-3 d-inline-block">
                                                        <?php
                                                            $iStart = new DateTime($eventDate);
                                                            $iEnd = new DateTime($eventDateEnd);
                                                            $iEnd->modify('+2 day');
                                                            for ($i = $iStart; $i <= $iEnd; $i->modify("+1 day")) {
                                                                echo '<option value="'.$i->format("Y-m-d").'" '.(($i->format("Y-m-d") == $currentDate) ? "selected":"").'>'.$i->format("M d, Y").'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                    <select id="amfmIvtSelect" class="form-control col-2 d-inline-block">
                                                        <option value="am" <?php echo ($currentAMPM == "am") ? "selected":"";?>>AM</option>
                                                        <option value="pm" <?php echo ($currentAMPM == "pm") ? "selected":"";?>>PM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="inviteeList" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th id="inviteeNum">No.</th>
                                                            <th id="inviteeStatus">Status</th>
                                                            <th id="inviteeName">Name (Type)</th>
                                                            <th id="inviteeCode">Invitee Code</th>
                                                            <th id="inviteeEmail">Email</th>
                                                            <th id="inviteePhoneNum">Phone No.</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-sm mb-2">
                                                    <h5><i class="fas fa-check-circle"></i> Present: <span class="totalPresent">N</span></h5>
                                                </div>
                                                <div class="col-sm mb-2">
                                                    <h5><i class="fas fa-times-circle"></i> Absent: <span id="totalAbsent">N</span></h5>
                                                </div>
                                                <div class="col-sm">
                                                    <h5><i class="fas fa-users"></i> Total: <span class="totalInvitees">N</span></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- The certificate options modal -->
                        <div  id="certOptionsModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 650px;">
                                <div class="modal-content border-form-override">
                                    <div class="modal-header bg-primary add-edit-invitee-override">
                                        <h5 class="modal-title text-light add-edit-invitee-title" id="exampleAddEditInvitee"><i class="fas fa-cog"></i> Options</h5>
                                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="post" id="certificateOptionForm" onsubmit="return optionConfigInputValidation()">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="certLayout" class="label-add-edit-event">Attendance Config:</label>
                                                        <div class="p-2 time-event">
                                                            <div class="form-group">
                                                                <label class="label-add-edit-event">Generate Certificate:</label>
                                                                <div class="custom-control custom-radio custom-control-inline ml-1">
                                                                    <input type="radio" id="generateCertAuto" name="generateCert" class="custom-control-input" value="Auto" checked>
                                                                    <label class="custom-control-label" for="generateCertAuto">Automatic</label>
                                                                </div>
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                    <input type="radio" id="generateCertManual" name="generateCert" class="custom-control-input" value="Manual">
                                                                    <label class="custom-control-label" for="generateCertManual">Manual</label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="label-add-edit-event">Send Certificate After Attendance:</label>
                                                                <div class="custom-control custom-switch ml-1">
                                                                    <input type="checkbox" class="custom-control-input" id="sendCert" name="sendCert">
                                                                    <label class="custom-control-label" for="sendCert" id="sendCertLabel">No</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="certLayout" class="label-add-edit-event">Certificate Layout:</label>
                                                        <div class="p-2 time-event">
                                                            <div class="form-group">
                                                                <label for="certOrientation" class="label-add-edit-event">
                                                                    Orientation:
                                                                </label>
                                                                <select class="form-control" name="certOrientation" id="certOrientation">
                                                                    <option value='L' selected>Landscape</option>
                                                                    <option value='P'>Portrait</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="certSize" class="label-add-edit-event">
                                                                    Size:
                                                                </label>
                                                                <select class="form-control" name="certSize" id="certSize">
                                                                    <option value='Letter' selected>Letter</option>
                                                                    <option value='A4'>A4</option>
                                                                    <option value='A3'>A3</option>
                                                                    <option value='A5'>A5</option>
                                                                    <option value='Legal'>Legal</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="certBarcodePosition" class="label-add-edit-event">
                                                            Barcode Position:
                                                        </label>
                                                        <div class="form-group">
                                                            <label for="certBarcodePositionX">X:</label>
                                                            <input type="number" class="form-control form-control-custom" id="certBarcodePositionX" name="certBarcodePositionX" min="0" max="300" value="20">
                                                            <label for="certBarcodePositionY">Y:</label>
                                                            <input type="number" class="form-control form-control-custom" id="certBarcodePositionY" name="certBarcodePositionY" min="0" max="300" value="169">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="certLayout" class="label-add-edit-event">Certificate Text Style:</label>
                                                        <div class="p-2 time-event">
                                                            <div class="form-group">
                                                                <label for="certFont" class="label-add-edit-event">
                                                                    Font:
                                                                </label>
                                                                <select class="form-control" name="certFont" id="certFont">
                                                                    <option value='Helvetica' selected>Helvetica</option>
                                                                    <option value='Courier'>Courier</option>
                                                                    <option value='Times'>Times</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="certFontStyle" class="label-add-edit-event">
                                                                    Style:
                                                                </label>
                                                                <select class="form-control" name="certFontStyle" id="certFontStyle">
                                                                    <option value='' selected>Regular</option>
                                                                    <option value='B'>Bold</option>
                                                                    <option value='I'>Italic</option>
                                                                    <option value='U'>Underline</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="certFontSize" class="label-add-edit-event">
                                                                    Size (8-72):
                                                                </label>
                                                                <input type="number" class="form-control form-control-custom" id="certFontSize" name="certFontSize" min="8" max="72" value="30">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="certFontColor" class="label-add-edit-event">
                                                                    Color:
                                                                </label>
                                                                <input type="color" class="form-control form-control-custom" id="certFontColor" name="certFontColor" value="#000000" style="padding: 0.1rem;">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="certTextPosition" class="label-add-edit-event">
                                                                    Position:
                                                                </label>
                                                                <div class="form-group">
                                                                    <label for="certTextPositionX">X:</label>
                                                                    <input type="number" class="form-control form-control-custom" id="certTextPositionX" name="certTextPositionX" min="0" max="300" value="130">
                                                                    <label for="certTextPositionY">Y:</label>
                                                                    <input type="number" class="form-control form-control-custom" id="certTextPositionY" name="certTextPositionY" min="0" max="300" value="79">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                            <button type="button" class="btn btn-primary" id="previewCertificateBtn"><i class="fa fa-eye"></i> Preview</button>
                                            <button type="submit" class="btn btn-success" name="certificateOptionSave" value="Save" id="certificateOptionSave"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                    </form>                 
                                </div>
                            </div>
                        </div>
                        <!-- Preview Certificate Config Modal -->
                        <div class="modal" id="previewCertConfigModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h4 class="modal-title text-light" id="viewCertificateModalTitleName">Preview Certificate</h5>
                                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe id="previewCertFile" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Scan Attendance Modal -->
                        <div class="modal" id="scanAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content container-scan-barcode" id="scanAttendanceContent" style="border: 10px solid #929eaa;">
                                    <div class="modal-header bg-primary add-edit-invitee-override">
                                        <h4 class="modal-title text-light"><?php echo $eventTitle;?> | Scan Attendance</h4>
                                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Camera Mode Content -->
                                        <div id="cameraModeContent">
                                            <!-- Permission Status -->
                                            <div id="permissionStatus" class="text-center" style="font-weight: 700; color: #ff0000;">
                                                <p><i class="fas fa-info-circle"></i> Please approve permission first.</p>
                                            </div>
                                            <!-- QR Code Reader Content -->
                                            <div id="qr-reader-content" style="display: none;">
                                                <div id="qr-reader" class="mb-2" style="max-width: 500px; margin: auto;"></div>
                                                <div id="qrButtonControls" class="text-center">
                                                    <div id="qrStartSelection">
                                                        <select id="cameraSelection" class="form-control col-sm-6 mx-auto mb-1">
                                                            <!-- Camera Selection -->
                                                        </select>
                                                        <button id="startQRCodeScanner" class="btn btn-success mb-1"><i class="fas fa-play"></i> Start Scanning</button>
                                                    </div>
                                                    <button id="stopQRCodeScanner" class="btn btn-danger mb-1" style="display: none;"><i class="fas fa-stop"></i> Stop Scanning</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Code Input  Content-->
                                        <div id="codeInputModeContent" style="display: none;">
                                            <form id="codeInputModeForm">
                                                <div class="form-group text-center container">
                                                    <label for="inviteeCodeInput" class="label-add-edit-event">Please Enter Invitee Code <span id="requiredCodeInputError"></span></label>
                                                    <input type="text" class="form-control mx-auto mb-2" id="inviteeCodeInput" name="inviteeCodeInput" style="max-width: 400px">
                                                    <button type="submit" class="btn btn-success"><i class="fas fa-sign-in-alt"></i> Enter</button>
                                                    <small class="form-text">You can use barcode scanner which is optional.</small>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- Scan Selection Mode -->
                                        <div class="form-group text-center mb-2">
                                            <label for="scanSelectionMode">Scan Mode: </label>
                                            <select id="scanSelectionMode" class="form-control col-sm-6 mx-auto">
                                                <option value="cameraMode">Camera</option>
                                                <option value="codeInputMode">Code Input</option>
                                            </select>
                                        </div>
                                        <!-- Scanned Result Message -->
                                        <div class="p-3 text-light rounded" id="scannedResultMsg" style="display: none">
                                            <p class="font-weight-bolder mb-0">RESULT: <span class="float-right"><a href="javascript:void(0)" class="text-light" id="closeScannedResult" data-toggle="tooltip" title="Close Message" ><i class="fas fa-times"></i></a></span></p>
                                            <p class="mb-0" id="scannedInviteeName"></p>
                                            <p class="mb-0">Code: <span id="scannedInviteeCode"></span></p>
                                            <p class="font-weight-bold mb-0">Status: <span id="scannedInviteeResult"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="container mt-2 mb-2 p-3 border-form-override event-invitee-parent">
                            <h4><i style="color: #ffc107;" class="fas fa-exclamation-triangle"></i> Please wait for the allowable time to scan attendance, hour before the date and start time (<?php echo $eventStartTime;?>). <a href="javascript:void(0)" style="color: green;" onClick="window.location.reload();">Refresh this if is ready to scan.</a></h4>
                        </div>
                    <?php }
                } else { ?>
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
    <!-- Barcode Scanner Script -->
    <script src="dist/html5-qrcode.min.js"></script>
    <!-- Attendance AJAX Script -->
    <script src="scripts/attendance-ajax.js"></script>
    <script>
        var attendanceListBtnFlag = true;
        var inviteesListBtnFlag = true;

        // Get Before Start Event and End Scan Attendance Flag
        var beforeStartScanFlag = <?php echo ($beforeStartScanFlag === true) ? "true" : "false";?>;
        var endScanFlag = <?php echo ($endScanFlag === true) ? "true" : "false";?>;

        // Get Current Event ID
        var eventID = document.getElementById('current-event-ID').value;

        // Get Attendance Status Snackbar
        var statusSnackBar = document.getElementById("snackbar");

        // Get Barcode Reader
        var barcodeReader = document.getElementById("scanAttendanceContent");

        function buttonFlagFunc(flag) {
            if (flag == 1) {
                if (attendanceListBtnFlag == true) {
                    document.getElementById("accordionInfo").style.display = "none";
                    attendanceListBtnFlag = false;
                    inviteesListBtnFlag = true;
                }else{
                    document.getElementById("accordionInfo").style.display = "initial";
                    attendanceListBtnFlag = true;
                    inviteesListBtnFlag = true;
                }
            }else{
                if (inviteesListBtnFlag == true) {
                    document.getElementById("accordionInfo").style.display = "none";
                    inviteesListBtnFlag = false;
                    attendanceListBtnFlag = true;
                }else{
                    document.getElementById("accordionInfo").style.display = "initial";
                    inviteesListBtnFlag = true;
                    attendanceListBtnFlag = true;
                }
            }
        }

        // Change Between Auto and Manual for generating certificate
        $("input[name='generateCert']").click(function() {
            if ($("#generateCertManual").is(":checked")) {
                $("input[name='sendCert']").prop({
                    'checked': false,
                    'disabled': true
                });
                $("#sendCertLabel").html("No");
            } else {
                $("input[name='sendCert']").prop('disabled', false);
            }
        });

        // Change Send Certificate Label
        $("input[name='sendCert']").click(function() {
            if ($(this).is(":checked")) {
                $("#sendCertLabel").html("Yes");
            } else {
                $("#sendCertLabel").html("No");
            }
        });

        // Option Config Input Validation
        function optionConfigInputValidation(){
            var valid = true;

            var certBarcodePositionX = $("#certBarcodePositionX").val();
            var certBarcodePositionY = $("#certBarcodePositionY").val();
            var certFontSize = $("#certFontSize").val();
            var certTextPositionX = $("#certTextPositionX").val();
            var certTextPositionY = $("#certTextPositionY").val();

            if(certBarcodePositionX.trim() == ""){
                $("#certBarcodePositionX").val("0");
                valid = false;
            }
            if(certBarcodePositionY.trim() == ""){
                $("#certBarcodePositionY").val("0");
                valid = false;
            }
            if(certFontSize.trim() == ""){
               $("#certFontSize").val("8");
                valid = false;
            }
            if(certTextPositionX.trim() == ""){
                $("#certTextPositionX").val("0");
                valid = false;
            }
            if(certTextPositionY.trim() == ""){
                $("#certTextPositionY").val("0");
                valid = false;
            }
            return valid;
        }
    </script>
</body>
</html>