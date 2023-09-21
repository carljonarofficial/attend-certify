<?php
    // User Flag
    $userFlag = false;

    // Get the Redirect if is user
    if (isset($_GET['user'])) {
        $userFlag = true;
    } else {
        // Validate if the admin logged in
        include 'validateLogin.php';

        // Using database connection file here
        include 'dbConnection.php';

        $eventData = array();
        $eventsQuery = $conn->query("SELECT * FROM `events` WHERE `admin_ID` = $id AND `status` = 1");
        while ($row = $eventsQuery->fetch_assoc()) {
            $eventInfo = array();
            $eventInfo[] = $row['ID'];
            $eventInfo[] = $row["event_title"];
            $eventData[] = $eventInfo;
        }
    }
    // Check if is already logged in
	$adminAccountName = "";
	$loggedIn = false;
    if ($userFlag == true) {
        session_start();
    }
    if (isset($_SESSION["username"])) {
    	$username = $_SESSION["username"];
    	$loggedIn = true;

        // Check if Full Name Session Exists
        if (isset($_SESSION["fullName"])) {
            $adminAccountName = $_SESSION["fullName"];
        } else {
            $adminAccountName = $username;
        }
    	$buttonStr = '<a href="home" class="btn btn-success" style="font-size: 2rem;"><i class="fas fa-home"></i> Go to Home Page</a>';
    } else {
    	$buttonStr = '<a href="login" class="btn btn-primary" style="font-size: 2rem;"><i class="fas fa-sign-in-alt"></i> Login to Continue</a>';
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>
        <?php
            if ($userFlag == true) {
                echo "User ";
            }
        ?>
        Certificates | Attend and Certify
    </title>

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
        .certificate-btn {
            border-width: 3px;
        }
        .certificate-btn.collapsed {
            background-color: #fff;
            color:  #000;
        }
        .container-scan-barcode {
            border-radius: 25px;
        }
        .accordion-override {
            background-color: white;
            border-radius: 25px;
        }
        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
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
        .viewSearchedCertificate {
            text-align: center;
            max-width: 100px;
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
        $activePage = "certificates";

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
        <!-- The certificate status snackbar -->
        <div id="snackbar"></div>
        <!-- Main Body -->
        <div class="container-fluid px-2 pt-2">
            <!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold header-title">
                    <?php
                        if ($userFlag == true) {
                            echo "USER ";
                        }
                    ?>
                    CERTIFICATES
                </h1>
                <h2 class="pl-3 font-weight-normal">
                     <?php
                        if ($userFlag == true) {
                            echo "Get your own certificate/s after the event has been concluded.";
                        } else {
                            echo "View generated certificates and/or validate them.";
                        }
                     ?>
                </h2>
            </div>
            <!-- Enter invitee code or Select Event to view certificates and/or validate them -->
            <div class="container shadow-sm p-3 my-3 mt-4 border-form-override">
                <?php
                    if ($userFlag == true) { ?>
                        <!-- Enter your code -->
                        <div class="mx-auto text-center">
                            <button class="btn btn-success btn-lg-add-event" id="enterIVTCodeBtn" data-toggle="modal" data-target="#userInviteeCodeModal"><i class="fas fa-barcode"></i> CLICK HERE TO ENTER YOUR CODE</button>
                        </div>
                    <?php } else { ?>
                        <h6 id="eventSelectionInfo"><i class="fas fa-info-circle"></i> Please select event below to view certificates or validate.</h6>
                        <select id="selectEvent" class="form-control" aria-label="Event-select">
                            <option selected="true" disabled="disabled">Open this select menu to choose event.</option>
                            <?php
                                $eventCount = count($eventData);
                                for ($i=0; $i < $eventCount; $i++) { ?>
                                    <option value="<?php echo $eventData[$i][0];?>"><?php echo $eventData[$i][1]?></option>
                                <?php }
                            ?>
                        </select>
                    <?php }
                ?>
            </div>
            <?php
                if ($userFlag == true) { ?>
                    <!-- Enter User Invitee Code Barcode Modal -->
                    <div class="modal" id="userInviteeCodeModal" tabindex="-1" role="dialog" aria-labelledby="ivtModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content container-scan-barcode" id="validateInviteeCodeContent" style="border: 10px solid #929eaa;">
                                <div class="modal-header bg-primary add-edit-invitee-override">
                                    <h4 class="modal-title text-light">Enter your Invitee Code</h4>
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
                                                <label for="ceritifcateCodeInput" class="label-add-edit-event">Please Enter your Invitee Code <span id="requiredCodeInputError"></span></label>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Certificate Canvas Container -->
                    <div class="container shadow-sm my-3 mt-4 border-form-override" id="certCanvasContainer" style="display: none;">
                        <!-- Scanned Result Message -->
                        <div class="p-3 my-3 text-light rounded" id="scannedResultMsg">
                            <p class="font-weight-bolder mb-0">Result:</p>
                            <p class="mb-0" id="scannedCertificateName"></p>
                            <p class="mb-0">Code: <span id="scannedCertificateCode"></span></p>
                            <p class="mb-0">Status: <span id="scannedCertificateResult"></span></p>
                        </div>
                        <div class="embed-responsive embed-responsive-16by9 mb-3" id="certFileFrame">
                            <iframe id="certificateFile" class="embed-responsive-item" src="" allowfullscreen></iframe>
                        </div>
                        <div id="searchCertsTable" style="display: none;">
                            <table id="searchCertsList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Event Title</th>
                                        <th>View Certificate</th>
                                    </tr>
                                </thead>
                                <tbody id="certsListRow"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- View Certificate Modal -->
                    <div class="modal" id="viewCertificateModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title text-light" id="viewSearchCertificateNameTitle"></h4>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe id="searchCertificateFile" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div id="main-cerficate-validate" style="display: none;">
                    <!-- Toggle Between Certificates and Validate Buttons -->
                    <div class="container mt-2 mb-2 p-2 border-form-override event-invitee-parent">
                        <div class="row">
                            <!-- Certificate Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingOne">
                                <button class="btn btn-success btn-lg-add-event btn-lg-event-invitee certificate-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseCertificate" aria-expanded="true" aria-controls="collapseOne" onclick="buttonFlagFunc()"><i class="fas fa-certificate"></i> CERTIFICATES</button>
                            </div>
                            <!-- Validate Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingTwo">
                                <button class="btn btn-primary btn-lg-add-event btn-lg-event-invitee certificate-btn" type="button" id="validateCertBtn" data-toggle="modal" data-target="#validateCertificateBarcodeModal"><i class="far fa-check-square"></i> VALIDATE</button>
                            </div>
                        </div>
                    </div>
                    <!-- Certificates and Validate Accordion -->
                    <div id="barcode-reader" class="container accordion-override shadow-sm mt-2 mb-4 p-3 event-invitee-parent" style="border: 10px solid #78a6ed!important">
                        <h6 id="accordionInfo"><i class="fas fa-info-circle"></i> Please click Certificate or Validate Button above to display the content.</h6>
                        <div class="accordion" id="accordionCertificate">
                            <!-- Certificate Part -->
                            <div class="event-attendance-details">
                                <div id="collapseCertificate" class="collapse" aria-labelledby="headingOne" data-parent="#accordionCertificate">
                                    <div class="card-body">
                                        <div class="mb-3 mr-3">
                                            <!-- Certificates List Table -->
                                            <h4 style="font-size: 2rem;"><i class="fas fa-certificate"></i> Certificates List Table</h4>
                                        </div>
                                        <!-- Certificate List Table -->
                                        <div class="table-responsive">
                                            <table id="certificateList" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th id="certificateView"></th>
                                                        <th id="certificateNum">No.</th>
                                                        <th id="certificateInviteeName">Name</th>
                                                        <th id="certificateInviteeCode">Invitee Code</th>
                                                        <th id="certificateCode">Certificate Code</th>
                                                        <th id="certificateDateTime">Date and Time Generated</th>
                                                        <th id="certificateSend">
                                                            <button class="btn btn-info btn-lg-add-invitee" id="sendSelectedCertificates" style="display: none; margin-right: 0.5rem;"><i class="fas fa-envelope"></i> SEND</button>
                                                            <input type="checkbox" id="selectAllInvitees">
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <!-- Total Number of Certificates -->
                                        <div class="row mt-3">
                                            <div class="col-sm">
                                                <h5><i class="fas fa-certificate"></i> Total Certificates Generated: <span id="totalCertificates">N</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- The certificate options modal -->
                    <div  id="certOptionsModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 600px;">
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
                    <!-- Validate Certificate Barcode Modal -->
                    <div class="modal" id="validateCertificateBarcodeModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content container-scan-barcode" id="validateCertificateContent" style="border: 10px solid #929eaa;">
                                <div class="modal-header bg-primary add-edit-invitee-override">
                                    <h4 class="modal-title text-light"><span id="titleValidateCert"></span> | Scan to Validate Certificate Barcode</h4>
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
                                                <label for="ceritifcateCodeInput" class="label-add-edit-event">Please Enter Certificate Code <span id="requiredCodeInputError"></span></label>
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
                                        <p class="font-weight-bolder mb-0">Result: <span class="float-right"><a href="javascript:void(0)" class="text-light" id="closeScannedResult" data-toggle="tooltip" title="Close Message" ><i class="fas fa-times"></i></a></span></p>
                                        <p class="mb-0" id="scannedCertificateName"></p>
                                        <p class="mb-0">Code: <span id="scannedCertificateCode"></span></p>
                                        <p class="mb-0">Status: <span id="scannedCertificateResult"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View Certificate Modal -->
                    <div class="modal" id="viewCertificateModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title text-light" id="viewCertificateModalTitleName"><span id="viewCertficateName">First_Name Middle_Name Last_Name</span> | <span id="viewEventTitle">Event_Title</span></h4>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe id="certificateFile" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }
            ?>
        </div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Barcode Scanner Script -->
    <script src="dist/html5-qrcode.min.js"></script>
    <?php
        if ($userFlag != true) { ?>
            <!-- Certificate AJAX Script -->
            <script src="scripts/certificate-ajax.js"></script>
        <?php }
    ?>
    <script>
        var certificateBtnFlag = true;

        // Get Certificate Status Snackbar
        var statusSnackBar = document.getElementById("snackbar");

        // Get Barcode Reader
        var barcodeReader = document.getElementById("validateCertificateContent");

        function buttonFlagFunc() {
            if (certificateBtnFlag == true) {
                document.getElementById("accordionInfo").style.display = "none";
                certificateBtnFlag = false;
            } else {
                document.getElementById("accordionInfo").style.display = "initial";
                certificateBtnFlag = true;
            }
        }

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

        <?php
            if ($userFlag == true) {
                if (isset($_GET["code"])) {
                    echo "getUserCert('".$_GET["code"]."');";
                } ?>

                // Initialize QR Code Reader
                const html5QrCode = new Html5Qrcode("qr-reader", { 
                    formatsToSupport: [ Html5QrcodeSupportedFormats.PDF_417 ] 
                });

                // Configure QR Code Reader
                const config = {
                    fps: 10,
                    qrbox: {
                        width: 280,
                        height: 200
                    } 
                };

                // Scan Attendance Camera Flag
                var camModeFlag = false;

                // Handle QR Code Success Scan
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    if (decodedText != lastResult) {
                        lastResult = decodedText;
                        getUserCert(decodedText);
                    }
                };

                // Initialize Camera IDs and Labels
                var cameraIDs = new Array();
                var cameraLabels = new Array();

                // This method will trigger user permissions
                Html5Qrcode.getCameras().then(devices => {
                    /**
                     * devices would be an array of objects of type:
                     * { id: "id", label: "label" }
                     */
                    if (devices && devices.length) {
                        // Fetch Camera IDs into Array
                        cameraIDs = devices.map(function(value) {
                            return value.id;
                        });
                        cameraLabels = devices.map(function(value) {
                            return value.label;
                        });
                        // Assign Camera to Drop Down List
                        $('#cameraSelection').html(function () {
                            var tempStr = ""
                            for (var i = 0; i < cameraIDs.length; i++) {
                                tempStr += "<option value='" + cameraIDs[i] + "'>" + cameraLabels[i] + "</option>"
                            }
                            return tempStr;
                        });
                        // Show Camera Content If Permission Granted
                        $("#permissionStatus").hide(0, function () {
                            $("#qr-reader-content").show();
                        });
                    }
                }).catch(err => {
                    $("#permissionStatus").html("<i class='fas fa-ban'></i> Permission denied.");
                });

                // Start Scanning
                $("#startQRCodeScanner").click(function () {
                    $("#qr-reader").show(0, function() {
                        $("#qrStartSelection").hide(0, function() {
                            $("#stopQRCodeScanner").show(0, function () {
                                html5QrCode.start({
                                    deviceId: {
                                        exact: $("#cameraSelection").val(),
                                    } 
                                }, config, qrCodeSuccessCallback);
                                lastResult = "";
                                camModeFlag = true;
                            });
                        });
                    });
                });

                // Stop Scanning
                $("#stopQRCodeScanner").click(function () {
                    html5QrCode.stop().then((ignore) => {
                        $("#qr-reader").hide(0, function () {
                            $("#stopQRCodeScanner").hide(0, function () {
                                $("#qrStartSelection").show();
                                camModeFlag = false;
                            });
                        });
                    }).catch((err) => {
                        console.log("Stop Error");
                    });
                });

                // Enter Invitee Code Submit
                $("#codeInputModeForm").submit(function(event) {
                    event.preventDefault();
                    // Get Input Code
                    var inputCode = $("input[name='inviteeCodeInput']").val().trim();
                    // Clear Input and Error Message
                    $("input[name='inviteeCodeInput']").val("");
                    $("#requiredCodeInputError").html("");
                    if (inputCode == "") {
                        $("#requiredCodeInputError").html("* Required").css('color', '#ee0000');
                    } else {
                        lastResult = inputCode;
                        getUserCert(inputCode);
                    }
                });

                // Hide Modal Event
                $('#userInviteeCodeModal').on('hide.bs.modal', function (e) {
                    // Check if Camera is Not Running
                    if (camModeFlag == true) {
                        html5QrCode.stop().then((ignore) => {
                            $("#qr-reader").hide(0, function () {
                                $("#stopQRCodeScanner").hide(0, function () {
                                    $("#qrStartSelection").show();
                                    camModeFlag = false;
                                });
                            });
                        }).catch((err) => {
                            console.log("Stop Error");
                        });
                    }
                });

                // Change Scan Attendance Mode
                $("#scanSelectionMode").change(function() {
                    // Check if Camera is Not Running
                    if (camModeFlag == true) {
                        html5QrCode.stop().then((ignore) => {
                            $("#qr-reader").hide(0, function () {
                                $("#stopQRCodeScanner").hide(0, function () {
                                    $("#qrStartSelection").show();
                                    camModeFlag = false;
                                });
                            });
                        }).catch((err) => {
                            console.log("Stop Error");
                        });
                    }
                    if ($("#scanSelectionMode").val() == "cameraMode") {
                        $("#codeInputModeContent").hide();
                        $("#cameraModeContent").show();
                    } else {
                        $("input[name='inviteeCodeInput']").val("");
                        $("#cameraModeContent").hide();
                        $("#codeInputModeContent").show();
                    }
                });

                // Get User Certificate Function
                function getUserCert(ivtCodeInput) {
                    $.ajax({
                        url: "certificate-action",
                        method: "POST",
                        dataType: "json",
                        data: {
                            certificateAction:'getUserCertificate',
                            inviteeCode: ivtCodeInput
                        },
                        beforeSend: function() {
                            $("#loadingModal").modal('show');
                            $("#searchCertsTable, #certFileFrame").hide();
                            $('#certificateFile').attr("src",'');
                        }
                    }).done(function(data) {
                        $("#loadingModal, #userInviteeCodeModal").modal('hide');
                        if (data.queryStatus == true) {
                            if (data.ivtStatus != "not-existing") {
                               if (data.ivtStatus == "available") {
                                    displyScannedResult(data.ivtName, ivtCodeInput, "#5cb85c", "Available");
                                    $('#certificateFile').attr("src",'data:application/pdf;base64,' + data.base64Cert);
                                    $("#certFileFrame").show();
                               } else {
                                   displyScannedResult(data.ivtName, ivtCodeInput, "#f0ad4e", "Not yet Available");
                                   $("#certFileFrame").hide();
                               }
                            } else {
                                console.log("Not existing");
                                displyScannedResult("Certificate doesn't exists.", ivtCodeInput, "#d9534f", "Invalid");
                                $("#certFileFrame").hide();
                            }
                        } else {
                            displyScannedResult("An error has occured, please try again.", "Error", "#d9534f", "Error");
                            $("#certFileFrame").hide();
                        }
                    }).fail(function() {
                        $("#loadingModal, #userInviteeCodeModal").modal('hide');
                        displyScannedResult("Error", "Error", "#d9534f", "Error");
                        $("#certFileFrame").hide();
                    });
                }

                // Display Scanned Invitee Name Result
                function displyScannedResult(name, code, bg_color, status) {
                    $("#scannedCertificateName").html(name);
                    $("#scannedCertificateCode").html(code);
                    $("#scannedCertificateResult").html(status);
                    $("#scannedResultMsg").css('background-color', bg_color).show();
                    $("#certCanvasContainer").show();
                }

                <?php if (isset($_SESSION["username"])) {
                    $emailAddress = $_SESSION["email"];
                    echo "getUserCertFunc('$emailAddress', '$username')";
                }?>

                // Get User Certificates Function
                function getUserCertFunc(email, userName) {
                    $.ajax({
                        url: "certificate-action",
                        method: "POST",
                        dataType: "json",
                        data: {
                            certificateAction:'getSearchCertsList',
                            searchEmail: email
                        },
                        beforeSend: function() {
                            $("#loadingModal").modal('show');
                            $("#searchCertsTable, #certFileFrame").hide();
                            $("#certsListRow").html("");
                        }
                    }).done(function (data) {
                        $("#loadingModal, #userInviteeCodeModal").modal('hide');
                        if (data.status == "success") {
                            if (data.length > 0) {
                                $("#certsListRow").html(data.resultStr);
                                $("#searchCertsTable").show();
                                displyScannedResult(userName, "N/A", "#5cb85c", data.length + " Certificate/s Found");
                            } else {
                                displyScannedResult(userName, "N/A", "#f0ad4e", "No Certificate/s Found");
                            }
                        } else {
                            displyScannedResult("An error has occured, please try again.", "Error", "#d9534f", "Error");
                        }
                    }).fail(function() {
                        $("#loadingModal, #userInviteeCodeModal").modal('hide');
                        displyScannedResult("Error", "Error", "#d9534f", "Error");
                    });
                }

                // Get Certificate Event
                $("#certsListRow").on("click", ".viewSearchedCertificate", function () {
                    var eventTitle = $(this).parent().prev().prev().html();
                    $.ajax({
                        url: "certificate-action",
                        method: "POST",
                        dataType: "json",
                        data: {
                            certificateAction:'getUserCertificate',
                            inviteeCode: $(this).val()
                        },
                        beforeSend: function() {
                            $("#loadingModal").modal('show');
                            $('#searchCertificateFile').attr("src",'');
                        }
                    }).done(function (data) {
                        $("#loadingModal").modal('hide');
                        if (data.queryStatus == true) {
                            if (data.ivtStatus != "not-existing") {
                               if (data.ivtStatus == "available") {
                                    $('#searchCertificateFile').attr("src",'data:application/pdf;base64,' + data.base64Cert);
                                    $("#viewSearchCertificateNameTitle").html(data.ivtName + " | " + eventTitle);
                                    $("#viewCertificateModal").modal("show");
                               } 
                            } 
                        } 
                    }).fail(function () {
                        $("#loadingModal").modal('hide');
                    });
                });
            <?php }
        ?>
    </script>
</body>
</html>