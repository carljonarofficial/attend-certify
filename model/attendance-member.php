<?php
	namespace AttendancePot;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	use setasign\Fpdi\Fpdi;

	// FPDI and FPDF
	require_once('model/fpdf/fpdf.php');
	require_once('model/fpdi/autoload.php');

	// PHP Mailer
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
		public function listAttendance($currentEventID, $selectDateStart, $selectDateEnd)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (ROW_NUMBER() OVER(ORDER BY `attendance`.`datetime_attendance` ASC)) AS `row_num`, CONCAT(`invitees`.lastname, ', ', `invitees`.firstname, ' ', `invitees`.`middlename`) AS `invitee_name`, `invitees`.invitee_code, `invitees`.type, `attendance`.datetime_attendance FROM `attendance`, `invitees`, (SELECT @row_number:=0) AS `row_temp` WHERE `attendance`.`event_ID` = ? AND `attendance`.invitee_code = `invitees`.invitee_code AND (`datetime_attendance` BETWEEN ? AND ?) ";

    		// For Search Query
    		if(!empty($_POST["search"]["value"])){
    			$sqlQuery .= "AND (`invitees`.`firstname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`middlename` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`lastname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`invitee_code` LIKE '".$_POST["search"]["value"]."%') ";
    		}

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$columnIndex = $_POST['order'][0]['column']; // Column index
    			$sqlQuery .= 'ORDER BY '.$_POST['columns'][$columnIndex]['data'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY `row_num` ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// echo $sqlQuery;

			// Fetch up a list of attendance
			$attendanceStmt = $conn->prepare($sqlQuery);
		    $attendanceStmt->bind_param('iss', $currentEventID, $selectDateStart, $selectDateEnd);
		    $attendanceStmt->execute();
		    $attendanceList =  $attendanceStmt->get_result();
		    $attendanceStmt->close();

			// Fetch up number of rows
			$attendanceNumStmt = $conn->prepare("SELECT * FROM attendance WHERE event_ID = ? AND (`datetime_attendance` BETWEEN ? AND ?)");
		    $attendanceNumStmt->bind_param('iss', $currentEventID, $selectDateStart, $selectDateEnd);
		    $attendanceNumStmt->execute();
		    $attendanceNumList =  $attendanceNumStmt->get_result();
		    $attendanceNumStmt->close();
			$numRows = mysqli_num_rows($attendanceNumList);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$attendanceData = array();
	        while ($row = $attendanceList->fetch_assoc()) {
	        	$ivtRows = array(
	        		"row_num" => $row["row_num"],
	        		"invitee_name" => $row["invitee_name"],
	        		"invitee_code" => $row["invitee_code"],
	        		"type" => $row["type"],
	        		"datetime_attendance" => $row["datetime_attendance"],
	        		"send" => '<input type="checkbox" class="selectAttendance" value="'.$row["invitee_code"].'">'
	        	);
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
		public function listInvitee($currentEventID, $selectDateStart, $selectDateEnd)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (@row_number:=@row_number + 1) AS `row_num`, (SELECT COUNT(*) FROM attendance WHERE attendance.invitee_code = invitees.invitee_code AND (`datetime_attendance` BETWEEN ? AND ?)) AS `attendance_status`, CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`, ' (', `type`, ')') AS `invitee_name`, `invitee_code`, `email`, `phonenum` FROM invitees, (SELECT @row_number:=0) AS row_temp WHERE `event_ID` = ? AND `status` = 1 ";

    		// For Search Query
    		if(!empty($_POST["search"]["value"])){
    			$sqlQuery .= "AND (`invitees`.`firstname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`middlename` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`lastname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`invitee_code` LIKE '".$_POST["search"]["value"]."%') ";
    		}

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$columnIndex = $_POST['order'][0]['column']; // Column index
    			$sqlQuery .= 'ORDER BY '.$_POST['columns'][$columnIndex]['data'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY `row_num` ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of invitees
			$inviteeStmt = $conn->prepare($sqlQuery);
		    $inviteeStmt->bind_param('ssi', $selectDateStart, $selectDateEnd, $currentEventID);
		    $inviteeStmt->execute();
		    $inviteeList =  $inviteeStmt->get_result();
		    $inviteeStmt->close();

			// Fetch up number of rows in invitees table
			$inviteeNumStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ?");
		    $inviteeNumStmt->bind_param('i', $currentEventID);
		    $inviteeNumStmt->execute();
		    $inviteeNumList =  $inviteeNumStmt->get_result();
		    $inviteeNumStmt->close();
			$numRows = mysqli_num_rows($inviteeNumList);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$attendanceData = array();
	        while ($row = $inviteeList->fetch_assoc()) {
	        	$attendanceStatus = '<div class="p-2 bg-danger text-white text-center rounded" style="width: 70px;">Absent</div>';
	        	if ($row["attendance_status"] == 1) {
	        		$attendanceStatus = '<div class="p-2 bg-success text-white text-center rounded" style="width: 70px;">Present</div>';
	        	}
	        	$ivtRows = array(
	        		"row_num" => $row["row_num"],
		        	"attendance_status" => $attendanceStatus,
		        	"invitee_name" => $row["invitee_name"],
		        	"invitee_code" => $row["invitee_code"],
		        	"email" => '<a href="mailto:' . $row["email"] . '">' . $row["email"] . '</a>',
		        	"phonenum" => '<a href="tel:' . $row["phonenum"] . '">' . $row["phonenum"] . '</a>',
	        	);
	        	$attendanceData[] = $ivtRows;
	        	$numFiltered++;
	        }

	        // Number of Absent Invitees
	        $absentNumStmt = $conn->prepare("SELECT (@row_number:=@row_number + 1) AS `row_num`, (SELECT COUNT(*) FROM attendance WHERE attendance.invitee_code = invitees.invitee_code) AS `attendance_status` FROM invitees WHERE `event_ID` = ? AND `status` = 1");
		    $absentNumStmt->bind_param('i', $currentEventID);
		    $absentNumStmt->execute();
		    $absentNumList =  $absentNumStmt->get_result();
		    $absentNumStmt->close();
			$numAbsent = 0;
			while ($row = $absentNumList->fetch_assoc()) {
				if ($row["attendance_status"] == 1) {
					// Present
				} else {
					$numAbsent++;
				}
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
			// Set Default Time Zone
			date_default_timezone_set("Asia/Manila");

			// Current Date and Time
			$currentDateTime = date("Y-m-d H:i:s");

			// Select Date Start and End
			$selectDateStart = date("Y-m-d ").((date("a") == "am") ? "00:00:00" : "12:00:00");
			$selectDateEnd = date("Y-m-d ").((date("a") == "am") ? "11:59:59" : "23:59:59");

    		// Fetch Invitee Code
    		$inviteeCode = $_POST["inviteeCode"];

    		// Fetch is Generate Certificate
    		$isGenerateCert = $_POST["isGenerateCert"];

    		// Initial Placeholder Variable for Invitee Name
    		$scannedInviteeName = "";

    		// Check if the Invitee Code Matches Pattern
    		if (preg_match("/^IVT-((\d){14})-(([A-Z0-9]){6})$/", $inviteeCode)) {
    			// Using database connection file here
    			include 'dbConnection.php';

    			// Generate Certificate Code
	    		$certficateCode  = date("YmdHis")."-CERT-".strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));

	    		// SQL Query to scan if the code is registered for the event
	    		$sqlScanQuery = "SELECT CONCAT(`firstname`, ' ', `middlename`, ' ',`lastname`) as `fullname` FROM `invitees` WHERE `event_ID` = ? AND `invitee_code` = ?";

	    		// Fetch up the scan result
	    		$scanStmt = $conn->prepare($sqlScanQuery);
		        $scanStmt->bind_param('is', $currentEventID, $inviteeCode);
		        $scanStmt->execute();
		        $scanResult =  $scanStmt->get_result();
		        $scanStmt->close();

	    		if ($scanResult->num_rows > 0) { // If the invitee code registered
					// SQL Query to check if the code already recorded attendance
					$sqlCheckQuery = "SELECT * FROM `attendance` WHERE `event_ID` = ? AND `invitee_code` = ? AND (`datetime_attendance` BETWEEN ? AND ?)";

					// Fetch Up Invitee Information
					while ($row = $scanResult->fetch_assoc()) {
						$scannedInviteeName = $row['fullname'];
					}

					// Fetch up the check result
	    			$checkStmt = $conn->prepare($sqlCheckQuery);
			        $checkStmt->bind_param('isss', $currentEventID, $inviteeCode, $selectDateStart, $selectDateEnd);
			        $checkStmt->execute();
			        $checkResult =  $checkStmt->get_result();
			        $checkStmt->close();

	    			if ($checkResult->num_rows > 0) {
	    				// It will output already code message
						$output = array("scanStatus" => "already", "scannedInviteeName" => $scannedInviteeName);
	    			} else {
	    				// Insert Attendance Record Query
						$recordStmt = $conn->prepare("INSERT INTO `attendance`(`event_ID`, `invitee_code`, `datetime_attendance`) VALUES (?,?,?)");
						$recordStmt->bind_param("iss", $currentEventID, $inviteeCode, $currentDateTime);

						// Validate if Insert Query is successful or not
						if ($recordStmt->execute()) {
							$recordStmt->close();
							// Check if toggle automatic certificate generation
							if ($isGenerateCert == "auto") {
								// Check if certificate exists
								$certExistsStmt = $conn->prepare("SELECT `invitee_code` FROM `certificate` WHERE `event_ID` = ? AND `invitee_code` = ?");
								$certExistsStmt->bind_param("is", $currentEventID, $inviteeCode);

								// Check if executes successfully
								if ($certExistsStmt->execute()) {
									$checkExistsResult =  $certExistsStmt->get_result();
									$certExistsStmt->close();

									// Check if the certificate has already generated
									if ($checkExistsResult->num_rows > 0) {
										// It will out success message
											$output = array("scanStatus" => "success", "scannedInviteeName" => $scannedInviteeName." (Checked you attendance succesfully, your certificate has already been generated)");
									} else {
										// Insert Certificate Record Query
										$certStmt = $conn->prepare("INSERT INTO `certificate`(`event_ID`, `invitee_code`, `certificate_code`) VALUES (?,?,?)");
										$certStmt->bind_param("iss", $currentEventID, $inviteeCode, $certficateCode);

										// Validate if Insert Query is successful or not
										if ($certStmt->execute()) {
											$certStmt->close();
											// It will out success message
											$output = array("scanStatus" => "success", "scannedInviteeName" => $scannedInviteeName);
										} else {
											// It will out error message
											$output = array("scanStatus" => "error", "scannedInviteeName" => $scannedInviteeName." (Checked but Error Sending Certificate)");
										}	
									}
								} else {
									// It will out error message
		    						$output = array("scanStatus" => "error", "scannedInviteeName" => $scannedInviteeName." (Checked but Error Generatin Certificate)");
								}
				    		} else {
				    			// It will out success message
		    					$output = array("scanStatus" => "success", "scannedInviteeName" => $scannedInviteeName);
				    		}
						} else {
							// It will out error message
	    					$output = array("scanStatus" => "error", "scannedInviteeName" => "Error");
						}

	    			}
	    			
				} else { // If the invitee code not registered
					// It will output invalid code message
					$output = array("scanStatus" => "invalid", "scannedInviteeName" => "You are not invited/registered this event");
				}
    		} else {
    			// It will output invalid code message
				$output = array("scanStatus" => "invalid", "scannedInviteeName" => "Invalid Barcode");
    		}
			echo json_encode($output);
		}

		/**
		 * To send selected certificate/s
		*/
		public function sendSelectedCertificate($currentEventID)
		{
			// Fetch Selected Invitee IDs and Count them
			$selectedInviteeCodes = $_POST["selectedInviteeCodes"];
			$selectedInviteeCodes = json_decode($selectedInviteeCodes);
			$idsCount = count($selectedInviteeCodes);

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// Validate if the admin logged in
    		include 'validateLogin.php';

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an event information
			$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ?");
	        $eventStmt->bind_param('ii', $id, $currentEventID);
	        $eventStmt->execute();
	        $eventInfo =  $eventStmt->get_result();
	        $eventStmt->close();
	        while ($row = $eventInfo->fetch_assoc()) {
	            $eventTitle = $row["event_title"];
	            $eventDate = date_format(date_create($row["date"]),"F d, Y");
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Check query if certificate/s are missing
	        $checkCertStmt = $conn->prepare("SELECT `attendance`.`invitee_code`, IF(`certificate`.`certificate_code` IS NULL, 0, 1) as `cert_status` FROM `attendance` LEFT JOIN `certificate` ON `attendance`.`invitee_code` = `certificate`.`invitee_code` WHERE `attendance`.`event_ID` = ? AND `attendance`.`status` = 1 AND `certificate`.`certificate_code` IS NULL");
	        $checkCertStmt->bind_param('i', $currentEventID);
	        $checkCertStmt->execute();
	        $checkCertInfo =  $checkCertStmt->get_result();
	        $checkCertStmt->close();
	        if ($checkCertInfo->num_rows > 0) {
		        // Prepare and Bind Missing Certificates
				$insertMissingStmt = $conn->prepare("INSERT INTO `certificate`(`event_ID`, `invitee_code`, `certificate_code`) VALUES (?,?,?)");
				$insertMissingStmt->bind_param("iss", $currentEventID, $inviteeCode, $certficateCode);

				// Check if the invitee is missing certificate
				while ($row = $checkCertInfo->fetch_assoc()) {
					if ($row['cert_status'] == 0) {
						// Generate Certificate Code Param
			    		$certficateCode  = date("YmdHis")."-CERT-".strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));

			    		// Set Event ID and Invitee Code Params
						$currentEventID = $currentEventID;
						$inviteeCode = $row['invitee_code'];

						// Execute Missing Certificate
						$insertMissingStmt->execute();
					}
				}
				$insertMissingStmt->close();
	        }

	        // Create Placeholder for Prepared Statements and Bind String
			$idsPlaceholders = implode(',', array_fill(0, $idsCount, '?'));
			$bindStr = str_repeat('s', $idsCount);

	        // Fetch up the selected certificate/s information
	        $certificateStmt = $conn->prepare("SELECT `certificate`.`ID`, CONCAT(`invitees`.`firstname`, ' ', `invitees`.`middlename`, ' ', `invitees`.`lastname`) AS `invitee_name`, `invitees`.`email`, `certificate`.`certificate_code` FROM `certificate` INNER JOIN `invitees` ON `invitees`.`invitee_code` = `certificate`.`invitee_code` WHERE `certificate`.`event_ID` = ? AND `certificate`.`invitee_code` IN ($idsPlaceholders) ORDER BY `certificate`.`ID` ASC");
	        $certificateStmt->bind_param("i".$bindStr, $currentEventID, ...$selectedInviteeCodes);
	        $certificateStmt->execute();
	        $certificateInfo =  $certificateStmt->get_result();
	        $certificateStmt->close();

	        // passing true in constructor enables exceptions in PHPMailer
			$mail = new PHPMailer(true);

			// Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
		    $mail->isSMTP();
		    $mail->Host = 'smtp.hostinger.com';
		    $mail->SMTPAuth = true;
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		    $mail->Port = 587;

		    $mail->Username = 'certificates-noreply@attend-certify.com';
		    $mail->Password = 'VLDHpmnE_c2ZhLA';

		    // Sender settings
		    $mail->setFrom('certificates-noreply@attend-certify.com', 'Attend and Certify Certificates');

		    // Setting the email content
		    $mail->IsHTML(true);
		    $mail->Subject = "Certificate: ". $eventTitle ." | Attend and Certify";

	        // Include Barcode Generator
			include "model/bulk-certificate-generator.php";

			$response = "error";

			// Send Selected Certificate/s
	        while ($row = $certificateInfo->fetch_assoc()){
	        	$certNameInvitee = $row["invitee_name"];
	        	$certEmailInvitee = $row["email"];
	        	$certCode = $row["certificate_code"];

	        	try {
	        		// Encode and Generate Certificate Barcode
		        	$pdf417->encode($certCode);

		        	// Get Base64 Encoded Certificate Barcode
				    $certBarcodeBase64 = "data:image/png;base64,".$pdf417->forWeb("BASE64", $certCode);

				    // initiate FPDI
					$pdf = new Fpdi();
					// add a page
					$pdf->AddPage($certOrientation, $certSize);
					// set the source file
					$pdf->setSourceFile('certificate-templates/'.$certTemplate);
					// import page 1
					$tplIdx = $pdf->importPage(1);
					// use the imported page and place it at position 0, 0
					$pdf->useTemplate($tplIdx, 0, 0);

					// now write some text above the imported page
					$pdf->SetFont($certTextFont, $certTextFontStyle, $certTextFontSize);
					// Set text Color
					$pdf->SetTextColor($certTextFontColorR, $certTextFontColorG, $certTextFontColorB);
					// Set text position
					$pdf->SetXY($certTextPositionX, $certTextPositionY);
					// Centered text in a framed 20*10 mm cell and line break
					$pdf->Cell(20,10,$certNameInvitee,0,0,'C');
					// Insert Certificate Code Barcode
					$pdf->Image($certBarcodeBase64, $certBarcodePositionX, $certBarcodePositionY, 70, 0, 'png'); // X start, Y start, X width, Y width in mm
					// Generate and Get Base64 Data
					$str = $pdf->Output('S', $certCode.'.pdf');
					$strBase64 =  base64_encode($str);

					// Get Certificate Base64
				    $certificateFileData = 'data:application/pdf;base64,'.$strBase64;

					// Recipient Setting
				    $mail->addAddress($certEmailInvitee, $certNameInvitee);

				    // Email Body
				    $mail->Body = include 'model/html-email-template-for-certificate.php';

				    // Add Static Attachment
				    $mail->clearAttachments();
					$mail->AddStringAttachment(base64_decode(substr($certificateFileData, strpos($certificateFileData, ","))), $certNameInvitee.' - '.$eventTitle.' - Certificate.pdf', 'base64', 'application/pdf');

				    $mail->send();
				    
				    $mail->clearAddresses();

				    $response = "success";
	        	} catch (Exception $e) {
	        		$response = "error";
	        	}
	        }
	        echo json_encode(array('Status' => $response));
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
			$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ?");
	        $eventStmt->bind_param('ii', $id, $currentEventID);
	        $eventStmt->execute();
	        $eventInfo =  $eventStmt->get_result();
	        $eventStmt->close();
	        while ($row = $eventInfo->fetch_assoc()) {
	            $eventTitle = $row["event_title"];
	            $eventDate = date_format(date_create($row["date"]),"F d, Y");
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Fetch up a certificate information
	        $certificateStmt = $conn->prepare("SELECT CONCAT(`invitees`.`firstname`, ' ', `invitees`.`middlename`, ' ', `invitees`.`lastname`) AS `invitee_name`, `invitees`.`email`, `certificate`.`certificate_code` FROM `invitees`, `certificate` WHERE `invitees`.`event_ID` = ? AND `certificate`.`event_ID` = ? AND `invitees`.`invitee_code` = ? AND `certificate`.`invitee_code` = ?");
	        $certificateStmt->bind_param('iiss', $currentEventID, $currentEventID, $inviteeCode, $inviteeCode);
	        $certificateStmt->execute();
	        $certificateInfo =  $certificateStmt->get_result();
	        $certificateStmt->close();
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
			    $mail->Host = 'smtp.hostinger.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'certificates-noreply@attend-certify.com';
			    $mail->Password = 'VLDHpmnE_c2ZhLA';

			    // Sender settings
			    $mail->setFrom('certificates-noreply@attend-certify.com', 'Attend and Certify Certificates');
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