<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Set Default Time Zone
	date_default_timezone_set("Asia/Manila");

    // Current Time
    $currentHour = date("H",time());

    // Using database connection file here
    include 'dbConnection.php';

    // Initialize Variables for number of records
    $eventRecords = 0;
    $inviteeRecords = 0;
    $attendanceRecords = 0;
    $generatedCertificateRecords = 0;

    // Initialize Array Data
    $eventTitles = array();
    $eventNumInvitees = array();
    $eventBarColors = array();
    $inviteeNumByType = array(0, 0, 0);
    $attendancePresent = array();
    $attendanceAbsent = array();
    $certificateNumStatus = array(0, 0);

    // Get the number of event records
    $eventStmt = $conn->prepare("SELECT `events`.`ID`, `events`.`event_title`, (SELECT COUNT(*) FROM `invitees` WHERE `invitees`.`event_ID` = `events`.`ID` AND `invitees`.`status` = 1) AS `num_invitees` FROM `events` WHERE `events`.`admin_ID` = ?");
    $eventStmt->bind_param('i', $id);
    $eventStmt->execute();
    $eventResult =  $eventStmt->get_result();
    $eventStmt->close();
    $eventRecords = mysqli_num_rows($eventResult);
    while($row = $eventResult->fetch_assoc()) {
        // Save it into event arrays
        $eventTitles[] = $row['event_title'];
        $eventNumInvitees[] = $row['num_invitees'];
        $randomColor = "#".dechex(rand(0x000000, 0xFFFFFF));
        while (in_array($randomColor, $eventBarColors)) {
            $randomColor = "#".dechex(rand(0x000000, 0xFFFFFF));
        }
        $eventBarColors[] = $randomColor;

        // Iterate Event ID
        $iterateEventID = $row['ID'];

        // Get the number of invitee records
        $inviteeStmt = $conn->prepare("SELECT `invitees`.`type`, IF(`attendance`.`status` IS NULL, 'Absent', 'Present') as `attendance_status` FROM `invitees` LEFT JOIN `attendance` ON `invitees`.`invitee_code` = `attendance`.`invitee_code` WHERE `invitees`.`event_ID` = ? AND `invitees`.`status` = 1");
        $inviteeStmt->bind_param("i", $iterateEventID);
        $inviteeStmt->execute();
        $inviteeResult = $inviteeStmt->get_result();
        $inviteeStmt->close();
        $inviteeRecords += mysqli_num_rows($inviteeResult);
        $tempPresent = 0;
        $tempAbsent = 0;
        while ($ivtRow = $inviteeResult->fetch_assoc()) {
            if ($ivtRow['type'] == "Student") {
                $inviteeNumByType[0]++;
            } else if ($ivtRow['type'] == "Guest") {
                $inviteeNumByType[1]++;
            } else if ($ivtRow['type'] == "Employee") {
                $inviteeNumByType[2]++;
            }
            if ($ivtRow['attendance_status'] == 'Present') {
                $tempPresent++;
                $attendanceRecords++;
            } else {
                $tempAbsent++;
            }
        }

        // Get the number of certificate records
        $certificateStmt = $conn->prepare("SELECT (ROW_NUMBER() OVER(ORDER BY `certificate`.`datetime_generated` ASC)) AS `row_num`, IF(`certificate`.`datetime_generated` IS NULL, 'Not Yet Generated', 'Generated') AS `generation_status` FROM `invitees` LEFT JOIN `certificate` ON `certificate`.`invitee_code` = `invitees`.`invitee_code` WHERE `invitees`.`event_ID` = ? AND (SELECT COUNT(*) FROM `attendance` WHERE `attendance`.`invitee_code` = `invitees`.`invitee_code`) = 1");
        $certificateStmt->bind_param("i", $iterateEventID);
        $certificateStmt->execute();
        $certificateResult = $certificateStmt->get_result();
        $certificateStmt->close();
        while ($certRow = $certificateResult->fetch_assoc()) {
            if ($certRow['generation_status'] == "Generated") {
                $generatedCertificateRecords++;
                $certificateNumStatus[0]++;
            } else {
                $certificateNumStatus[1]++;
            }
        }

        // Save into attendance statuses arrays
        $attendancePresent[] = $tempPresent;
        $attendanceAbsent[] = $tempAbsent;
    }
    
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>

    <script src="./scripts/chart.js"></script>
</head>
<body class="d-flex flex-column">
	<?php 
        // Initialize Active Page for Navbar Highlight
        $activePage = "home";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
    <div class="main-body container-fluid flex-grow-1 mt-5">
    	<div class="container-fluid px-2 pt-2">
    		<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold">Hello and Good <?php if ($currentHour >= 0 && $currentHour < 5) {
                	echo "night";
                } else if ($currentHour >= 5 && $currentHour < 12) {
                	echo "morning";
                } else if ($currentHour >= 12 && $currentHour < 18) {
                	echo "afternoon";
                } else if ($currentHour >= 18 && $currentHour < 21) {
                	echo "evening";
                } else {
                	echo "night";
                } ?> <?php echo $username;?>!</h1>
                <h2 class="pl-3 font-weight-normal">Your home dashboard.</h2>
            </div>
            <!-- Numbers -->
            <div class="container shadow-sm p-3 my-3 border-form-override">
            	<!-- Statistics -->
            	<div class="row mt-0">
            		<!-- Total Events -->
            		<div class="col mb-3">
            			<div class="p-3 bg-info rounded">
            				<div class="row mb-1">
            					<div class="col">
            					  	<div class="float-left">
	                					<h1 class="display-5 text-light font-weight-bold"><?php echo $eventRecords;?></h1>
	                					<h6 class="text-light">Events Created</h6>
	            					</div>
            					</div>
            					<div class="col">
            					  	<div class="float-right">
		            					<img alt="Events" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDEyNC44IDEyNC44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMjQuOCAxMjQuODsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCgkuc3Qwe2ZpbGw6I0ZGRkZGRjt9DQo8L3N0eWxlPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00OC4xLDgwLjRsLTEuOSwxMS40Yy0wLjMsMS42LDAuNCwzLjEsMS43LDQuMWMxLjMsMC45LDMsMS4xLDQuNCwwLjNsMTAuMi01LjNsMTAuMiw1LjMNCgkJCWMwLjYsMC4zLDEuMywwLjUsMS45LDAuNWMwLjksMCwxLjctMC4zLDIuNC0wLjhjMS4zLTAuOSwxLjktMi41LDEuNy00LjFsLTEuOS0xMS40bDguMi04LjFjMS4xLTEuMSwxLjUtMi43LDEtNC4yDQoJCQlzLTEuOC0yLjYtMy4zLTIuOGwtMTEuNC0xLjdsLTUuMS0xMC4zYy0wLjctMS40LTIuMS0yLjMtMy43LTIuM2MtMS42LDAtMywwLjktMy43LDIuM2wtNS4xLDEwLjNsLTExLjQsMS43DQoJCQljLTEuNiwwLjItMi45LDEuMy0zLjMsMi44Yy0wLjUsMS41LTAuMSwzLjEsMSw0LjJMNDguMSw4MC40eiIvPg0KCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTExLjQsMTMuM2gtMTNWNmMwLTMuMy0yLjctNi02LTZoLTFjLTMuMywwLTYsMi43LTYsNnY3LjJIMzkuM1Y2YzAtMy4zLTIuNy02LTYtNmgtMWMtMy4zLDAtNiwyLjctNiw2djcuMg0KCQkJSDEzLjRDNi41LDEzLjMsMSwxOC44LDEsMjUuN3Y4Ni44YzAsNi44LDUuNiwxMi40LDEyLjQsMTIuNGg5OC4xYzYuOCwwLDEyLjQtNS42LDEyLjQtMTIuNFYyNS43DQoJCQlDMTIzLjgsMTguOCwxMTguMywxMy4zLDExMS40LDEzLjN6IE0xMDkuOCwxMTAuOEgxNVY0My4zaDk0LjhWMTEwLjh6Ii8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+DQo=" width="100" height="100"/>
		            				</div>
            					</div>	
            				</div>
            				<a href="javascript:void(0)" class="text-light" id="eventChart">More info <i class="fas fa-arrow-circle-right"></i></a>
			            </div>
            		</div>
            		<!-- Total Invitees Served -->
            		<div class="col mb-3">
            			<div class="p-3 bg-success rounded">
            				<div class="row mb-1">
            					<div class="col">
            					  	<div class="float-left">
	                					<h1 class="display-5 text-light font-weight-bold"><?php echo $inviteeRecords;?></h1>
	                					<h6 class="text-light">Invitees Served</h6>
	            					</div>
            					</div>
            					<div class="col">
            					  	<div class="float-right">
		            					<img alt="Invitees" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDgwLjEgODAuMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgODAuMSA4MC4xOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojRkZGRkZGO30NCjwvc3R5bGU+DQo8Zz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNDguNCwxNy45YzMuNywyLjMsNi4zLDYuMyw2LjgsMTAuOGMxLjUsMC43LDMuMiwxLjEsNSwxLjFjNi41LDAsMTEuOC01LjMsMTEuOC0xMS44DQoJCWMwLTYuNS01LjMtMTEuOC0xMS44LTExLjhDNTMuNyw2LjMsNDguNSwxMS41LDQ4LjQsMTcuOXogTTQwLjcsNDJjNi41LDAsMTEuOC01LjMsMTEuOC0xMS44cy01LjMtMTEuOC0xMS44LTExLjgNCgkJcy0xMS44LDUuMy0xMS44LDExLjhTMzQuMiw0Miw0MC43LDQyeiBNNDUuNiw0Mi44aC0xMGMtOC4zLDAtMTUsNi44LTE1LDE1VjcwbDAsMC4ybDAuOCwwLjNjNy45LDIuNSwxNC44LDMuMywyMC41LDMuMw0KCQljMTEuMSwwLDE3LjUtMy4yLDE3LjktMy40bDAuOC0wLjRoMC4xVjU3LjhDNjAuNyw0OS41LDUzLjksNDIuOCw0NS42LDQyLjh6IE02NS4xLDMwLjdoLTkuOWMtMC4xLDQtMS44LDcuNS00LjUsMTAuMQ0KCQljNy40LDIuMiwxMi44LDksMTIuOCwxNy4xdjMuOGM5LjgtMC40LDE1LjQtMy4xLDE1LjgtMy4zbDAuOC0wLjRoMC4xVjQ1LjdDODAuMSwzNy40LDczLjQsMzAuNyw2NS4xLDMwLjd6IE0yMCwyOS45DQoJCWMyLjMsMCw0LjQtMC43LDYuMi0xLjhjMC42LTMuOCwyLjYtNyw1LjUtOS4zYzAtMC4yLDAtMC40LDAtMC43YzAtNi41LTUuMy0xMS44LTExLjgtMTEuOGMtNi41LDAtMTEuOCw1LjMtMTEuOCwxMS44DQoJCUM4LjMsMjQuNiwxMy41LDI5LjksMjAsMjkuOXogTTMwLjYsNDAuN2MtMi43LTIuNi00LjMtNi4xLTQuNS0xMGMtMC40LDAtMC43LTAuMS0xLjEtMC4xSDE1Yy04LjMsMC0xNSw2LjctMTUsMTV2MTIuMmwwLDAuMg0KCQlsMC44LDAuM2M2LjQsMiwxMiwyLjksMTYuOSwzLjJ2LTMuN0MxNy44LDQ5LjgsMjMuMiw0Mi45LDMwLjYsNDAuN3oiLz4NCjwvZz4NCjwvc3ZnPg0K" width="100" height="100"/>
		            				</div>
            					</div>	
            				</div>
            				<a href="javascript:void(0)" class="text-light" id="inviteesChart">More info <i class="fas fa-arrow-circle-right"></i></a>
			            </div>
            		</div>
            		<!-- Total Attendances Checked -->
            		<div class="col mb-3">
            			<div class="p-3 bg-warning rounded">
            				<div class="row mb-1">
            					<div class="col">
            					  	<div class="float-left">
	                					<h1 class="display-5 text-light font-weight-bold"><?php echo $attendanceRecords;?></h1>
	                					<h6 class="text-light">Attendance Checked</h6>
	            					</div>
            					</div>
            					<div class="col">
            					  	<div class="float-right">
		            					<img alt="Attendance" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yNTUsMzQ4Yy0xMSwwLTIwLDktMjAsMjB2ODVjMCwxMSw5LDIwLDIwLDIwYzExLDAsMjAtOSwyMC0yMHYtODVDMjc1LDM1NywyNjYsMzQ4LDI1NSwzNDh6Ii8+DQoJCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjAsMTIwYzExLDAsMjAtOSwyMC0yMFY0MGg2MGMxMSwwLDIwLTksMjAtMjBzLTktMjAtMjAtMjBIMjBDOSwwLDAsOSwwLDIwdjgwQzAsMTExLDksMTIwLDIwLDEyMHoiLz4NCgkJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xMDEsMzQ4Yy0xMSwwLTIwLDktMjAsMjB2MzVjMCwxMSw5LDIwLDIwLDIwczIwLTksMjAtMjB2LTM1QzEyMSwzNTcsMTEyLDM0OCwxMDEsMzQ4eiIvPg0KCQkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTEwMCw0NzJINDB2LTYwYzAtMTEtOS0yMC0yMC0yMHMtMjAsOS0yMCwyMHY4MGMwLDExLDksMjAsMjAsMjBoODBjMTEsMCwyMC05LDIwLTIwUzExMSw0NzIsMTAwLDQ3MnoiLz4NCgkJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNzgsMzQ4Yy0xMSwwLTIwLDktMjAsMjB2MzVjMCwxMSw5LDIwLDIwLDIwczIwLTksMjAtMjB2LTM1QzE5OCwzNTcsMTg5LDM0OCwxNzgsMzQ4eiIvPg0KCQkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTQ5MiwwaC04MGMtMTEsMC0yMCw5LTIwLDIwczksMjAsMjAsMjBoNjB2NjBjMCwxMSw5LDIwLDIwLDIwczIwLTksMjAtMjBWMjBDNTEyLDksNTAzLDAsNDkyLDB6Ii8+DQoJCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzM1LDM0OGMtMTEsMC0yMCw5LTIwLDIwdjM1YzAsMTEsOSwyMCwyMCwyMHMyMC05LDIwLTIwdi0zNUMzNTUsMzU3LDM0NiwzNDgsMzM1LDM0OHoiLz4NCgkJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00OTIsMjc4aC01OFYxMDBjMC0xMS05LTIwLTIwLTIwcy0yMCw5LTIwLDIwdjE3OGgtMzlWMTAwYzAtMTEtOS0yMC0yMC0yMHMtMjAsOS0yMCwyMHYxNzhoLTQwVjEwMA0KCQkJCWMwLTExLTktMjAtMjAtMjBzLTIwLDktMjAsMjB2MTc4aC0zN1YxMDBjMC0xMS05LTIwLTIwLTIwcy0yMCw5LTIwLDIwdjE3OGgtMzdWMTAwYzAtMTEtOS0yMC0yMC0yMHMtMjAsOS0yMCwyMHYxNzhIMjANCgkJCQljLTExLDAtMjAsOS0yMCwyMHM5LDIwLDIwLDIwaDQ3MmMxMSwwLDIwLTksMjAtMjBTNTAzLDI3OCw0OTIsMjc4eiIvPg0KCQkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTQxNCw0MjNjMTEsMCwyMC05LDIwLTIwdi0zNWMwLTExLTktMjAtMjAtMjBzLTIwLDktMjAsMjB2MzVDMzk0LDQxNCw0MDMsNDIzLDQxNCw0MjN6Ii8+DQoJCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNDkyLDM5MmMtMTEsMC0yMCw5LTIwLDIwdjYwaC02MGMtMTEsMC0yMCw5LTIwLDIwczksMjAsMjAsMjBoODBjMTEsMCwyMC05LDIwLTIwdi04MA0KCQkJCUM1MTIsNDAxLDUwMywzOTIsNDkyLDM5MnoiLz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K" width="100" height="100"/>
		            				</div>
            					</div>	
            				</div>
            				<a href="javascript:void(0)" class="text-light" id="attendanceChart">More info <i class="fas fa-arrow-circle-right"></i></a>
			            </div>
            		</div>
            		<!-- Total Certificates Generated -->
            		<div class="col">
            			<div class="p-3 bg-primary rounded">
            				<div class="row mb-1">
            					<div class="col">
            					  	<div class="float-left">
	                					<h1 class="display-5 text-light font-weight-bold"><?php echo $generatedCertificateRecords;?></h1>
	                					<h6 class="text-light">Certificates Generated</h6>
	            					</div>
            					</div>
            					<div class="col">
            					  	<div class="float-right">
		            					<img alt="" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxnPg0KCTxlbGxpcHNlIGNsYXNzPSJzdDAiIGN4PSIyNTYuNSIgY3k9IjM0Ni41IiByeD0iNTAiIHJ5PSI1MCIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00OTcsMEgxNUM2LjcsMCwwLDYuNywwLDE1djM2MGMwLDguMyw2LjcsMTUsMTUsMTVoMTc0LjRjLTM0LjQtNTIuOSwzLjgtMTIzLjUsNjcuMS0xMjMuNQ0KCQlTMzU4LDMzNy4xLDMyMy42LDM5MEg0OTdjOC4zLDAsMTUtNi43LDE1LTE1VjE1QzUxMiw2LjcsNTA1LjMsMCw0OTcsMHogTTE5Niw2MWgxMjBjOC4zLDAsMTUsNi43LDE1LDE1cy02LjcsMTUtMTUsMTVIMTk2DQoJCWMtOC4zLDAtMTUtNi43LTE1LTE1UzE4Ny43LDYxLDE5Niw2MXogTTQwNiwyNDFIMTA2Yy04LjMsMC0xNS02LjctMTUtMTVzNi43LTE1LDE1LTE1aDMwMGM4LjMsMCwxNSw2LjcsMTUsMTVTNDE0LjMsMjQxLDQwNiwyNDF6DQoJCSBNNDA2LDE4MUgxMDZjLTguMywwLTE1LTYuNy0xNS0xNXM2LjctMTUsMTUtMTVoMzAwYzguMywwLDE1LDYuNywxNSwxNVM0MTQuMywxODEsNDA2LDE4MXoiLz4NCgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTk2LjcsMzk5LjZWNDk3YzAsMTEuOSwxMy4zLDE5LjEsMjMuMywxMi41bDM3LTI0LjVsMzcsMjQuNWM5LjksNi41LDIzLjMtMC41LDIzLjMtMTIuNXYtOTguNQ0KCQlDMjg1LjYsNDM1LjQsMjI5LjEsNDM2LDE5Ni43LDM5OS42TDE5Ni43LDM5OS42eiIvPg0KPC9nPg0KPC9zdmc+DQo=" width="100" height="100"/>
		            				</div>
            					</div>	
            				</div>
            				<a href="javascript:void(0)" class="text-light" id="certificateChart">More info <i class="fas fa-arrow-circle-right"></i></a>
			            </div>
            		</div>
            	</div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6" id="eventParentContainer" style="display: none;">
                    <!-- Event Chart -->
                    <div class="container shadow-sm p-3 my-4 border-form-override" style="height: 95%">
                        <div class="chart-container mx-auto" style="position: relative; max-width: 500px; min-height: 450px;">
                            <canvas id="eventChartCanvas"></canvas>
                        </div>
                    </div>  
                </div>
                <div class="col-md-6" id="inviteeParentContainer" style="display: none;">
                    <!-- Invitee Chart -->
                    <div class="container shadow-sm p-3 my-4 border-form-override" style="height: 95%">
                        <div class="chart-container mx-auto" style="position: relative; max-width: 500px;">
                            <canvas id="inviteeChartCanvas"></canvas>
                        </div>                
                    </div>  
                </div>
                <div class="col-md-6" id="attendanceParentContainer" style="display: none;">
                    <!-- Attendance Chart -->
                    <div class="container shadow-sm p-3 my-4 border-form-override" style="height: 95%">
                        <div class="chart-container mx-auto" style="position: relative; max-width: 500px; min-height: 450px;">
                            <canvas id="attendanceChartCanvas"></canvas>
                        </div>                
                    </div>  
                </div>
                <div class="col-md-6" id="certificateParentContainer" style="display: none;">
                    <!-- Certificate Chart -->
                    <div class="container shadow-sm p-3 my-4 border-form-override" style="height: 95%">
                        <div class="chart-container mx-auto" style="position: relative; max-width: 500px;">
                            <canvas id="certificateChartCanvas"></canvas>
                        </div>                
                    </div>  
                </div>
            </div>
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>

    <!-- Chart Scripts -->
    <script>
        // Initialize Charts
        var eventChart;
        var inviteeChart;
        var attendanceChart;
        var certificateChart;

        // Default Chart Js Global Config
        Chart.defaults.color = "#000";

        // Initialize Chart Flags
        var eventFlag = true;
        var inviteeFlag = true;
        var attendanceFlag = true;
        var certificateFlag = true;

        // Global Font Styles
        const titleFontStyle = {family:'sans-serif', size: 18};
        const labelFontStyle = {family:'sans-serif', size: 16};
        const eventTitleFontStyle = {family:'sans-serif', size: 14};

        // Event Chart Click
        $("#eventChart").click(function() {
            if (eventFlag == true) {
                var totalEvent = <?php echo $eventRecords;?>;
                var xValues = <?php echo json_encode($eventTitles);?>;
                var yValues = <?php echo json_encode($eventNumInvitees);?>;
                var barColors = <?php echo json_encode($eventBarColors);?>;
                var total = yValues.reduce((accumulator, currentValue) => accumulator + currentValue);
                var percentageLabels = yValues.map(function(currentValue, index) {
                    return xValues[index] + " ("+ ((currentValue / total) * 100).toFixed(2) + "%)";
                });

                eventChart = new Chart("eventChartCanvas", {
                    type: "bar",
                    data: {
                        labels: percentageLabels,
                        datasets: [{
                            backgroundColor: barColors,
                            data: yValues
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'No. of invitees',
                                    font: labelFontStyle
                                },
                                ticks: {
                                    stepSize: 1,
                                    font: labelFontStyle
                                },
                            },
                            x: {
                                display: true,
                                ticks: {
                                    maxRotation: 90,
                                    minRotation: 90,
                                    font: eventTitleFontStyle,
                                    callback: function(value, index, values) {
                                        return xValues[index];
                                    }
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Events Summary',
                                font: titleFontStyle
                            },
                            subtitle: {
                                display: true,
                                text: 'Total Events: ' + totalEvent,
                                font: labelFontStyle
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
                eventFlag = false;
            } else {
                eventChart.destroy();
                eventFlag = true;
            }
            $("#eventParentContainer").fadeToggle();
        });

        // Invitee Chart Click
        $("#inviteesChart").click(function() {
            if (inviteeFlag == true) {
                var xValues = ["Student", "Guest", "Employee"];
                var yValues = <?php echo json_encode($inviteeNumByType);?>;
                var barColors = ["#ff0000", "#00ff00", "#0000ff"];
                var total = yValues.reduce((accumulator, currentValue) => accumulator + currentValue);
                var percentageLabels = yValues.map(function(currentValue, index) {
                    return xValues[index] + " ("+ ((currentValue / total) * 100).toFixed(2) + "%)";
                });

                inviteeChart = new Chart("inviteeChartCanvas", {
                    type: "doughnut",
                    data: {
                        labels: percentageLabels,
                        datasets: [{
                            backgroundColor: barColors,
                            data: yValues
                        }]
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Invitees Summary',
                                font: titleFontStyle
                            },
                            legend: {
                                labels: {
                                    font: labelFontStyle
                                }
                            }
                        }
                    }
                });
                inviteeFlag = false;
            } else {
                inviteeChart.destroy();
                inviteeFlag = true;
            }
            $("#inviteeParentContainer").fadeToggle();
        });

        // Attendance Chart Click
        $("#attendanceChart").click(function() {
            if (attendanceFlag == true) {
                var totalAttendance = <?php echo $attendanceRecords;?>;
                var xValues = <?php echo json_encode($eventTitles);?>;
                var yValuesPresent = <?php echo json_encode($attendancePresent);?>;
                var yValuesAbsent = <?php echo json_encode($attendanceAbsent);?>;
                // var yValuesPresentPercentage = new Array();
                // for (var i = 0; i < yValuesPresent.length; i++) {
                //     var tempArray = {percentage:i, present:yValuesPresent[i]};
                //     yValuesPresentPercentage.push(tempArray);
                // }
                // console.log(yValuesPresentPercentage);

                attendanceChart = new Chart("attendanceChartCanvas", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [
                            {
                                label: 'Present',
                                backgroundColor: "#0000ff",
                                data: yValuesPresent
                            },
                            {
                                label: 'Absent',
                                backgroundColor: "#ff0000",
                                data: yValuesAbsent
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Attendance Status No.',
                                    font: labelFontStyle
                                },
                                ticks: {
                                    stepSize: 1,
                                    font: labelFontStyle
                                }
                            },
                            x: {
                                display: true,
                                ticks: {
                                    maxRotation: 90,
                                    minRotation: 90,
                                    font: eventTitleFontStyle
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Attendance Summary',
                                font: titleFontStyle
                            },
                            subtitle: {
                                display: true,
                                text: 'Total Attendance: ' + totalAttendance,
                                font: labelFontStyle
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var numInvitees = <?php echo json_encode($eventNumInvitees);?>;
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        var datasetIndex = tooltipItems[0].datasetIndex;
                                        var percentageTemp = ((tooltipItems[0].raw / numInvitees[dataIndex]) * 100).toFixed(2);
                                        return 'Percentage: ' + percentageTemp + "%";
                                    }
                                }
                            }
                        }
                    }
                });
                attendanceFlag = false;
            } else {
                attendanceChart.destroy();
                attendanceFlag = true;
            }
            $("#attendanceParentContainer").fadeToggle(); 
        });

        // Certificate Chart Click
        $("#certificateChart").click(function() {
            if (certificateFlag == true) {
                var xValues = ["Generated", "Not Yet Generated"];
                var yValues = <?php echo json_encode($certificateNumStatus);?>;
                var barColors = ["#0000ff", "#ff0000"];
                var total = yValues.reduce((accumulator, currentValue) => accumulator + currentValue);
                var percentageLabels = yValues.map(function(currentValue, index) {
                    return xValues[index] + " ("+ ((currentValue / total) * 100).toFixed(2) + "%)";
                });

                certificateChart = new Chart("certificateChartCanvas", {
                    type: "pie",
                    data: {
                        labels: percentageLabels,
                        datasets: [{
                            backgroundColor: barColors,
                            data: yValues
                        }]
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Certificates Summary',
                                font: titleFontStyle
                            },
                            legend: {
                                labels: {
                                    font: labelFontStyle
                                }
                            }
                        }
                    }
                });
                certificateFlag = false;
            } else {
                certificateChart.destroy();
                certificateFlag = true;
            }
            $("#certificateParentContainer").fadeToggle(); 
        });
    </script>
</body>
</html>