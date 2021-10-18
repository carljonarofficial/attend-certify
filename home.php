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

    // Get the number of event records
    $eventSQL = $conn->query("SELECT COUNT(*) as 'num_row' FROM `events` WHERE `admin_ID` = $id AND `status` = 1");
    while($row = $eventSQL->fetch_assoc())
    {
        $eventRecords = $row['num_row'];
    }

    // Get the number of each records
    $eventSQLRow = $conn->query("SELECT * FROM `events` WHERE `admin_ID` = $id");
    while($row = $eventSQLRow->fetch_assoc())
    {
    	$eventIDS = $row['ID'];
    	// Get the number of invitee records
    	$inviteSQL = $conn->query("SELECT COUNT(*) as 'num_row' FROM `invitees` WHERE `event_ID` = $eventIDS AND `status` = 1");
        while($row2 = $inviteSQL->fetch_assoc()){
        	$inviteeRecords += $row2['num_row'];
        }
        // Get the nummber of attendance records
        $attendanceSQL = $conn->query("SELECT COUNT(*) as 'num_row' FROM `attendance` WHERE `event_ID` = $eventIDS");
        while($row3 = $attendanceSQL->fetch_assoc()){
        	$attendanceRecords += $row3['num_row'];
        }
        // Get the number of generated certificate records
        $certificateSQL = $conn->query("SELECT COUNT(*) as 'num_row' FROM `certificate` WHERE `event_ID` = $eventIDS");
        while($row4 = $certificateSQL->fetch_assoc()){
        	$generatedCertificateRecords += $row4['num_row'];
        }
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
            <div class="container shadow-sm p-3 my-2 mt-4 border-form-override">
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
            				<a href="events.php" class="text-light">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            				<a href="events.php" class="text-light">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            				<a href="events.php" class="text-light">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            				<a href="events.php" class="text-light">More info <i class="fas fa-arrow-circle-right"></i></a>
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
</body>
</html>