<?php
    include 'validateLogin.php';

    // Validate Add Event Session
    $eventcrud_msg = "";
    session_start();
    if (isset($_SESSION["event-crud-validation-msg"])) {
        $eventcrud_msg = $_SESSION["event-crud-validation-msg"];
    }
    unset($_SESSION["event-crud-validation-msg"]);
    session_write_close();

    // Set session
    session_start();
    if(isset($_POST['records-limit'])){
        $_SESSION['records-limit'] = $_POST['records-limit'];
    }

    $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 5;
    $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
    $paginationStart = ($page - 1) * $limit;

    // Calculate total pages
    $totalPages = 5;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Events - Attend and Certify</title>
		<?php 
            include 'style/style.php';
            // the include or require statement takes all the text/code/markup that exists in the specified file	
        ?>
	</head>
	<body style="background-image: url('style/img/background_add.jpeg')">
		<?php 
            include 'model/navbar.php';
            // the include or require statement takes all the text/code/markup that exists in the specified file	
        ?>
        <br><br><br>
        <div class="container-fluid">
        	<div class="container-fluid px-2 pt-2">
                <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                    <h1 class="font-weight-bold">EVENTS</h1>
                    <h2 class="pl-3 font-weight-normal">Your saved events repository.</h2>
                </div>
                <!-- Validate Event if Successful Added into Database -->
                <?php
                    if (!empty($eventcrud_msg)) {?>
                        <div class="w-100 p-3 mt-3 shadow-sm rounded bg-success text-light response">
                            <h4>ADD EVENT SUCCESSFULLY</h4>
                        </div>
                    <?php }
                ?>
                <!-- Add Event Button -->
                <div class="mt-3">
                    <a href="add-event.php" class="ml-3 h4 btn btn-success btn-lg-add-event rounded-pill"><i class="fa fa-plus-circle"></i> ADD EVENT</a>
                </div>
                <div class="mb-3 mr-3">
                    <div class="row">
                        <!-- Search Event -->
                        <div class="col-sm-4 mt-3 mr-3">
                            <div class="input-group ml-3">
                                <input type="text" class="form-control" placeholder="Search Event" name="searchEvent" id="searchEvent">
                                <div class="input-group-append bg-light">
                                    <button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
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
                    </div>
                </div>
                <div class="container">
                    <?php
                    /*include 'dbConnection.php';  // Using database connection file here
                    //$sql = "SELECT * FROM `events` WHERE `admin_ID` = 1";
                    //$result = $conn->query($sql);
                    if ($result->num_rows > 0) {*/
                        ?>
                        <div class="row">
                            <?php  
                                // output data of each row
                                for ($i=0; $i < 4; $i++) { 
                                    ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="card hovercard shadow-sm">
                                            <div class="card-header" style="background: url('img/assets/modern_01.jpg')">
                                                <!-- This part would be background image. -->
                                            </div>
                                            <div class="card-body info bg-light">
                                                <div class="card-title text-left font-weight-bolder">
                                                    <h4><a href="#" data-toggle="modal" data-target="#eventModal<?php echo $i; ?>">LOREM IPSUM</a></h4>
                                                </div>
                                                <div class="card-title text-left">
                                                    <h6><i class="fa fa-calendar"></i> Date: MMM-DD-YYYY</h6>
                                                </div>
                                                <div class="card-title text-left">
                                                    <h6><i class="fa fa-clock-o"></i> Time: hh:mma - hh:mma</h6>
                                                </div>
                                                <div class="card-title text-justify">
                                                    <h6><i class="fa fa-map-marker"></i> Venue: Lorem ipsum dolor sit amet, consectetur adipiscing elit</h6>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-success h5"><i class="fa fa-barcode"></i> Scan</button>
                                                <button type="button" class="btn btn-warning h5"><i class="fa fa-pencil"></i> Edit</button>
                                                <button type="button" class="btn btn-danger h5"><i class="fa fa-trash"></i> Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Event Modal -->
                                    <div class="modal" id="eventModal<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle" aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h4 class="modal-title text-light" id="exampleModalLongTitle">Modal title No. <?php echo $i+1; ?></h5>
                                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="card bg-info-event-modal h-100">
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-calendar"></i> Date:</h5>
                                                                        <p>MMM-DD-YYYY</p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-clock-o"></i> Time:</h5>
                                                                        <p>hh:mma - hh:mma</p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-map-marker"></i> Venue:</h5>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="card bg-info-event-modal h-100">
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-info-circle"></i> Description:</h5>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-tasks"></i> Agenda:</h5>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <h5><i class="fa fa-quote-left"></i> Theme:</h5>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">View Details</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                        <!-- Pagination -->
                        <div class="container-fluid">
                            <nav aria-label="Event Pagination">
                                <ul class="pagination justify-content-center flex-wrap">
                                    <!-- Previous Page Button -->
                                    <li class="page-item m-2 <?php if($page <= 1){ echo 'disabled'; } ?>">
                                        <a class="page-link rounded-left-event font-weight-bold" href="<?php if($page <= 1){ echo '#'; } else { echo "?page=" . $prev; } ?>">Previous</a>
                                    </li>
                                    <!-- N Page Button -->
                                    <?php for($i = 1; $i <= $totalPages; $i++ ): ?>
                                        <li class="page-item m-2 <?php if($page == $i) {echo 'active'; } ?>">
                                            <a class="page-link rounded-circle font-weight-bold" href="events.php?page=<?= $i; ?>"> <?= $i; ?> </a>
                                        </li>
                                    <?php endfor; ?>
                                    <!-- Next Page Button -->
                                    <li class="page-item m-2 <?php if($page >= $totalPages) { echo 'disabled'; } ?>">
                                        <a class="page-link rounded-right-event font-weight-bold" href="<?php if($page >= $totalPages){ echo '#'; } else {echo "?page=". $next; } ?>">Next</a>
                                    </li>
                                  </ul>
                            </nav>
                        </div>
                        <?php
                    /*} else {
                        ?>
                            <div class="text-center">
                                <br><h4>No event, please add one.</h4>
                            </div>
                        <?php
                    }

                     $conn->close();*/
                ?>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="bg-primary text-white">
            <!-- Copyright -->
            <div class="container footer-copyright text-center py-3">
                &copy; 2021. All Rights Reserved. This system created by PALADO Group.
            </div>
        </footer>

        <!-- Records Limit Script -->
        <script>
            $(document).ready(function () {
                $('#records-limit').change(function () {
                    console.log("Test Record Limit")
                })
            });
        </script>
	</body>
</html>