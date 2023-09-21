<?php
	// Require HTTP, OAuth Client, and Config Files
	require '../model/social-login-config/google-login/http.php';
	require '../model/social-login-config/google-login/oauth_client.php';
	require '../model/social-login-config/google-login/config.php';

	$client = new oauth_client_class;

	$loginType = 'admin';
	// Check if login type is user
	if (isset($_GET['user'])) {
		$loginType = "user";
	}

	// Set the offline access  only if you need to call an API
	// When the user is not present and the token may expire
	$client->offline = FALSE;

	$client->debug = false;
	$client->debug_http = true;
	$client->redirect_uri = REDIRECT_URL;

	$client->client_id = CLIENT_ID;
	// $application_line = __LINE__;
	$client->client_secret = CLIENT_SECRET;

	$user = '';

	if (strlen($client->client_id == 0) || strlen($client->client_secret) == 0) {
		die("An error has occured. Please try again.");
	}

	// API Permissions
	$client->scope = SCOPE;
	if (($success = $client->Initialize())) {
		if (($success = $client->Process())) {
			if (strlen($client->authorization_error)) {
				$client->error = $client->authorization_error;
				$success = false;
			} else if (strlen($client->access_token)) {
				$success = $client->CallAPI(
					'https://www.googleapis.com/oauth2/v1/userinfo', 'GET', array(), array('FailOnAccessError' => true), $user
				);
			}
		}
		$success = $client->Finalize($success);
	}
	if ($client->exit) {
		exit;
	}
	if ($success) {
		$adminAccount = new AdminAccount();
		$accountExist = $adminAccount->isAccountExists($user->email);
		if ($accountExist) {
			// echo "Existing";
			$adminAccount->loginAccount($user->email, $user->name);
		} else {
			// echo "Not Existing";
			$generatedUsername = substr($user->email, 0, strpos($user->email, '@')).date("YmdHis");
			$addAccount = $adminAccount->addAccount($generatedUsername, $user->email);
			if ($addAccount) {
				$adminAccount->loginAccount($user->email, $user->name);
			} else {
				header("Location: ../login.php");
			}
		}
	}
	exit;

	/**
	 * Account Class
	 */
	class AdminAccount
	{
		/**
		 * Get Admin Account Existence Function
		*/
		public function isAccountExists($email)
		{
			// Include Database Connection File
			include "../dbConnection.php";

			// Account Statemnt
			$accountStmt = $conn->prepare("SELECT * FROM `admin_accounts` WHERE `email` = ? AND `status` = 1");
			$accountStmt->bind_param('s', $email);
		    $accountStmt->execute();
		    $accountResult =  $accountStmt->get_result();
		    $accountStmt->close();
		    $conn->close();

		    // Check if Account Exists
		    if ($accountResult->num_rows > 0) {
		    	$existsFlag = true;
		    } else {
		    	$existsFlag = false;
		    }

		    // Return Exist Flag and Account ID
		    return $existsFlag;
		}

		/**
		 * Add Account Data
		*/
		public function addAccount($username, $email)
		{
			// Include Database Connection File
			include "../dbConnection.php";

			// Add Account Statement
			$hashedPassword = password_hash($username, PASSWORD_DEFAULT);
			$addAccountStmt = $conn->prepare("INSERT INTO `admin_accounts`(`username`, `password`, `email`) VALUES (?,?,?)");
    		$addAccountStmt->bind_param("sss", $username, $hashedPassword, $email);
    		if ($addAccountStmt->execute()) {
    			$addAccountStmt->close();
    			$conn->close();
    			$addAccountFlag = true;
    		} else {
    			$addAccountFlag = false;
    		}

    		// Return Add Account Flag
    		return $addAccountFlag;
		}

		/**
		 * Login Account Data
		*/
		public function loginAccount($email, $fullname)
		{
			// Include Database Connection File
			include "../dbConnection.php";

			// Account Statemnt
			$accountStmt = $conn->prepare("SELECT * FROM `admin_accounts` WHERE `email` = ? AND `status` = 1");
			$accountStmt->bind_param('s', $email);
		    $accountStmt->execute();
		    $accountResult =  $accountStmt->get_result();
		    $accountStmt->close();
		    $conn->close();

		    // Check if Account Exists
		    if ($accountResult->num_rows > 0) {
		    	// Get Account Record
		    	$accountRow = $accountResult->fetch_assoc();
		    	session_start();
	            $_SESSION["ID"] = $accountRow["ID"];
	            $_SESSION["username"] = $accountRow["username"];
	            $_SESSION["fullName"] = $fullname;
	            $_SESSION["email"] = $accountRow["email"];
	            $_SESSION['isSocialAccount'] = true;
				$_SESSION['loginType'] = $loginType;
	            session_write_close();
				if ($loginType == "admin") {
					header("Location: ../home.php");
				} else {
					header("Location: ../certificates?user");
				}
		    }
		}
	}
?>