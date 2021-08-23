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
		public function isNameExists($currentEventID,$firstname,$middlename,$lastname)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the name exists
			$sql = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `status` = 1 AND `firstname` = '$firstname' AND `middlename` = '$middlename' AND `lastname` = '$lastname'";

			$result = $conn->query($sql);
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
		public function isEmailExists($currentEventID,$email)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the email exists
			$sql = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `status` = 1 AND `email` = '$email'";

			$result = $conn->query($sql);
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
		public function isPhoneNumExists($currentEventID,$phonenum)
		{
			// Using database connection file here
			include 'dbConnection.php';

			// Query if the email exists
			$sql = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `status` = 1 AND `phonenum` = '$phonenum'";

			$result = $conn->query($sql);
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
    		$sqlQuery = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `status` = 1 ";

    		// For Search Query
    		if(!empty($_POST["search"]["value"])){
    			$sqlQuery .= "AND (`firstname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `middlename` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `lastname` LIKE '".$_POST["search"]["value"]."%' ";
    		}

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY ID ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of invitees
			$inviteeInfo = $conn->query($sqlQuery);

			// Fetch up number of rows
			$sqlNumQuery = "SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `status` = 1 ";
			$numInvitees = $conn->query($sqlNumQuery);
			$numRows = mysqli_num_rows($numInvitees);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$inviteeData = array();
	        while ($row = $inviteeInfo->fetch_assoc()) {
	        	$ivtRows = array();
	        	$ivtRows[] = '<button type="button" name="viewInvitee" id="'.$row["ID"].'" class="btn btn-primary btn-xs viewInvitee w-100"><i class="fas fa-eye"></i> View</button>';
	        	$ivtRows[] = $row["ID"];
	        	$ivtRows[] = ucfirst($row["firstname"]);
	        	$ivtRows[] = ucfirst($row["middlename"]);
	        	$ivtRows[] = ucfirst($row["lastname"]);
	        	$ivtRows[] = $row["type"];
	        	$ivtRows[] = '<button type="button" name="sendEmailInvitee" id="'.$row["ID"].'" class="btn btn-success btn-xs sendEmailInvitee w-100"><i class="fas fa-envelope"></i> Send</button>';
	        	$ivtRows[] = '<button type="button" name="editInvitee" id="'.$row["ID"].'" class="btn btn-warning btn-xs editInvitee w-100"><i class="fas fa-user-edit"></i> Edit</button>';
	        	$ivtRows[] = '<button type="button" name="deleteInvitee" id="'.$row["ID"].'" class="btn btn-danger btn-xs deleteInvitee w-100"><i class="fas fa-trash-alt"></i> Delete</button>';
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
			$isNameExists = $this->isNameExists($currentEventID,$inviteeFirstName,$inviteeMiddleName,$inviteeLastName);
			$isEmailExists = $this->isEmailExists($currentEventID,$inviteeEmail);
			$isPhoneNumExists = $this->isPhoneNumExists($currentEventID,$inviteePhoneNum);
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
				$query = "INSERT INTO `invitees`(`event_ID`, `invitee_code`, `firstname`, `middlename`, `lastname`, `email`, `phonenum`, `type`) VALUES ('$currentEventID','$inviteeCode','$inviteeFirstName','$inviteeMiddleName','$inviteeLastName','$inviteeEmail','$inviteePhoneNum','$inviteeType')";

				// Validate if Insert Query is successful or not
				if ($conn->query($query) === TRUE) {

					// Text to be encoded into the barcode
    				//$text = $inviteeCode;

					// Barcode Model
					//include 'model/barcode-creator.php';

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

			// Database Connection
			include 'dbConnection.php';

			// Update Query
			$query = "UPDATE `invitees` SET `firstname`='$inviteeFirstName',`middlename`='$inviteeMiddleName',`lastname`='$inviteeLastName',`email`='$inviteeEmail',`phonenum`='$inviteePhoneNum',`type`='$inviteeType' WHERE `event_ID` = $currentEventID AND `ID` = $presentInviteeID";

			// Validate if Update Query is successful or not
			if ($conn->query($query) === TRUE) {
				$response = array('Status' => "success");
				echo json_encode($response);
			}else{
				echo $query;
				$response = array('Status' => "error");
				echo json_encode($response);
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
			$query = "UPDATE `invitees` SET `status`= 0 WHERE `ID` = $inviteeID";

			// Validate if Delete Query is successful or not
			if ($conn->query($query) === TRUE) {
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

				// Fetch up an invitee information
				$inviteeInfo = $conn->query("SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `ID` = ".$_POST["inviteeID"]);
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
	            $eventDesciption = $row["description"];
	            $eventAgenda = $row["agenda"];
	            $eventTheme = $row["theme"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Fetch up an invitee information
			$inviteeInfo = $conn->query("SELECT * FROM `invitees` WHERE `event_ID` = $currentEventID AND `ID` = ".$_POST["inviteeID"]);
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