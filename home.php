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
    $eventSchedules = array();
    $eventNumInvitees = array();
    $eventBarColors = array();
    $inviteeNumByType = array(0, 0, 0);
    $attendancePresent = array();
    $attendanceAbsent = array();
    $certificateNumStatus = array(0, 0);

    // Get the number of event records
    $eventStmt = $conn->prepare("SELECT `ID`, `event_title`, `date`, `date_end`, `time_inclusive`, `time_conclusive`, (SELECT COUNT(*) FROM `invitees` WHERE `invitees`.`event_ID` = `events`.`ID` AND `invitees`.`status` = 1) AS `num_invitees` FROM `events` WHERE `events`.`admin_ID` = ?");
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
        $iStart = new DateTime($row['date']);
        $iEnd = new DateTime($row['date_end']);
        for ($i = $iStart; $i <= $iEnd; $i->modify("+1 day")) {
            $eventSchedules[] = array(
                "title" => $row['event_title'],
                "start" => $i->format("Y-m-d")."T".$row['time_inclusive'],
                "end" => $i->format("Y-m-d")."T".$row['time_conclusive']
            );
        }

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
    <!-- Custom Stat Boxes Styles -->
    <link rel="stylesheet" href="style/custom-stat-box.css">
    <!-- FullCalendar -->
    <link rel="stylesheet" href="style/fullcalendar/main.css">
    <script src="scripts/fullcalendar/main.js"></script>
    <!-- Chart.js -->
    <script src="./scripts/chart.js"></script>
    <style type="text/css">
        .fc-toolbar-chunk {
            white-space: pre;
            margin-left: 3px;
            margin-right: 3px;
        }
    </style>
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
                } ?> <?php echo $adminAccountName;?>!</h1>
                <h2 class="pl-3 font-weight-normal">Your home dashboard.</h2>
            </div>
            <!-- Numbers -->
            <div class="container-fluid shadow-sm p-3 my-3 border-form-override">
            	<!-- Statistics -->
            	<div class="row mt-0">
            		<!-- Total Events -->
                    <div class="col-lg-3 col-sm-6 my-1">
                        <!-- Small Box -->
                        <div class="small-box small-box-events my-auto">
                            <div class="inner-part">
                                <h3><?php echo $eventRecords;?></h3>
                                <p>Events <br>Created</p>
                            </div>
                            <div class="icon">
                                <img src="./img/assets/stat-boxes-icons/events.svg" width="70" onContextMenu="return false;" ondragstart="return false;">
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" id="eventChart">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
            		<!-- Total Invitees Served -->
                    <div class="col-lg-3 col-sm-6 my-1">
                        <!-- Small Box -->
                        <div class="small-box small-box-invitees my-auto">
                            <div class="inner-part">
                                <h3><?php echo $inviteeRecords;?></h3>
                                <p>Invitees <br>Served</p>
                            </div>
                            <div class="icon">
                                <img src="./img/assets/stat-boxes-icons/invitees.svg" width="70" onContextMenu="return false;" ondragstart="return false;">
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" id="inviteesChart">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
            		<!-- Total Attendances Checked -->
                    <div class="col-lg-3 col-sm-6 my-1">
                        <!-- Small Box -->
                        <div class="small-box small-box-attendance my-auto">
                            <div class="inner-part">
                                <h3><?php echo $attendanceRecords;?></h3>
                                <p>Attendance <br>Checked</p>
                            </div>
                            <div class="icon">
                                <img src="./img/assets/stat-boxes-icons/attendance.svg" width="70" onContextMenu="return false;" ondragstart="return false;">
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" id="attendanceChart">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
            		<!-- Total Certificates Generated -->
                    <div class="col-lg-3 col-sm-6 my-1">
                        <!-- Small Box -->
                        <div class="small-box small-box-certificate my-auto">
                            <div class="inner-part">
                                <h3><?php echo $generatedCertificateRecords;?></h3>
                                <p>Certificates <br>Generated</p>
                            </div>
                            <div class="icon">
                                <img src="./img/assets/stat-boxes-icons/certificate.svg" width="70" onContextMenu="return false;" ondragstart="return false;">
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" id="certificateChart">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            <!-- Calendar -->
            <div class="container-fluid shadow-sm p-3 my-3 border-form-override" style="max-width: 600px;">
                <h4><i class="far fa-calendar"></i> EVENTS CALENDAR</h4>
                <div id="eventsCalendar" style="overflow-x: auto;">
                    <!-- Calendar Placeholder -->
                </div>
            </div>
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>

    <!-- Calendar Chart Scripts -->
    <script>
        // Initialize Calendar
        var eventSchedules = <?php echo json_encode($eventSchedules);?>;
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('eventsCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                dayMaxEvents: true, // allow "more" link when too many events
                events: eventSchedules
            });
            calendar.render();
        });

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