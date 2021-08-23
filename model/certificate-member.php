<?php
	namespace CertificatePot;

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
	class CertificateMember
	{
		/**
		 * To fetch up the certificate list
		*/
		public function listCertificate($selectedEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (ROW_NUMBER() OVER(ORDER BY `certificate`.`datetime_generated` ASC)) AS `row_num`, CONCAT(`invitees`.lastname, ', ', `invitees`.firstname, ' ', `invitees`.`middlename`) AS `invitee_name`, `certificate`.`invitee_code`, `certificate`.`certificate_code`, `certificate`.`datetime_generated` FROM `invitees`, `certificate` WHERE `certificate`.`invitee_code` = `invitees`.`invitee_code` AND `certificate`.`event_ID` = $selectedEventID ";

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
			$certificateList = $conn->query($sqlQuery);

			// Fetch up number of rows
			$sqlNumQuery = "SELECT * FROM `certificate` WHERE `event_ID` = $selectedEventID";
			$numAttendance = $conn->query($sqlNumQuery);
			$numRows = mysqli_num_rows($numAttendance);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$certificateData = array();
	        while ($row = $certificateList->fetch_assoc()) {
	        	$ivtRows = array();
	        	$ivtRows[] = '<button type="button" name="viewCertificate" id="'.$row["invitee_code"].'" class="btn btn-primary btn-xs viewCertificate w-100"><i class="fas fa-eye"></i> View</button>';
	        	$ivtRows[] = $row["row_num"];
	        	$ivtRows[] = $row["invitee_name"];
	        	$ivtRows[] = $row["invitee_code"];
	        	$ivtRows[] = $row["certificate_code"];
	        	$ivtRows[] = $row["datetime_generated"];
	        	$ivtRows[] = '<button type="button" name="sendEmailCertificate" id="'.$row["invitee_code"].'" class="btn btn-success btn-xs sendCertificate w-100"><i class="fas fa-envelope"></i> Send</button>';
	        	$certificateData[] = $ivtRows;
	        	$numFiltered++;
	        }
	        $output = array(
				"draw"				=>	intval($_POST["draw"]),
				"recordsTotal"  	=>  $numRows,
				"recordsFiltered" 	=> 	$numFiltered,
				"data"    			=> 	$certificateData
			);
			echo json_encode($output);
		}

		/**
		 * To validate up the certificate
		*/
		public function validateCertificate($selectedEventID, $scannedCertificateCode)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query to scan if the certificate code is valid
    		$sqlScanQuery = "SELECT * FROM `certificate` WHERE `event_ID` = $selectedEventID AND `certificate_code` = '$scannedCertificateCode'";

    		// Fetch up the scan result
    		$scanResult = $conn->query($sqlScanQuery);

    		// Checl if the query is successful
    		if ($scanResult->num_rows > 0) { // If the invitee code registered
    			// It will out success message
    			$output = array("scanStatus" => "success");
    		} else { // If the invitee code not registered
				// It will output invalid code message
				$output = array("scanStatus" => "invalid");
			}

			echo json_encode($output);
		}

		/**
		 * To get certificate template file
		*/
		public function getCertificateTemplateFile($currentEventID)
		{
			// Database Connection
			include 'dbConnection.php';

			// Fetch up an certificate information
			$certificateFileInfo = $conn->query("SELECT * FROM `events` WHERE `ID` = $currentEventID");

			while ($row = $certificateFileInfo->fetch_assoc()){
				$certificateFile = $row["certificate_template"];
			}

			return $certificateFile;
		}

		/**
		 * To get the selected certificate
		*/
		public function getCertificate($selectedInviteeCode, $currentEventID)
		{
			// Get Certificate File
			$certificateFile =  $this->getCertificateTemplateFile($currentEventID);

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an certificate information
			$certificateInfo = $conn->query("SELECT `certificate`.*, CONCAT(`invitees`.firstname, ' ', `invitees`.`middlename`, ' ', `invitees`.lastname) AS `invitee_name` FROM `certificate`, `invitees` WHERE `certificate`.`invitee_code` = '$selectedInviteeCode' AND `invitees`.`invitee_code` = '$selectedInviteeCode';");

			while ($row = $certificateInfo->fetch_assoc()){
				// Get Certificate Code
				$text = $row["certificate_code"];

				// Get Name
				$name = $row["invitee_name"];

				include "model/certificate-generator.php";

				echo json_encode(array_merge($row, array("base64CERT" => $strBase64)));
			}
		}

		/**
		 * To send a generated certificate
		*/
		public function sendCertificate($inviteeCode, $currentEventID)
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