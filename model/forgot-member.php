<?php
	// Use PHPMailer
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load PHPMailer's autoloader
	require __DIR__.'/Exception.php';
	require __DIR__.'/PHPMailer.php';
	require __DIR__.'/SMTP.php';

	// Search account using email address
	if (!empty($_POST['forgotAction']) && $_POST['forgotAction'] == 'searchAccount') {
		// Access Class
		$forgot = new ForgotMember();

		// Validate if the email exists in the database
		$isEmailExists = $forgot->isEmailExists($_POST['emailSearch']);
		if ($isEmailExists) {
			// Get Account Record
			$accountRecord = $forgot->getAccount($_POST['emailSearch']);

			// Generated Verification Code
			$verificationCode = strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 12));

			// Convert Generated Verification Code into Hashe one for security reasons
			session_start();
			session_unset();
			$_SESSION["verificationCode"] = $verificationCode;
	        $_SESSION["tempHashedVerificationCode"] = password_hash($verificationCode, PASSWORD_DEFAULT);
	        session_write_close();

	        // Send email verification
	        $emailVerification = $forgot->sendVerificationCode($_POST['emailSearch'], $accountRecord[0]["username"], $verificationCode);

	        // Verify if Email was successful
	        if ($emailVerification) {
	        	echo json_encode(array('emailStatus' => "existing", 'adminID' => $accountRecord[0]["ID"]));
	        } else {
	        	echo json_encode(array('emailStatus' => "error"));
	        }

		} else {
			echo json_encode(array('emailStatus' => "notExisting"));
		}
		
	}

	// Resend Verification Code
	if (!empty($_POST['forgotAction']) && $_POST['forgotAction'] == 'resendCode') {
		// Access Class
		$forgot = new ForgotMember();

		// Get Account Record
		$accountRecord = $forgot->getAccount($_POST['emailSearch']);

		// Get Verification Code
		session_start();
		$verificationCode = $_SESSION["verificationCode"];
        session_write_close();

        // Send email verification
        $emailVerification = $forgot->sendVerificationCode($_POST['emailSearch'], $accountRecord[0]["username"], $verificationCode);

        // Verify if Email was successful
        if ($emailVerification) {
        	echo json_encode(array('resendStatus' => "success"));
        } else {
        	echo json_encode(array('resendStatus' => "failed"));
        }

	}

	// Verify Code
	if (!empty($_POST['forgotAction']) && $_POST['forgotAction'] == 'verifyCode') {
		// Temporary Store Hashed Password
		session_start();
		$hashedVerificationCode = $_SESSION["tempHashedVerificationCode"];
        session_write_close();
		// Verify if code input matches the hashed one
		if (password_verify($_POST['verificationCode'], $hashedVerificationCode)) {
            echo json_encode(array('codeStatus' => "valid"));
        } else {
        	echo json_encode(array('codeStatus' => "invalid"));
        }
	}

	// Reset and Save New Password
	if (!empty($_POST['forgotAction']) && $_POST['forgotAction'] == 'savePassword') {
		// Get Admin ID
		$adminID = $_POST['adminID'];

		// Generate Hashed Password
		$hashedPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

		// Using database connection file here
		include trim(__DIR__,"model").'/dbConnection.php';

		// Update Query for Reset and Save New Password
		$updateStmt = $conn->prepare("UPDATE `admin_accounts` SET `password`=? WHERE `ID` = ?");
		$updateStmt->bind_param('si', $hashedPassword, $adminID);

		// Validate if Update Query is successful or not
		if ($updateStmt->execute()) {
			$updateStmt->close();
			// Clear all the session variables
        	session_start();
			session_unset();
	        session_write_close();
	        
			echo json_encode(array('resetPasswordStatus' => "success"));	
		}else{
			echo json_encode(array('resetPasswordStatus' => "failed"));	
		}
	}

	/**
	 * 
	 */
	class ForgotMember
	{
		/**
		 * To check if email already exists
		 * 
		 * @param string $email
		 * @return boolean
		*/
		public function isEmailExists($email)
		{
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

			$emailStmt = $conn->prepare("SELECT * FROM `admin_accounts` WHERE `email` = ? AND `status` = 1");
		    $emailStmt->bind_param('s', $email);
		    $emailStmt->execute();
		    $checkResult =  $emailStmt->get_result();
		    $emailStmt->close();
			if ($checkResult->num_rows > 0) {
				$checkResult = true;
			}else{
				$checkResult = false;
			}
			return $checkResult;
		}

		/**
		 * To get Admin ID and Username using email addresss
		 * 
		 * @param string $email
		 * @return array
		*/
		public function getAccount($email)
		{
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

	    	// Get Account Query
	    	$emailStmt = $conn->prepare("SELECT `ID`, `username` FROM `admin_accounts` WHERE `email` = ? AND `status` = 1");
	    	$emailStmt->bind_param('s', $email);
		    $emailStmt->execute();
		    $accountQuery =  $emailStmt->get_result();
		    $emailStmt->close();
			if ($accountQuery->num_rows > 0){
				// output data of each row
                while($row = $accountQuery->fetch_assoc())
                {
                	$accountRecord[] = $row;
                }
			}
			if (! empty($accountRecord)) {
	            return $accountRecord;
	        }
		}

		/**
		 * To send email verification code
		 * 
		*/
		public function sendVerificationCode($email, $username, $verificationCode)
		{
			// Send Status
			$sendStatus = true;

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// passing true in constructor enables exceptions in PHPMailer
	        $mail = new PHPMailer(true);

	        try {
			    // Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
			    $mail->isSMTP();
			    $mail->Host = 'smtp.hostinger.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'accounts-support@attend-certify.com';
			    $mail->Password = 'VLDHpmnE_c2ZhLA';

			    // Sender settings
			    $mail->setFrom('accounts-support@attend-certify.com', 'Attend and Certify Account Support');
			    $mail->addAddress($email, $username);

			    // Setting the email content
			    $mail->IsHTML(true);
			    $mail->Subject = "Your Verification Code | Attend and Certify";
			    $mail->Body = include __DIR__.'/html-email-template-for-verification-code.php';

			    // Send to email
			    $mail->send();

	        } catch(Exception $e) {
	        	$sendStatus = false;
	        }

	        return $sendStatus;
		}
	}
?>