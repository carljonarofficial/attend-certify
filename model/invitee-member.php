<?php
	namespace InviteePot;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require 'model/Exception.php';
	require 'model/PHPMailer.php';
	require 'model/SMTP.php';
	require 'model/SMS/autoload.php';

	/**
	 *
	*/
	class InviteeMember
	{
		/**
		 * To check if name already exists
		*/
		public function isNameExists($currentEventID,$currentInviteeID,$firstname,$middlename,$lastname)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the name exists
			$nameStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1 AND `ID` != ? AND `firstname` = ? AND `middlename` = ? AND `lastname` = ?");
		    $nameStmt->bind_param('iisss', $currentEventID, $currentInviteeID, $firstname, $middlename, $lastname);
		    $nameStmt->execute();
		    $result =  $nameStmt->get_result();
		    $nameStmt->close();

			if ($result->num_rows > 0) {
				$result = true;
			}else{
				$result = false;
			}

			// The return result value
			return $result;
		}

		/**
		 * To check if email already exists
		*/
		public function isEmailExists($currentEventID,$currentInviteeID,$email)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the email exists
			$emailStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1 AND `ID` != ? AND `email` = ?");
		    $emailStmt->bind_param('iis', $currentEventID, $currentInviteeID, $email);
		    $emailStmt->execute();
		    $result =  $emailStmt->get_result();
		    $emailStmt->close();

			if ($result->num_rows > 0) {
				$result = true;
			}else{
				$result = false;
			}

			// The return result value
			return $result;
		}

		/**
		 * To check if phone number already exists
		*/
		public function isPhoneNumExists($currentEventID,$currentInviteeID,$phonenum)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the email exists
			$phoneNumStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1 AND `ID` != ? AND `phonenum` = ?");
		    $phoneNumStmt->bind_param('iis', $currentEventID, $currentInviteeID, $phonenum);
		    $phoneNumStmt->execute();
		    $result =  $phoneNumStmt->get_result();
		    $phoneNumStmt->close();

			if ($result->num_rows > 0) {
				$result = true;
			}else{
				$result = false;
			}

			// The return result value
			return $result;
		}

		/**
		 * To fetch up the invitee list
		*/
		public function listInvitee($currentEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// Default Time Zone
    		date_default_timezone_set('Asia/Manila');

    		// Current Date and Time
    		$currentDateTime = date("Y-m-d H:i:s");

    		// End Add Invitee Flag
    		$endAddInviteeFlag = false;

    		// Fetch Up Event Date and End Time Information
    		$eventDateEndTimeInfoStmt = $conn->prepare("SELECT CONCAT(`date_end`, ' ', `time_conclusive`) AS `date_endtime` FROM `events` WHERE `ID` = ?");
    		$eventDateEndTimeInfoStmt->bind_param("i", $currentEventID);
    		$eventDateEndTimeInfoStmt->execute();
    		$eventDateEndTimeInfoResult = $eventDateEndTimeInfoStmt->get_result();
    		$eventDateEndTimeInfoStmt->close();
    		if ($eventDateEndTimeInfoResult->num_rows > 0) {
    			while ($row = $eventDateEndTimeInfoResult->fetch_assoc()) {
    				$eventDateAndEndEventTime = $row["date_endtime"];
    			}
                $diffEndTimes = (strtotime($currentDateTime) - strtotime($eventDateAndEndEventTime)) / 60 / 60 / 24;
                // Check if Intervals are Allowed to Scan Attendance
                if ($diffEndTimes < 2){
                    $endAddInviteeFlag = true;
                }
    		} else {
    			exit("Ann error has occured.");
    		}

    		// SQL Query
    		$sqlQuery = "SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1 ";

    		// For Search Query
    		if(!empty($_POST["search"]["value"])){
    			$sqlQuery .= "AND (`firstname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `middlename` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `lastname` LIKE '".$_POST["search"]["value"]."%') ";
    		}

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$columnIndex = $_POST['order'][0]['column']; // Column index
    			$sqlQuery .= 'ORDER BY '.$_POST['columns'][$columnIndex]['data'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY ID ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of invitees
			$inviteeStmt = $conn->prepare($sqlQuery);
		    $inviteeStmt->bind_param('i', $currentEventID);
		    $inviteeStmt->execute();
		    $inviteeInfo =  $inviteeStmt->get_result();
		    $inviteeStmt->close();

			// Fetch up number of rows
			$inviteeNumsStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1");
		    $inviteeNumsStmt->bind_param('i', $currentEventID);
		    $inviteeNumsStmt->execute();
		    $numInvitees = $inviteeNumsStmt->get_result();
		    $inviteeNumsStmt->close();
			$numRows = mysqli_num_rows($numInvitees);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$inviteeData = array();
	        while ($row = $inviteeInfo->fetch_assoc()) {
	        	if ($endAddInviteeFlag) {
	        		$moreBtnStr = '<div class="dropdown">
	        						<button type="button" class="btn btn-secondary btn-xs w-100" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i> More</button>
	        						<div class="dropdown-menu dropdown-menu-right p-2">
	        							<button type="button" name="editInvitee" id="'.$row["ID"].'" class="btn btn-warning btn-xs editInvitee w-100 my-1"><i class="fas fa-user-edit"></i> Edit</button>
	        							<button type="button" name="deleteInvitee" id="'.$row["ID"].'" class="btn btn-danger btn-xs deleteInvitee w-100 my-1"><i class="fas fa-trash-alt"></i> Delete</button>
	        						</div>
	        					</div>';
	        	} else {
	        		$moreBtnStr = '';
	        	}
	        	$ivtRows = array(
	        		"view"=> '<button type="button" name="viewInvitee" id="'.$row["ID"].'" class="btn btn-primary btn-xs viewInvitee w-100"><i class="fas fa-eye"></i> View</button>',
	        		"ID"=> $row["ID"],
	        		"firstname"=> ucfirst($row["firstname"]),
	        		"middlename"=> ucfirst($row["middlename"]),
	        		"lastname"=> ucfirst($row["lastname"]),
	        		"type"=> $row["type"],
	        		"delete"=> $moreBtnStr,
	        		"checkbox"=> '<input type="checkbox" class="selectInvitee" value="'.$row["ID"].'">'
	        	);
	        	$inviteeData[] = $ivtRows;
	        	$numFiltered++;
	        }
	        $output = array(
				"draw"				=>	intval($_POST["draw"]),
				"recordsTotal"  	=>  $numRows,
				"recordsFiltered" 	=> 	$numFiltered,
				"data"    			=> 	$inviteeData
			);
			echo json_encode($output);
		}
		
		/**
		 * To add up an invitee
		*/
		public function addInvitee($currentEventID){
			// Invitee Details
			$inviteeFirstName = $_POST["inviteeFirstName"];
			$inviteeMiddleName = $_POST["inviteeMiddleName"];
			$inviteeLastName = $_POST["inviteeLastName"];
			$inviteeEmail = $_POST["inviteeEmail"];
			$inviteePhoneNum = $_POST["inviteePhoneNum"];
			$inviteeType = $_POST["inviteeTypeForm"];

			// Check if the name, email, or phone number already exists
			$isNameExists = $this->isNameExists($currentEventID,0,$inviteeFirstName,$inviteeMiddleName,$inviteeLastName);
			$isEmailExists = $this->isEmailExists($currentEventID,0,$inviteeEmail);
			$isPhoneNumExists = $this->isPhoneNumExists($currentEventID,0,$inviteePhoneNum);
			if ($isNameExists) {
				$response = array('Status' => "nameAlreadyExists");
				echo json_encode($response);
			}else if($isEmailExists){
				$response = array('Status' => "emailAlreadyExists");
				echo json_encode($response);
			}else if($isPhoneNumExists){
				$response = array('Status' => "phoneNumAlreadyExists");
				echo json_encode($response);
			}else{
				// Set Default Time Zone
				date_default_timezone_set("Asia/Manila");

				// Current Date and Time
				$currentDateTime = date("Y-m-d H:i:s");

				// Database Connection
				include 'dbConnection.php';

				// Generate Invitee Code
				$inviteeCode  = "IVT-".date("YmdHis")."-".strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));	

				// Insert Query
				$insertStmt = $conn->prepare("INSERT INTO `invitees`(`event_ID`, `invitee_code`, `firstname`, `middlename`, `lastname`, `email`, `phonenum`, `type`, `datetime_added`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$insertStmt->bind_param("issssssss", $currentEventID, $inviteeCode, $inviteeFirstName, $inviteeMiddleName, $inviteeLastName, $inviteeEmail, $inviteePhoneNum, $inviteeType, $currentDateTime);

				// Validate if Insert Query is successful or not
				if ($insertStmt->execute()) {
					$insertStmt->close();

					$this->sendDirectEmailInvitation($currentEventID, $inviteeCode, $inviteeFirstName." ".$inviteeMiddleName." ".$inviteeLastName, $inviteeEmail);
				}else{
					$response = array('Status' => "error");
					echo json_encode($response);
				}
				
			}

		}

		/**
		 * To edit up an invitee
		*/
		public function editInvitee($currentEventID)
		{
			// Present Invitee ID
			$presentInviteeID = $_POST["selectedInviteeID"];

			// Edited Invitee Details
			$inviteeFirstName = $_POST["inviteeFirstName"];
			$inviteeMiddleName = $_POST["inviteeMiddleName"];
			$inviteeLastName = $_POST["inviteeLastName"];
			$inviteeEmail = $_POST["inviteeEmail"];
			$inviteePhoneNum = $_POST["inviteePhoneNum"];
			$inviteeType = $_POST["inviteeTypeForm"];

			// Check if the name, email, or phone number already exists
			$isNameExists = $this->isNameExists($currentEventID,$presentInviteeID,$inviteeFirstName,$inviteeMiddleName,$inviteeLastName);
			$isEmailExists = $this->isEmailExists($currentEventID,$presentInviteeID,$inviteeEmail);
			$isPhoneNumExists = $this->isPhoneNumExists($currentEventID,$presentInviteeID,$inviteePhoneNum);
			if ($isNameExists) {
				$response = array('Status' => "nameAlreadyExists");
				echo json_encode($response);
			}else if($isEmailExists){
				$response = array('Status' => "emailAlreadyExists");
				echo json_encode($response);
			}else if($isPhoneNumExists){
				$response = array('Status' => "phoneNumAlreadyExists");
				echo json_encode($response);
			}else{
				// Database Connection
				include 'dbConnection.php';

				// Update Query
				$updateStmt = $conn->prepare("UPDATE `invitees` SET `firstname`=?, `middlename`=?, `lastname`=?, `email`=?, `phonenum`=?, `type`=? WHERE `event_ID` = ? AND `ID` = ?");
				$updateStmt->bind_param('ssssssii', $inviteeFirstName, $inviteeMiddleName, $inviteeLastName, $inviteeEmail, $inviteePhoneNum, $inviteeType, $currentEventID, $presentInviteeID);

				// Validate if Update Query is successful or not
				if ($updateStmt->execute()) {
					$updateStmt->close();

					// Get Invitee Code
					$ivtCodeStmt = $conn->prepare("SELECT `invitee_code` FROM `invitees` WHERE `event_ID` = ? AND `ID` = ?");
					$ivtCodeStmt->bind_param("ii", $currentEventID, $presentInviteeID);
					if ($ivtCodeStmt->execute()) {
						$ivtCodeResult = $ivtCodeStmt->get_result();
						$ivtCodeStmt->close();
						$ivtCodeRow = $ivtCodeResult->fetch_assoc();
						$this->sendDirectEmailInvitation($currentEventID, $ivtCodeRow["invitee_code"], $inviteeFirstName." ".$inviteeMiddleName." ".$inviteeLastName, $inviteeEmail);
					} else {
						$response = array('Status' => "error");
						echo json_encode($response);
					}
				}else{
					$response = array('Status' => "error");
					echo json_encode($response);
				}
			}

		}

		/**
		 * To delete an invitee
		*/
		public function deleteInvitee($currentEventID)
		{
			// Database Connection
			include 'dbConnection.php';

			// Invitee Details
			$inviteeID = $_POST["inviteeDeleteID"];

			// Delete Query
			$deleteStmt = $conn->prepare("UPDATE `invitees` SET `status`= 0 WHERE `ID` = ?");
			$deleteStmt->bind_param('i', $inviteeID);

			// Validate if Delete Query is successful or not
			if ($deleteStmt->execute()) {
				$deleteStmt->close();
				$response = array('Status' => "success");
				echo json_encode($response);
			}else{
				$response = array('Status' => "error");
				echo json_encode($response);
			}
		}

		/**
		 * To get an invitee information 
		 */
		public function getInvitee($currentEventID){
			if($_POST["inviteeID"]) {
				// Database Connection
				include 'dbConnection.php';

				// Get Selected Invitee ID
				$selectedEventID = $_POST["inviteeID"];

				// Fetch up an invitee information;
				$inviteeStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `ID` = ?");
				$inviteeStmt->bind_param('ii', $currentEventID, $selectedEventID);
		        $inviteeStmt->execute();
		        $inviteeInfo =  $inviteeStmt->get_result();
		        $inviteeStmt->close();
				while ($row = $inviteeInfo->fetch_assoc()){
					// Get Invitee Code
					$text = $row["invitee_code"];

					// Barcode Model
					include 'model/barcode-encoded.php';

					// Encode row into JSON
					echo json_encode(array_merge($row, array("base64IVT" => $base64Encoded)));
				}

			}
		}

		/**
		 * To send an email invitation
		*/
		public function sendEmailInvitation($currentEventID)
		{
			// Get Selected ID
			$selectedEventID = $_POST["inviteeID"];

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
	            $eventDesciption = $row["description"];
	            $eventAgenda = $row["agenda"];
	            $eventTheme = $row["theme"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Fetch up an invitee information
			$inviteeStmt = $conn->prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `ID` = ?");
			$inviteeStmt->bind_param('ii', $currentEventID, $selectedEventID);
	        $inviteeStmt->execute();
	        $inviteeInfo =  $inviteeStmt->get_result();
	        $inviteeStmt->close();
			while ($row = $inviteeInfo->fetch_assoc()){
				$inviteeCode =  $row["invitee_code"];
				$inviteeName =  $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
				$inviteeEmail =  $row["email"];
			}

			try {
				// Assign Invitee Code for encoding into base64
				$text = $inviteeCode;

				// Barcode Model
				include 'model/barcode-encoded.php';
				
				// Get Barcode Base64
			    $inviteeBarcodeData = $base64Encoded;

			    // Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
			    $mail->isSMTP();
			    $mail->Host = 'smtp.hostinger.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'invitations@attend-certify.com';
			    $mail->Password = 'VLDHpmnE_c2ZhLA';

			    // Sender and recipient settings
			    $mail->setFrom('invitations@attend-certify.com', 'Attend and Certify Invitations');
			    $mail->addAddress($inviteeEmail, $inviteeName);
			    // $mail->addReplyTo('example@gmail.com', 'Sender Name'); // to set the reply to

			    // Setting the email content
			    $mail->IsHTML(true);
			    $mail->Subject = $eventTitle." - Event Invitation | Attend and Certify";
			    // $mail->AddEmbeddedImage('invitees-barcodes/'.$inviteeCode.'.png', 'barcodeEmbedded', $inviteeName." - ".$inviteeCode.'.png');
			    $mail->addStringEmbeddedImage(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inviteeBarcodeData)), 'barcodeEmbedded', $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");
			    $mail->Body = include 'model/html-email-template-for-invitation.php';
			    // $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';

			    // Add Static Attachment
				// $mail->addAttachment('invitees-barcodes/'.$inviteeCode.'.png', $inviteeName." - ".$inviteeCode.'.png');    //Optional name
				$mail->AddStringAttachment(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inviteeBarcodeData)), $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");

			    $mail->send();
			    // echo "Email message sent.";
				
			    $response = array('Status' => "success");
				echo json_encode($response);
			} catch (Exception $e) {
			    // echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";

			    $response = array('Status' => "error");
				echo json_encode($response);
			}
		}

		/**
		 * To send directly an email invitation
		 */
		public function sendDirectEmailInvitation($currentEventID, $inviteeCode, $inviteeName, $inviteeEmail) {
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
				if ($row['date'] == $row['date_end']) {
					$eventDate = date_format(date_create($row["date"]),"F d, Y");
				} else {
					if (date_format(date_create($row['date']),"Y") == date_format(date_create($row['date_end']),"Y")) {
						$yearStr = date_format(date_create($row['date']),"Y");
						if (date_format(date_create($row['date']),"M") == date_format(date_create($row['date_end']),"M")) {
							$monthStr = date_format(date_create($row['date']),"M ").date_format(date_create($row['date']),"d-").date_format(date_create($row['date_end']),"d");
						} else {
							$monthStr = date_format(date_create($row['date']),"M d").'-'.date_format(date_create($row['date_end']),"M d");
						}
						$eventDate = $monthStr.", ".$yearStr;
					} else {
						$eventDate = date_format(date_create($row['date']),"M d, Y").' - '.date_format(date_create($row['date_end']),"M d, Y");
					}
				}
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	            $eventDesciption = $row["description"];
	            $eventAgenda = $row["agenda"];
	            $eventTheme = $row["theme"];
	            $certTemplate = $row["certificate_template"];
	        }
			
			try {
				// Assign Invitee Code for encoding into base64
				$text = $inviteeCode;

				// Barcode Model
				include 'model/barcode-encoded.php';
				
				// Get Barcode Base64
			    $inviteeBarcodeData = $this->createCustomInvitationFile($base64Encoded, $inviteeCode, $inviteeName, $eventTitle, "$eventDate at $eventTimeInclusive-$eventTimeConclusive", $eventVenue);

			    // Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
			    $mail->isSMTP();
			    $mail->Host = 'smtp.hostinger.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'invitations@attend-certify.com';
			    $mail->Password = 'VLDHpmnE_c2ZhLA';

			    // Sender and recipient settings
			    $mail->setFrom('invitations@attend-certify.com', 'Attend and Certify Invitations');
			    $mail->addAddress($inviteeEmail, $inviteeName);

			    // Setting the email content
			    $mail->IsHTML(true);
			    $mail->Subject = $eventTitle." - Event Invitation | Attend and Certify";
			    $mail->addStringEmbeddedImage(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Encoded)), 'barcodeEmbedded', $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");
			    $mail->Body = include 'model/html-email-template-for-invitation.php';
				$mail->AddStringAttachment(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inviteeBarcodeData)), $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");

			    $mail->send();

			    $response = array('Status' => "success");
				echo json_encode($response);
			} catch (Exception $e) {
			    $response = array('Status' => "error");
				echo json_encode($response);
			}
		}

		/**
		 * To send selected email invitation
		*/
		public function sendSelectedInvitation($currentEventID)
		{
			// Fetch Selected Invitee IDs and Count them
			$selectedInviteeIDS = $_POST["selectedInviteeIDs"];
			$selectedInviteeIDS = json_decode($selectedInviteeIDS);
			$idsCount = count($selectedInviteeIDS);

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
	            $eventDesciption = $row["description"];
	            $eventAgenda = $row["agenda"];
	            $eventTheme = $row["theme"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // passing true in constructor enables exceptions in PHPMailer
			$mail = new PHPMailer(true);

			// Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
		    $mail->isSMTP();
		    $mail->Host = 'smtp.hostinger.com';
		    $mail->SMTPAuth = true;
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		    $mail->Port = 587;

		    $mail->Username = 'invitations@attend-certify.com';
		    $mail->Password = 'VLDHpmnE_c2ZhLA';

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// Create Placeholder for Prepared Statements and Bind String
			$idsPlaceholders = implode(',', array_fill(0, $idsCount, '?'));
			$bindStr = str_repeat('i', $idsCount);

			// Fetch up selected Invitees info from database
			$selectedStmt = $conn -> prepare("SELECT *  FROM `invitees` WHERE `event_ID` = ? AND `ID` in ($idsPlaceholders)");
			$selectedStmt->bind_param("i".$bindStr, $currentEventID, ...$selectedInviteeIDS);
			$selectedStmt->execute();
			$selectedResults =  $selectedStmt->get_result();
			$selectedStmt->close();

			// Include Barcode Generator
			include "model/bulk-barcode-generator.php";

			// Initalize response
			$response = "error";

			// Initial SMS Config
			$basic  = new \Vonage\Client\Credentials\Basic("fe8f23c8", "aMcOvNgvV5DO2kiU");
			$client = new \Vonage\Client($basic);

			// Send Selected Invitees' Invitation
			while ($row = $selectedResults->fetch_assoc()) {
				$inviteeCode =  $row["invitee_code"];
				$inviteeName =  $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
				$inviteeEmail =  $row["email"];
				$inviteePhoneNum = $row["phonenum"];

				try {

					$pdf417->encode($inviteeCode);

				    $base64Encoded = $pdf417->forWeb("BASE64", $inviteeCode);
					
					// Get Barcode Base64
				    $inviteeBarcodeData = $this->createCustomInvitationFile($base64Encoded, $inviteeCode, $inviteeName, $eventTitle, "$eventDate - $eventTimeInclusive-$eventTimeConclusive", $eventVenue);

				    // Sender and recipient settings
				    $mail->setFrom('invitations@attend-certify.com', 'Attend and Certify Invitations');
				    $mail->addAddress($inviteeEmail, $inviteeName);
				    // $mail->addReplyTo('example@gmail.com', 'Sender Name'); // to set the reply to

				    // Setting the email content
				    $mail->IsHTML(true);
				    $mail->Subject = $eventTitle." - Event Invitation | Attend and Certify";
				    $mail->clearAttachments();
				    $mail->addStringEmbeddedImage(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Encoded)), 'barcodeEmbedded', $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");
				    $mail->Body = include 'model/html-email-template-for-invitation.php';

				    // Add Static Attachment
					$mail->AddStringAttachment(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $inviteeBarcodeData)), $inviteeName." - ".$inviteeCode.'.png', "base64", "image/png");

				    $mail->send();

				    $mail->clearAddresses();

				    $response = "success";

				    // Send SMS Message
				    // $response = $this->sendSMS($client, $inviteePhoneNum, $inviteeEmail);
					
				} catch (Exception $e) {
				    $response = "error";
				}
			}
			echo json_encode(array('Status' => $response));

		}

		/**
		 * To get selected invitees
		*/
		public function getSelectedInvitees($currentEventID)
		{
			// Fetch Selected Invitee IDs and Count them
			$selectedInviteeIDS = $_POST["selectedInviteeIDs"];
			$selectedInviteeIDS = json_decode($selectedInviteeIDS);
			$idsCount = count($selectedInviteeIDS);

			// Database Connection
			include 'dbConnection.php';

    		// Create Placeholder for Prepared Statements and Bind String
			$idsPlaceholders = implode(',', array_fill(0, $idsCount, '?'));
			$bindStr = str_repeat('i', $idsCount);

			// Fetch up selected Invitees info from database
			$selectedStmt = $conn -> prepare("SELECT *  FROM `invitees` WHERE `event_ID` = ? AND `ID` in ($idsPlaceholders)");
			$selectedStmt->bind_param("i".$bindStr, $currentEventID,...$selectedInviteeIDS);
			$selectedStmt->execute();
			$selectedResults =  $selectedStmt->get_result();
			$selectedStmt->close();

			// Initialize Invitee Array
			$selectedArray = array();

			$getSelectedResult = "error";

			// Get Selected Invitees
			while ($row = $selectedResults->fetch_assoc()) {
				$selectedArray[] = $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
				$getSelectedResult = "success";
			}

			echo json_encode(array(
				"Status" => $getSelectedResult,
				"selectedData" => $selectedArray
			));
		}

		/**
		 * To delete selected invitees
		*/
		public function deleteSelectedInvitees($currentEventID)
		{
			// Fetch Selected Invitee IDs and Count them
			$selectedInviteeIDS = $_POST["selectedInviteeIDs"];
			$selectedInviteeIDS = json_decode($selectedInviteeIDS);
			$idsCount = count($selectedInviteeIDS);

			// Database Connection
			include 'dbConnection.php';

    		// Create Placeholder for Prepared Statements and Bind String
			$idsPlaceholders = implode(',', array_fill(0, $idsCount, '?'));
			$bindStr = str_repeat('i', $idsCount);

			// Delete invitees using Update Statement
			$deleteStmt = $conn->prepare("UPDATE `invitees` SET `status` = 0 WHERE `event_ID` = ? AND `ID` in($idsPlaceholders)");
			$deleteStmt->bind_param("i".$bindStr, $currentEventID,...$selectedInviteeIDS);

			// Validate if Delete Query is successful or not
			if ($deleteStmt->execute()) {
				$deleteStmt->close();
				$response = array('Status' => "success");
				echo json_encode($response);
			}else{
				$response = array('Status' => "error");
				echo json_encode($response);
			}

		}

		/**
		 * To send SMS message
		*/
		public function sendSMS($clientConfig, $senderNum, $senderEmail)
		{
			$smsResponse = $clientConfig->sms()->send(
					    new \Vonage\SMS\Message\SMS("63".substr($senderNum,1), "AttnCert", 'Your are invited with the event that sent to you thru your email: '.$senderEmail)
			);

			$message = $smsResponse->current();

			if ($message->getStatus() == 0) {
			    $response = "success";
			} else {
			    $response = "error";
			}
			return $response;
		}

		/**
		 * To Create Custom Invitation Barcode Image File
		*/
		public function createCustomInvitationFile($barcodeData, $ivtCode, $ivtName, $ivtEvent, $ivtDateTime, $ivtVenue)
		{
			// Get Font File
			$fontFile = "fonts/Roboto-Medium.ttf";

			// Decode Barcode Data and Get Dimensions
			$imgBarcode = imagecreatefromstring(base64_decode($barcodeData));
			$imgDimen = getimagesizefromstring(base64_decode($barcodeData));

			// Image Dimensions
			$imgWidth = 784;
			$imgHeight = $imgDimen[1] + 230;

			// Starting Point Vertically to add Text
			$yAxis = $imgHeight - 160;

			// Invitee Information Font Size
			$inviteeInfoFontSize = 20;

			// System Title Config
			$systemTitleText = "ATTEND and CERTIFY";
			$systemTitleFontSize = 25;
			$systemTitleTextBox = imagettfbbox($systemTitleFontSize, 0, $fontFile, $systemTitleText);
			$systemTitleTextWidth = $systemTitleTextBox[2] - $systemTitleTextBox[0];
			$systemTitleYCoordinates = ($imgWidth/2) - ($systemTitleTextWidth/2);

			// Invitee Code Config
			$inviteeCodeText = "CODE: $ivtCode";

			// Invitee Name Config
			$inviteeName = "INVITEE: $ivtName";
			$inviteeNameParagraph = explode('|', wordwrap($inviteeName, 50, '|'));
			foreach ($inviteeNameParagraph as $textLine) {
				$imgHeight += 30;
			}

			// Event Config
			$eventTitle = "EVENT: $ivtEvent";
			$eventTitleParagraph = explode('|', wordwrap($eventTitle, 50, '|'));
			foreach ($eventTitleParagraph as $textLine) {
				$imgHeight += 30;
			}

			// Date and Time Config
			$dateTimeText = "DATE and TIME: $ivtDateTime";
			$dateTimeParagraph = explode('|', wordwrap($dateTimeText, 50, '|'));
			foreach ($dateTimeParagraph as $textLine) {
				$imgHeight += 30;
			}

			// Venue Config
			$venueText = "VENUE: $ivtVenue";
			$venueParagraph = explode('|', wordwrap($venueText, 50, '|'));
			foreach ($venueParagraph as $textLine) {
				$imgHeight += 30;
			}

			// Important Notice Text
			$importantNoticeText = "* Please present this on scheduled event at designated venue above.";
			$importantNoticeFontSize = 12;
			$importantNoticeTextBox = imagettfbbox($importantNoticeFontSize, 0, $fontFile, $importantNoticeText);
			$importantNoticeTextWidth = $importantNoticeTextBox[2] - $importantNoticeTextBox[0];
			$importantNoticeYCoordinates = ($imgWidth/2) - ($importantNoticeTextWidth/2);

			// All Rights Reserved Text
			$allRightsReservedText = "Copyright ".date("Y").". All Rights Reserved. This system created by PALADO Group.";
			$allRightsReservedFontSize = 15;
			$allRightsReservedTextBox = imagettfbbox($allRightsReservedFontSize, 0, $fontFile, $allRightsReservedText);
			$allRightsReservedTextWidth = $allRightsReservedTextBox[2] - $allRightsReservedTextBox[0];
			$allRightsReservedeYCoordinates = ($imgWidth/2) - ($allRightsReservedTextWidth/2);

			// Create the size of image or blank image
			$imageFrame = imagecreate($imgWidth, $imgHeight);

			// Set the background color of image
			$backgroundColor = imagecolorallocate($imageFrame, 255, 255, 255);

			// Set the text color of image
			$textColor = imagecolorallocate($imageFrame, 0, 0, 0);

			// Add System Name
			imagefilledrectangle($imageFrame, 0, 0, $imgWidth, 70, imagecolorallocate($imageFrame, 0, 123, 255));
			imagettftext($imageFrame, $systemTitleFontSize, 0, $systemTitleYCoordinates, 48, imagecolorallocate($imageFrame, 255, 255, 255), $fontFile, $systemTitleText);

			// Add Invitee Barcode
			imagecopymerge($imageFrame, $imgBarcode, 0, 70, 0, 0, $imgWidth, 300, 100);

			// Add Invitee Code Text to Image
			imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $inviteeCodeText);
			$yAxis += 35;

			// Add Invitee Name Text to Image
			foreach ($inviteeNameParagraph as $textLine) {
				imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $textLine);
				$yAxis += 30;
			}
			$yAxis += 5;

			// Add Event Title Text to Image
			foreach ($eventTitleParagraph as $textLine) {
				imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $textLine);
				$yAxis += 30;
			}
			$yAxis += 5;

			// Add Date and Time Text to Image
			foreach ($dateTimeParagraph as $textLine) {
				imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $textLine);
				$yAxis += 30;
			}
			$yAxis += 5;

			// Add Venue Text to Image
			foreach ($venueParagraph as $textLine) {
				imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $textLine);
				$yAxis += 30;
			}
			$yAxis += 5;

			// Add Important Notice to Image
			imagettftext($imageFrame, $importantNoticeFontSize, 0, $importantNoticeYCoordinates, $yAxis, $textColor, $fontFile, $importantNoticeText);
			$yAxis += 60;

			// Add All Rights Reserved to Image
			imagefilledrectangle($imageFrame, 0, $imgHeight, $imgWidth, $yAxis-30, imagecolorallocate($imageFrame, 0, 123, 255));
			imagettftext($imageFrame, $allRightsReservedFontSize, 0, $allRightsReservedeYCoordinates, $yAxis, imagecolorallocate($imageFrame, 255, 255, 255), $fontFile, $allRightsReservedText);

			// Encode Image File and Clear It Immediately
			ob_start();
			imagepng($imageFrame);
			$imageFramedata = ob_get_contents();
			imagedestroy($imageFrame);
			ob_end_clean();

			return base64_encode($imageFramedata);
		}
	}
?>