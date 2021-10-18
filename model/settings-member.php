<?php

	// Validate if the admin logged in
    include trim(__DIR__,"model").'/validateLogin.php';

    // Save username into database
	if (!empty($_POST['saveSettingsAction']) && $_POST['saveSettingsAction'] == 'saveUsername') {
		// Access Class
		$settings = new SettingsMember();

		// Validates if the new username already exists in the database
		$isUsernameExists = $settings->isUsernameExists($_POST['newUsername']);
		if ($isUsernameExists) {
			echo json_encode(array('usernameStatus' => "existing"));
		} else {
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

	    	// Fetch Username from Form
	    	$newUsername = $_POST['newUsername'];

			$updateStmt = $conn->prepare("UPDATE `admin_accounts` SET `username`=? WHERE `ID` = ?");
			$updateStmt->bind_param('si', $newUsername, $id);

			// Validate if Update Query is successful or not
			if ($updateStmt->execute()) {
				$updateStmt->close();
				session_start();
	            $_SESSION["username"] = $newUsername;
	            session_write_close();
				echo json_encode(array('usernameStatus' => "success"));
			}else{
				echo json_encode(array('usernameStatus' => "failed"));
			}

			
		}
	}

	// Save email into database
	if (!empty($_POST['saveSettingsAction']) && $_POST['saveSettingsAction'] == 'saveEmail') {
		// Access Class
		$settings = new SettingsMember();

		// Validates if the new email already exists in the database
		$isEmailExists = $settings->isEmailExists($_POST['newEmail']);
		if ($isEmailExists) {
			echo json_encode(array('emailStatus' => "existing"));
		} else {
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

	    	// Fetch Username from Form
	    	$newEmail = $_POST['newEmail'];

			$updateStmt = $conn->prepare("UPDATE `admin_accounts` SET `email`=? WHERE `ID` = ?");
			$updateStmt->bind_param('si', $newEmail, $id);

			// Validate if Update Query is successful or not
			if ($updateStmt->execute()) {
				$updateStmt->close();
				session_start();
	            $_SESSION["email"] = $newEmail;
	            session_write_close();
				echo json_encode(array('emailStatus' => "success"));
			}else{
				echo json_encode(array('emailStatus' => "failed"));
			}
		}

	}

	// Delete Account
	if (!empty($_POST['saveSettingsAction']) && $_POST['saveSettingsAction'] == 'deleteAccount') {
		// Using database connection file here
    	include trim(__DIR__,"model").'/dbConnection.php';

		$deleteStmt = $conn->prepare("UPDATE `admin_accounts` SET `status` = 0 WHERE `ID` = ?");
		$deleteStmt->bind_param('i', $id);

		// Validate if Update Query Statement is successful or not
		if ($deleteStmt->execute()) {
			$deleteStmt->close();
			// Clear all the session variables
			session_start();
			session_unset();
	        $_SESSION["delete-account-validation-msg"] = "success";
	        session_write_close();
			echo json_encode(array('deleteStatus' => "success"));
		}else{
			echo json_encode(array('deleteStatus' => "failed"));
		}
		
	}

	// Save New Password
	if (!empty($_POST['saveSettingsAction']) && $_POST['saveSettingsAction'] == 'savePassword') {
		// Access Class
		$settings = new SettingsMember();

		// Validates if the current password matches in the database
		$isPasswordMatch = $settings->isPasswordMatch($id, $_POST['currentPassword']);
		if ($isPasswordMatch) {
			// Validate if the current password matches with new one
			if ($_POST['currentPassword'] == $_POST['newPassword']) {
				echo json_encode(array('passwordStatus' => "matched"));	
			} else {
				// Generate hashed password
				$newHashedPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

				// Using database connection file here
				include trim(__DIR__,"model").'/dbConnection.php';

				// Update Query for Save New Password
				$updateStmt = $conn->prepare("UPDATE `admin_accounts` SET `password`=? WHERE `ID` = ?");
				$updateStmt->bind_param('si', $newHashedPassword, $id);

				// Validate if Update Query is successful or not
				if ($updateStmt->execute()) {
					$updateStmt->close();
					echo json_encode(array('passwordStatus' => "success"));	
				}else{
					echo json_encode(array('passwordStatus' => "failed"));	
				}
			}
			
		} else {
			echo json_encode(array('passwordStatus' => "invalid"));
		}

	}

	/**
	 * 
	 */
	class SettingsMember
	{

		/**
		 * To check if username already exists
		 *
		 * @param string $username
		 * @return boolean
		*/
		public function isUsernameExists($username)
		{
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

			$usernameStmt = $conn->prepare("SELECT * FROM `admin_accounts` WHERE `username` = ? AND `status` = 1");
		    $usernameStmt->bind_param('s', $username);
		    $usernameStmt->execute();
		    $checkResult =  $usernameStmt->get_result();
		    $usernameStmt->close();
			if ($checkResult->num_rows > 0) {
				$checkResult = true;
			}else{
				$checkResult = false;
			}
			return $checkResult;
		}

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
		 * To check if the current password matches in the database
		 * 
		 * @param string $password
		 * @return boolean
		*/
		public function isPasswordMatch($adminID, $password)
		{
			// Using database connection file here
	    	include trim(__DIR__,"model").'/dbConnection.php';

	    	// Fetch hashed password
			$passwordStmt = $conn->prepare("SELECT `password` FROM `admin_accounts` WHERE `ID` = ?");
		    $passwordStmt->bind_param('i', $adminID);
		    $passwordStmt->execute();
		    $passwordQuery =  $passwordStmt->get_result();
		    $passwordStmt->close();
			while ($row = $passwordQuery->fetch_assoc()) {
				$hashedPassword = $row["password"];

				// Verify if current password matches in the database
				if (password_verify($password, $hashedPassword)) {
	                $checkResult = true;
	            } else {
	            	$checkResult = false;
	            }
			}
			return $checkResult;
		}
	}

?>