<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require __DIR__.'/Exception.php';
	require __DIR__.'/PHPMailer.php';
	require __DIR__.'/SMTP.php';

	// Get Registration Configuration
	if (!empty($_POST['registrationAction']) && $_POST['registrationAction'] == 'getRegistrationConfig') {
		// Get Event ID
		$currentEventID = $_POST['eventID'];

		// Access Class
		$registrationMem = new RegistrationMember();

		// Get Registration Config
		$registrationMem->getRegistrationConfig($currentEventID);

	}

	// Save Registration Configuration
	if (!empty($_POST['registrationAction']) && $_POST['registrationAction'] == 'saveRegistrationConfig') {
		// Get Event ID
		$currentEventID = $_POST['eventID'];

		// Access Class
		$registrationMem = new RegistrationMember();

		// Registration Config Array
		$regConfigArray = array(
			'openRegs' => $_POST['openRegs'],
			'unifiedRegs' => $_POST['unifiedRegs'],
			'allowEmployeeCheckbox' => $_POST['allowEmployeeCheckbox'],
			'allowStudentCheckbox' => $_POST['allowStudentCheckbox'],
			'allowFacultyCheckbox' => $_POST['allowFacultyCheckbox'],
			'allowGuestCheckbox' => $_POST['allowGuestCheckbox']
		);

		// Save Registration Config
		$registrationMem->saveRegistrationConfig($currentEventID, $regConfigArray);

	}

	// Add Invitee Registration
	if (!empty($_POST['registrationAction']) && $_POST['registrationAction'] == 'addInviteeRegistration') {
		// Get Event ID
		$currentEventID = $_POST['eventID'];

		// Access Class
		$registrationMem = new RegistrationMember();

		// Registration Config Array
		$ivtRegArray = array(
			'inviteeFirstNameInput' => $_POST['inviteeFirstNameInput'],
			'inviteeMiddleNameInput' => $_POST['inviteeMiddleNameInput'],
			'inviteeLastNameInput' => $_POST['inviteeLastNameInput'],
			'inviteeEmailInput' => $_POST['inviteeEmailInput'],
			'inviteePhoneNumInput' => $_POST['inviteePhoneNumInput'],
			'inviteeTypeSelect' => $_POST['inviteeTypeSelect']
		);

		// Save Registration Config
		$registrationMem->addInviteeRegistration($currentEventID, $ivtRegArray);
	}

	/**
	 * 
	 */
	class RegistrationMember
	{
		/**
		 * To add registration config
		*/
		public function addRegistrationConfig($currentEventID) {
			// Using database connection file here
			include trim(__DIR__,"model").'/dbConnection.php';

			$stmtResult = false;

			// Check if Registration Config Exists Statement
			$addRegConfigStmt = $conn->prepare("INSERT INTO `registration`(`event_ID`) VALUES (?)");
			$addRegConfigStmt->bind_param("i", $currentEventID);
			if ($addRegConfigStmt->execute()){
				$stmtResult = true;
				$addRegConfigStmt->close();
				$conn->close();
			}

			return $stmtResult;
		}

		/**
		 * To get registration config
		*/
		public function getRegistrationConfig($currentEventID) {
			// Check if Registration Exists
			if ($this->isRegConfigExists($currentEventID)["stmtResult"]) {
				if ($this->isRegConfigExists($currentEventID)["existsResult"]) {
					// Using database connection file here
					include trim(__DIR__,"model").'/dbConnection.php';

					$getRegConfigStmt = $conn->prepare("SELECT * FROM `registration` WHERE `event_ID` = ?");
					$getRegConfigStmt->bind_param("i", $currentEventID);
					if ($getRegConfigStmt->execute()) {
						$getRegConfigResult = $getRegConfigStmt->get_result();
						$getRegConfigStmt->close();
						$conn->close();
						if ($getRegConfigResult ->num_rows > 0) {
							while ($row = $getRegConfigResult->fetch_assoc()) {
								$regConfigArray = array(
									"openRegistration" => $row["openRegistration"],
									"allowedEmp" => $row["allowedEmp"],
									"allowedStud" => $row["allowedStud"],
									"allowedFaculty" => $row["allowedFaculty"],
									"allowedGuest" => $row["allowedGuest"],
									"allowedAll" => $row["allowedAll"]
								);
							}
							echo json_encode(array(
								'stmtResult' => true, 
								'regConfigArr' => $regConfigArray
							));	
						} else {
							echo json_encode(array(
								'stmtResult' => false, 
								'regConfigArr' => null
							));	
						}
					} else {
						echo json_encode(array(
							'stmtResult' => false, 
							'regConfigArr' => null
						));
					}
				} else {
					// Go to Add Registration Config Function
					$addReg = $this->addRegistrationConfig($currentEventID);
					if ($addReg) {
						$this->getRegistrationConfig($currentEventID);
					} else {
						echo json_encode(array(
							'stmtResult' => false, 
							'regConfigArr' => null
						));
					}
				}
			} else {
				echo json_encode(array(
					'stmtResult' => false, 
					'regConfigArr' => null
				));
			}
		}

		/**
		 * To check if registration exists
		*/
		public function isRegConfigExists($currentEventID) {
			// Using database connection file here
			include trim(__DIR__,"model").'/dbConnection.php';

			$stmtResult = false;
			$existsResult = false;

			// Check if Registration Config Exists Statement
			$regConfigStmt = $conn->prepare("SELECT `ID` FROM `registration` WHERE `event_ID` = ?");
			$regConfigStmt->bind_param("i", $currentEventID);
			if ($regConfigStmt->execute()){
				$stmtResult = true;
				$regConfigResult = $regConfigStmt->get_result();
				$regConfigStmt->close();
				$conn->close();
				if ($regConfigResult->num_rows > 0) {
					$existsResult = true;
				}
			}

			return array(
				'stmtResult' => $stmtResult,
				'existsResult' => $existsResult
			);
		}

		/**
		 * To save registration configuration
		*/
		public function saveRegistrationConfig($currentEventID, $regConfigArray)
		{
			// Using database connection file here
			include trim(__DIR__,"model").'/dbConnection.php';

			$saveStatus = false;

			// Save Registration Config Statement
			$saveRegConfigStmt = $conn->prepare("UPDATE `registration` SET 
				`openRegistration`= ?,
				`allowedEmp`= ?,
				`allowedStud`= ?,
				`allowedFaculty`= ?,
				`allowedGuest`= ?,
				`allowedAll`= ?
				WHERE `event_ID` = ?");
			$saveRegConfigStmt->bind_param("iiiiiii",
				$regConfigArray["openRegs"],
				$regConfigArray["allowEmployeeCheckbox"],
				$regConfigArray["allowStudentCheckbox"],
				$regConfigArray["allowFacultyCheckbox"],
				$regConfigArray["allowGuestCheckbox"],
				$regConfigArray["unifiedRegs"],
				$currentEventID
			);
			if ($saveRegConfigStmt->execute()) {
				$saveStatus = true;
				$saveRegConfigStmt->close();
				$conn->close();
			}

			echo json_encode(array('saveStatus' => $saveStatus));
		}

		/**
		 * To add up an invitee registration
		*/
		public function addInviteeRegistration($currentEventID, $inviteeRegistrationForm){
			// Invitee Details
			$inviteeFirstName = $inviteeRegistrationForm["inviteeFirstNameInput"];
			$inviteeMiddleName = $inviteeRegistrationForm["inviteeMiddleNameInput"];
			$inviteeLastName = $inviteeRegistrationForm["inviteeLastNameInput"];
			$inviteeEmail = $inviteeRegistrationForm["inviteeEmailInput"];
			$inviteePhoneNum = $inviteeRegistrationForm["inviteePhoneNumInput"];
			$inviteeType = $inviteeRegistrationForm["inviteeTypeSelect"];

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
				include trim(__DIR__,"model").'/dbConnection.php';

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
		 * To check if name already exists
		*/
		public function isNameExists($currentEventID,$currentInviteeID,$firstname,$middlename,$lastname)
		{
			// Using database connection file here
			include trim(__DIR__,"model").'/dbConnection.php';

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
			include trim(__DIR__,"model").'/dbConnection.php';

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
			include trim(__DIR__,"model").'/dbConnection.php';

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
		 * To send directly an email invitation
		 */
		public function sendDirectEmailInvitation($currentEventID, $inviteeCode, $inviteeName, $inviteeEmail) {
			// passing true in constructor enables exceptions in PHPMailer
			$mail = new PHPMailer(true);

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// Validate if the admin logged in
    		include '../validateLogin.php';

			// Database Connection
			include '../dbConnection.php';

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
			
			try {
				// Assign Invitee Code for encoding into base64
				$text = $inviteeCode;

				// Barcode Model
				include 'barcode-encoded.php';
				
				// Get Barcode Base64
			    $inviteeBarcodeData = $this->createCustomInvitationFile($base64Encoded, $inviteeCode, $inviteeName, $eventTitle, "$eventDate - $eventTimeInclusive-$eventTimeConclusive", $eventVenue);

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
			    $mail->Body = include 'html-email-template-for-invitation.php';
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
		 * To Create Custom Invitation Barcode Image File
		*/
		public function createCustomInvitationFile($barcodeData, $ivtCode, $ivtName, $ivtEvent, $ivtDateTime, $ivtVenue)
		{
			// Get Font File
			$fontFile = "../fonts/Roboto-Medium.ttf";

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
			imagettftext($imageFrame, $inviteeInfoFontSize, 0, 50, $yAxis, $textColor, $fontFile, $dateTimeText);
			$yAxis += 35;

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