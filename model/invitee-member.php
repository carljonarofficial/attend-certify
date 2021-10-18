<?php
	namespace InviteePot;

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
	        	$ivtRows = array(
	        		"view"=> '<button type="button" name="viewInvitee" id="'.$row["ID"].'" class="btn btn-primary btn-xs viewInvitee w-100"><i class="fas fa-eye"></i> View</button>',
	        		"ID"=> $row["ID"],
	        		"firstname"=> ucfirst($row["firstname"]),
	        		"middlename"=> ucfirst($row["middlename"]),
	        		"lastname"=> ucfirst($row["lastname"]),
	        		"type"=> $row["type"],
	        		"send"=> '<button type="button" name="sendEmailInvitee" id="'.$row["ID"].'" class="btn btn-success btn-xs sendEmailInvitee w-100"><i class="fas fa-envelope"></i> Send</button>',
	        		"edit"=> '<button type="button" name="editInvitee" id="'.$row["ID"].'" class="btn btn-warning btn-xs editInvitee w-100"><i class="fas fa-user-edit"></i> Edit</button>',
	        		"delete"=> '<button type="button" name="deleteInvitee" id="'.$row["ID"].'" class="btn btn-danger btn-xs deleteInvitee w-100"><i class="fas fa-trash-alt"></i> Delete</button>',
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

				// Database Connection
				include 'dbConnection.php';

				// Generate Invitee Code
				$inviteeCode  = "IVT-".date("YmdHis")."-".strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6));	

				// Insert Query
				$insertStmt = $conn->prepare("INSERT INTO `invitees`(`event_ID`, `invitee_code`, `firstname`, `middlename`, `lastname`, `email`, `phonenum`, `type`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
				$insertStmt->bind_param("isssssss", $currentEventID, $inviteeCode, $inviteeFirstName, $inviteeMiddleName, $inviteeLastName, $inviteeEmail, $inviteePhoneNum, $inviteeType);

				// Validate if Insert Query is successful or not
				if ($insertStmt->execute()) {
					$insertStmt->close();
					$response = array('Status' => "success");
					echo json_encode($response);
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
					$response = array('Status' => "success");
					echo json_encode($response);
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
			    $mail->Host = 'smtp.gmail.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'attend.certify@gmail.com'; // YOUR gmail email
			    $mail->Password = 'k0rnb33f19'; // YOUR gmail password

			    // Sender and recipient settings
			    $mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
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
	}
?>