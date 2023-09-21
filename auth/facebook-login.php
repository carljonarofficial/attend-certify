<?php
	// Require Facebook Config File
	require '../model/social-login-config/facebook-login/config.php';

	// Success Flag
	$successFlag = true;

	// Get Access Token
	try {  
		$accessToken = $fbHelper->getAccessToken();  
	} catch (Facebook\Exceptions\FacebookResponseException $e) {  
		$successFlag = false;
		// When Graph returns an error  
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;  
	} catch (Facebook\Exceptions\FacebookSDKException $e) {  
		$successFlag = false;
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();  
		exit;  
	}  
	 
	try {
		// Get the Facebook\GraphNodes\GraphUser object for the current user.
		$response = $facebookConfig->get('/me?fields=id,name,email,first_name,last_name', $accessToken->getValue());
	} catch (Facebook\Exceptions\FacebookResponseException $e) {
		$successFlag = false;
		// When Graph returns an error
		echo 'ERROR: Graph ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		$successFlag = false;
		// When validation fails or other local issues
		echo 'ERROR: validation fails ' . $e->getMessage();
		exit;
	}
	
	if ($successFlag) {
		// Get Profile Data
		$me = $response->getGraphUser();
	 
		// echo "Full Name: ".$me->getProperty('name')."<br>";
		// echo "Email: ".$me->getProperty('email')."<br>";
		// echo "Facebook ID: <a href='https://www.facebook.com/".$me->getProperty('id')."' target='_blank'>".$me->getProperty('id')."</a>";
		$adminAccount = new AdminAccount();
		$accountExist = $adminAccount->isAccountExists($me->getProperty('email'));
		if ($accountExist) {
			// echo "Existing";
			$adminAccount->loginAccount($me->getProperty('email'), $me->getProperty('name'));
		} else {
			// echo "Not Existing";
			$generatedUsername = substr($me->getProperty('email'), 0, strpos($me->getProperty('email'), '@')).date("YmdHis");
			$addAccount = $adminAccount->addAccount($generatedUsername, $me->getProperty('email'));
			if ($addAccount) {
				$adminAccount->loginAccount($me->getProperty('email'), $me->getProperty('name'));
			} else {
				header("Location: ../login.php");
			}
		}
	}

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
	            session_write_close();
	            header("Location: ../home.php");
		    }
		}
	}
?>