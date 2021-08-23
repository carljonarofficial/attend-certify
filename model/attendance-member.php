<?php
	namespace AttendancePot;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require 'model/Exception.php';
	require 'model/PHPMailer.php';
	require 'model/SMTP.php';

	/**
	 *
	*/
	class AttendanceMember
	{
		/**
		 * To fetch up the attendance list
		*/
		public function listAttendance($currentEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (ROW_NUMBER() OVER(ORDER BY `attendance`.`datetime_attendance` ASC)) AS `row_num`, CONCAT(`invitees`.lastname, ', ', `invitees`.firstname, ' ', `invitees`.`middlename`) AS `invitee_name`, `invitees`.invitee_code, `invitees`.type, `attendance`.datetime_attendance FROM `attendance`, `invitees`, (SELECT @row_number:=0) AS `row_temp` WHERE `attendance`.`event_ID` = $currentEventID AND `attendance`.invitee_code = `invitees`.invitee_code ";

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY `row_num` ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of attendance
			$attendanceList = $conn->query($sqlQuery);

			// Fetch up number of rows
			$sqlNumQuery = "SELECT * FROM attendance WHERE event_ID = $currentEventID";
			$numAttendance = $conn->query($sqlNumQuery);
			$numRows = mysqli_num_rows($numAttendance);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$attendanceData = array();
	        while ($row = $attendanceList->fetch_assoc()) {
	        	$ivtRows = array();
	        	$ivtRows[] = $row["row_num"];
	        	$ivtRows[] = $row["invitee_name"];
	        	$ivtRows[] = $row["invitee_code"];
	        	$ivtRows[] = $row["type"];
	        	$ivtRows[] = $row["datetime_attendance"];
	        	$ivtRows[] = '<button type="button" name="sendEmailCertificate" id="'.$row["invitee_code"].'" class="btn btn-success btn-xs sendCertificate w-100"><i class="fas fa-envelope"></i> Send Certificate</button>';
	        	$attendanceData[] = $ivtRows;
	        	$numFiltered++;
	        }
	        $output = array(
				"draw"				=>	intval($_POST["draw"]),
				"recordsTotal"  	=>  $numRows,
				"recordsFiltered" 	=> 	$numFiltered,
				"data"    			=> 	$attendanceData
			);
			echo json_encode($output);
		}

		/**
		 * To fetch up the invitees list
		*/
		public function listInvitee($currentEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (@row_number:=@row_number + 1) AS `row_num`, (SELECT COUNT(*) FROM attendance WHERE attendance.invitee_code = invitees.invitee_code) AS `attendance_status`, CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`, ' (', `type`, ')') AS `invitee_name`, `invitee_code`, `email`, `phonenum` FROM invitees, (SELECT @row_number:=0) AS row_temp WHERE `event_ID` = $currentEventID ";

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY `row_num` ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of attendance
			$attendanceList = $conn->query($sqlQuery);

			// Fetch up number of rows in invitees table
			$sqlNumQuery = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID";
			$numAttendance = $conn->query($sqlNumQuery);
			$numRows = mysqli_num_rows($numAttendance);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$attendanceData = array();
			$numAbsent = 0;
	        while ($row = $attendanceList->fetch_assoc()) {
	        	$ivtRows = array();
	        	$ivtRows[] = $row["row_num"];
	        	if ($row["attendance_status"] == 1) {
	        		$ivtRows[] = '<div class="p-2 bg-success text-white text-center rounded" style="width: 70px;">Present</div>';
	        	}else{
	        		$ivtRows[] = '<div class="p-2 bg-danger text-white text-center rounded" style="width: 70px;">Absent</div>';
	        		$numAbsent++;
	        	}
	        	$ivtRows[] = $row["invitee_name"];
	        	$ivtRows[] = $row["invitee_code"];
	        	$ivtRows[] = '<a href="mailto:' . $row["email"] . '">' . $row["email"] . '</a>';
	        	$ivtRows[] = '<a href="tel:' . $row["phonenum"] . '">' . $row["phonenum"] . '</a>';
	        	$attendanceData[] = $ivtRows;
	        	$numFiltered++;
	        }
	        $output = array(
				"draw"				=>	intval($_POST["draw"]),
				"recordsTotal"  	=>  $numRows,
				"recordsFiltered" 	=> 	$numFiltered,
				"recordsAbsent"		=>	$numAbsent,
				"data"    			=> 	$attendanceData
			);
			echo json_encode($output);
		}

		/**
		 * To scan and record an attendance
		*/
		public function scanAttendance($currentEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// Fetch Invitee Code
    		$inviteeCode = $_POST["inviteeCode"];

    		// Generate Certificate Code
    		$certficateCode  = date("YmdHis")."-CERT-".strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));

    		// SQL Query to scan if the code is registered for the event
    		$sqlScanQuery = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `invitee_code` = '$inviteeCode'";

    		// Fetch up the scan result
    		$scanResult = $conn->query($sqlScanQuery);

    		if ($scanResult->num_rows > 0) { // If the invitee code registered
				// SQL Query to check if the code already recorded attendance
				$sqlCheckQuery = "SELECT * FROM `attendance` WHERE `event_ID` = $currentEventID AND `invitee_code` = '$inviteeCode'";

				// Fetch up the check result
    			$checkResult = $conn->query($sqlCheckQuery);

    			if ($checkResult->num_rows > 0) {
    				// It will output already code message
					$output = array("scanStatus" => "already");
    			} else {
    				// Insert Attendance Record Query
					$recordQuery = "INSERT INTO `attendance`(`event_ID`, `invitee_code`) VALUES ('$currentEventID','$inviteeCode');";

					// Validate if Insert Query is successful or not
					if ($conn->query($recordQuery) === TRUE) {
						// Insert Certificate Record Query
						$certQuery = "INSERT INTO `certificate`(`event_ID`, `invitee_code`, `certificate_code`) VALUES ('$currentEventID','$inviteeCode','$certficateCode');";

						// Validate if Insert Query is successful or not
						if ($conn->query($certQuery) === TRUE) {
							// It will out success message
    						$output = array("scanStatus" => "success");
						} else {
							// It will out error message
    						$output = array("scanStatus" => "error");	
						}
						
					} else {
						// It will out error message
    					$output = array("scanStatus" => "error");
					}

    				// $output = array("scanStatus" => "success");
    			}
    			
			} else { // If the invitee code not registered
				// It will output invalid code message
				$output = array("scanStatus" => "invalid");
			}
			echo json_encode($output);
		}

		/**
		 * To send a generated certificate
		*/
		public function sendCertificate($currentEventID, $inviteeCode)
		{
			// passing true in constructor enables exceptions in PHPMailer
			$mail = new PHPMailer(true);

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// Validate if the admin logged in
    		include 'validateLogin.php';

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an event information
			$eventInfo = $conn->query("SELECT * FROM `events` WHERE `admin_ID` = $id AND `ID` = $currentEventID");
	        while ($row = $eventInfo->fetch_assoc()) {
	            $eventTitle = $row["event_title"];
	            $eventDate = date_format(date_create($row["date"]),"F d, Y");
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Fetch up a certificate information
	        $certificateInfo = $conn->query("SELECT CONCAT(`invitees`.`firstname`, ' ', `invitees`.`middlename`, ' ', `invitees`.`lastname`) AS `invitee_name`, `invitees`.`email`, `certificate`.`certificate_code` FROM `invitees`, `certificate` WHERE `invitees`.`event_ID` = $currentEventID AND `certificate`.`event_ID` = $currentEventID AND `invitees`.`invitee_code` = '$inviteeCode' AND `certificate`.`invitee_code` = '$inviteeCode'");
	        while ($row = $certificateInfo->fetch_assoc()){
	        	$certNameInvitee = $row["invitee_name"];
	        	$certEmailInvitee = $row["email"];
	        	$certCode = $row["certificate_code"];
	        }

	        try {
	        	// Assign Certificate Code for encoding into base64
				$text = $certCode;

				// Assign Invitee Name for embedding to certificate
				$name = $certNameInvitee;

				// Assign Certificate Template File
				$certificateFile = $certTemplate;

				// Get Generated Certificate
			    include "model/certificate-generator.php";
				
				// Get Certificate Base64
			    $certificateFileData = 'data:application/pdf;base64,'.$strBase64;

			    // Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
			    $mail->isSMTP();
			    $mail->Host = 'smtp.gmail.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'attend.certify@gmail.com'; // YOUR gmail email
			    $mail->Password = 'k0rnb33f19'; // YOUR gmail password

			    // Sender and recipient settings
			    $mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
			    $mail->addAddress($certEmailInvitee, $certNameInvitee);

			    // Setting the email content
			    $mail->IsHTML(true);
			    $mail->Subject = "Certificate: ". $eventTitle ." | Attend and Certify";
			    $mail->Body = include 'model/html-email-template-for-certificate.php';

			    // Add Static Attachment
				$mail->AddStringAttachment(base64_decode(substr($certificateFileData, strpos($certificateFileData, ","))), $certNameInvitee.' - '.$eventTitle.' - Certificate.pdf', 'base64', 'application/pdf');

			    $mail->send();
			    // echo "Email message sent.";

			    $response = array('Status' => "success");
				echo json_encode($response);

	        } catch(Exception $e) {
	        	$response = array('Status' => "error");
				echo json_encode($response);
	        }
		}
	}
?>