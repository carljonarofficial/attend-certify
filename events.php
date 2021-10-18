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

    // Using database connection file here
    include 'dbConnection.php';

    // Set session
    session_start();
    if(isset($_POST['records-limit'])){
        $_SESSION['records-limit'] = $_POST['records-limit'];
    }
    if (isset($_POST['searchEvent'])) {
        $_SESSION['searchEvent'] = $_POST['searchEvent'];
    }

    // Fetch all event records with limit to display
    $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 5;
    $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
    $searchInput = isset($_SESSION['searchEvent']) ? $_SESSION['searchEvent']: '';
    $searchLength = strlen($searchInput);
    $search = "$searchInput%";
    $paginationStart = ($page - 1) * $limit;
    $eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `status` = 1 AND `event_title` LIKE ? LIMIT ?, ?");
    $eventStmt->bind_param('isii', $id, $search, $paginationStart, $limit);
    $eventStmt->execute();
    $events =  $eventStmt->get_result();
    $eventStmt->close();

    // Get the number of records
    $eventNumsStmt = $conn->prepare("SELECT COUNT(*) as 'num_row' FROM `events` WHERE `admin_ID` = ? AND `status` = 1 AND `event_title` LIKE ?");
    $eventNumsStmt->bind_param('is', $id, $search);
    $eventNumsStmt->execute();
    $eventNums = $eventNumsStmt->get_result();
    $eventNumsStmt->close();
    while($row = $eventNums->fetch_assoc())
    {
        $allRecords = $row['num_row'];
    }

    // Calculate total pages
    $totalPages = ceil($allRecords / $limit);

    // Previous + Next
    $prev = $page - 1;
    $next = $page + 1;

    $certTemplates = array();

    // Event increment
    $increment = 1;

    // If the admin click yes to delete an event, then it will proceed here
    use Eventpot\EventMember;
    if (! empty($_POST["deleteEvent-btn"])) {
        require_once './model/event-member.php';
        $member = new EventMember();
        $addResponse = $member->deleteEvent();
    }

    // If the admin click scan event attendance, then it will proceed here
    if (!empty($_POST['scanAttendance'])) {
        session_start();
        unset($_SESSION["attendance-present-id"]);
        session_start();
        $_SESSION["attendance-present-id"] = $_POST['scanAttendance'];
        session_write_close();
        header("Location: scan-attendance.php");
    }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Events | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>
    <!-- Modified Modal Style -->
    <style>
        .modal {
            overflow-y: auto;
        }
    </style>
</head>
<body class="d-flex flex-column" >
	<?php 
        // Initialize Active Page for Navbar Highlight
        $activePage = "events";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>

    <div class="main-body container-fluid flex-grow-1 mt-5">
    	<div class="container-fluid px-2 pt-2">
            <!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold">EVENTS</h1>
                <h2 class="pl-3 font-weight-normal">Your saved events repository.</h2>
            </div>
            <!-- Validate Event if Successful Added into Database -->
            <?php
                if (!empty($eventcrud_msg)) {
                    if ($eventcrud_msg == "add-success") {?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-success text-light response">
                            <h4>ADD EVENT SUCCESSFULLY</h4>
                        </div>
                    <?php } else if ($eventcrud_msg == "edit-success"){?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-success text-light response">
                            <h4>EDIT EVENT SUCCESSFULLY</h4>
                        </div>
                    <?php } else if ($eventcrud_msg == "delete-success"){?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-danger text-light response">
                            <h4>DELETE EVENT SUCCESSFULLY</h4>
                        </div>
                    <?php } else if ($eventcrud_msg == "delete-error"){?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-danger text-light response">
                            <h4>DELETE ERROR, PLEASE TRY AGAIN</h4>
                        </div>
                    <?php } else if ($eventcrud_msg == "edit-error") {?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-danger text-light response">
                            <h4>EDIT ERROR, PLEASE TRY AGAIN</h4>
                        </div>
                    <?php } else if ($eventcrud_msg == "existing") {?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-warning text-light response">
                            <h4>YOUR DATE AND TIME HAVE EXISITING EVENT, PLEASE SET NEW ONE</h4>
                        </div>
                    <?php }
                }
            ?>
            <!-- Add Event Button -->
            <div class="mt-3">
                <a href="add-event.php" class="ml-3 h4 btn btn-success btn-lg-add-event rounded-pill"><i class="fa fa-plus-circle"></i> ADD EVENT</a>
            </div>
            <div class="mb-3 mr-3">
                <div class="row">
                    <!-- Search Event -->
                    <div class="col-sm-4 mt-3 mr-3">
                        <form action="events" method="post">
                            <div class="input-group ml-3">
                                <input type="text" class="form-control" placeholder="Search Event" name="searchEvent" id="searchEvent" value="<?php if($searchLength > 0){ echo $searchInput;}?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php 
                        if($allRecords > 5){?>
                            <!-- Select dropdown -->
                            <div class="col mt-3">
                                <div class="d-flex flex-row-reverse bd-highlight">
                                    <form action="events.php" method="post">
                                        <select name="records-limit" id="records-limit" class="form-control custom-select">
                                            <option disabled selected>Display Limit</option>
                                            <?php foreach([5,7,10,12] as $limit) : ?>
                                            <option
                                                <?php if(isset($_SESSION['records-limit']) && $_SESSION['records-limit'] == $limit) echo 'selected'; ?>
                                                value="<?= $limit; ?>">
                                                <?= $limit; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        <?php }
                    ?>
                </div>
            </div>
            <div class="container event-container">
                <?php
                    if($searchLength > 0){?>
                        <!-- Clear Search Button -->
                        <div class="mt-3">
                            <form action="events" method="post">
                                <input type="hidden" class="form-control" placeholder="Search Event" name="searchEvent" id="searchEvent" value="">
                                <button class="ml-3 h6 btn btn-danger rounded-pill" type="submit"><i class="fas fa-times"></i> CLEAR SEARCH</button>
                            </form>
                        </div>
                    <?php }
                ?>
                <?php 
                    if ($allRecords > 0) { ?>
                        <div class="row">
                            <?php
                                while ($row = $events->fetch_assoc()) {
                                    array_push($certTemplates, $row['certificate_template']);?>
                                    <!-- Event Card -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card hovercard shadow-sm">
                                            <div class="card-header p-0">
                                                <img class="card-img-top" src="img/assets/card_01.png" alt="Card image cap">
                                            </div>
                                            <div class="card-body info bg-light">
                                                <div class="card-title text-left font-weight-bolder">
                                                    <h4 class="event-title"><a href="#" data-toggle="modal" data-target="#eventModal<?php echo $increment; ?>"><?php echo $row['event_title'];?></a></h4>
                                                </div>
                                                <div class="card-title text-left">
                                                    <h6><i class="fa fa-calendar"></i> Date: <?php echo date_format(date_create($row['date']),"M d, Y");?></h6>
                                                </div>
                                                <div class="card-title text-left">
                                                    <h6><i class="fa fa-clock-o"></i> Time: <?php echo date_format(date_create($row['time_inclusive']),"h:iA");?> - <?php echo date_format(date_create($row['time_conclusive']),"h:iA");?></h6>
                                                </div>
                                                <div class="card-title text-justify">
                                                    <h6 class="event-venue"><i class="fa fa-map-marker"></i> Venue: <?php echo $row['venue'];?></h6>
                                                </div>
                                            </div>
                                            <div class="card-footer event-footer row align-items-center justify-content-center">
                                                <div>
                                                    <button type="button" class="btn btn-success h5" style="width: 95px;" onclick="location.href='./scan-attendance.php?eventID=<?php echo $row['ID']?>';"><i class="fas fa-barcode"></i> Scan</button><br>
                                                    <button type="button" class="btn btn-warning h5" style="width: 95px;" onclick="location.href='./edit-event.php?eventID=<?php echo $row['ID']?>';"><i class="fas fa-edit"></i> Edit</button><br>
                                                    <button type="button" class="btn btn-danger h5" data-toggle="modal" data-target="#deleteModal<?php echo $increment; ?>" style="width: 95px;"><i class="fas fa-trash"></i> Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Delete Modal -->
                                    <div class="modal" id="deleteModal<?php echo $increment; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h4 class="modal-title text-light" id="exampleModalDelete"><i class="fas fa-exclamation-triangle"></i> Are you sure to delete this event?</h5>
                                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5><i class="fa fa-calendar"></i> Title: <?php echo $row['event_title'];?></h5>
                                                    <p>After you delete this. You will not able to retrieve this, please be careful.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form name="deleteEvent" action="" method="post">
                                                        <input type="hidden" name='eventId' value="<?php echo $row['ID']; ?>" />
                                                        <button type="submit" name="deleteEvent-btn" id="deleteEvent-btn" value="Delete Event" class="btn btn-danger"><i class="fas fa-trash"></i> Yes</button>
                                                    </form>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-circle-left"></i> No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Event Modal -->
                                    <div class="modal" id="eventModal<?php echo $increment; ?>" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle" aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h4 class="modal-title text-light" id="exampleModalLongTitle"><?php echo $row['event_title'];?></h5>
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
                                                                        <h5><i class="fa fa-calendar"></i> Date:</h5>
                                                                        <p><?php echo date_format(date_create($row['date']),"F d, Y");?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-clock-o"></i> Time:</h5>
                                                                        <p><?php echo date_format(date_create($row['time_inclusive']),"h:iA");?> - <?php echo date_format(date_create($row['time_conclusive']),"h:iA");?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-map-marker"></i> Venue:</h5>
                                                                        <p><?php echo $row['venue'];?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-certificate"></i> Certificate Template:</h5>
                                                                        <input type="file" name="certAttachment" id="certAttachment<?php echo $increment; ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col m-1">
                                                            <div class="card bg-info-event-modal h-100 pt-0">
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-info-circle"></i> Description:</h5>
                                                                        <p><?php echo $row['description'];?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-tasks"></i> Agenda:</h5>
                                                                        <p><?php echo $row['agenda'];?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-quote-left"></i> Theme:</h5>
                                                                        <p><?php echo $row['theme'];?></p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                         <h5><i class="fas fa-hourglass-start"></i> Date and Time Added:</h5>
                                                                         <p><?php echo date_format(date_create($row['datetime_added']),"M d, Y h:iA");?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                                    <button type="button" name="eventDetails" id="eventDetails" class="btn btn-primary" onclick="location.href='./event-details.php?eventID=<?php echo $row['ID']?>';"><i class="fas fa-eye"></i> View Details</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php $increment++; }
                            ?>
                        </div>
                        <?php
                        if ($allRecords > 5) {?>
                            <!-- Pagination -->
                            <div class="container-fluid">
                                <nav aria-label="Event Pagination" id="event-pagination">
                                    <ul class="pagination">
                                        <!-- Previous Page Button -->
                                        <li class="page-item m-2 <?php if($page <= 1){ echo 'disabled'; } ?>">
                                            <a class="page-link rounded-left-event font-weight-bold" href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>">Previous</a>
                                        </li>
                                        <!-- N Page Button -->
                                        <?php for($i = 1; $i <= $totalPages; $i++ ): ?>
                                            <li class="page-item m-2 <?php if($page == $i) {echo 'active'; } ?>">
                                                <a class="page-link font-weight-bold" href="events.php?page=<?= $i; ?>"> <?= $i; ?> </a>
                                            </li>
                                        <?php endfor; ?>
                                        <!-- Next Page Button -->
                                        <li class="page-item m-2 <?php if($page >= $totalPages) { echo 'disabled'; } ?>">
                                            <a class="page-link rounded-right-event font-weight-bold" href="<?php if($page >= $totalPages){ echo '#'; } else {echo "?page=". $next; } ?>">Next</a>
                                        </li>
                                      </ul>
                                </nav>
                            </div>
                        <?php }
                    ?>
                    <?php } else { ?>
                        <!-- If no record is found, then the add one message appear. -->
                        <div class="w-100 p-3 mt-3 mb-5 shadow-sm rounded bg-info text-light response">
                            <h4>No event, please add one.</h4>
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
        // File Upload Script
        var $js_array =<?php echo json_encode($certTemplates);?>;
        for (var i = 0; i < <?php echo $allRecords; ?>; i++) {
            var $certAttach = "#certAttachment";
            var $iDString = $certAttach.concat((i+1).toString());
            var $certFileType = $js_array[i].substring($js_array[i].indexOf(".")+1);
            $($iDString).fileinput({
                theme: 'fas',
                showRemove:false,
                showUpload: false,
                showZoom: true,
                showClose: false,
                dropZoneEnabled: false,
                allowedFileExtensions: ['docx', 'pdf', 'jpeg', 'jpg', 'png'],
                required:true,
                initialPreview: [// PDF DATA
                    'http://localhost/attend-certify/certificate-templates/' + $js_array[i]
                ],
                initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
                initialPreviewConfig: [{type: $certFileType, caption: $js_array[i], downloadUrl: 'http://localhost/attend-certify/certificate-templates/' + $js_array[i]}, // disable download
                    ],
                initialPreviewShowDelete: false,
                showBrowse: false,
                showCaption: false,
                showClose: false
            }).on('filepreupload', function(event, data, previewId, index) {
                alert('The description entered is:\n\n' + ($('#description').val() || ' NULL'));
            });
        }
        
        // Record Limit Script
        $(document).ready(function () {
            $('#records-limit').change(function () {
                $('form').submit();
            })
        });
    </script>
</body>
</html>