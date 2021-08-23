<?php
	namespace Phppot;

	/**
	 * 
	 */
	class Member
	{

		/**
		 * To check if username already exists
		 *
		 * @param string $username
		 * @return boolean
		*/
		public function isUsernameExists($username)
		{
			include 'dbConnection.php';
			$sql = "SELECT * FROM `admin_accounts` WHERE `username` = '$username'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$result = true;
			}else{
				$result = false;
			}
			return $result;
		}

		/**
		 * To check if email already exists
		 *
		 * @param string $email
		 * @return boolean
		*/
		public function isEmailExists($email)
		{
			include 'dbConnection.php';
			$sql = "SELECT * FROM `admin_accounts` WHERE `email` = '$email'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$result = true;
			}else{
				$result = false;
			}
			return $result;
		}

		/**
		 * To sign up an user
		 *
		 * @return string[] registration status message
		*/
		public function registerMember()
		{
			$isUsernameExists = $this->isUsernameExists($_POST["username"]);
        	$isEmailExists = $this->isEmailExists($_POST["email"]);
        	if ($isUsernameExists) {
        		$response = array(
	                "status" => "error",
	                "message" => "Username already exists."
	            );
        	}else if ($isEmailExists) {
        		$response = array(
	                "status" => "error",
	                "message" => "Email already exists."
	            );
        	}else{
        		include 'dbConnection.php';
        		if (!empty($_POST["signup-password"])) {
        			// PHP's password_hash is the best choice to use to store passwords
        			$hashedPassword = password_hash($_POST["signup-password"], PASSWORD_DEFAULT);
        		}
        		$username = $_POST["username"];
        		$email = $_POST["email"];
        		$query = "INSERT INTO `admin_accounts`(`username`, `password`, `email`) VALUES ('$username','$hashedPassword','$email')";
        		if ($conn->query($query) === TRUE) {
        			session_start();
		            $_SESSION["sign-up-validation-msg"] = "success";
		            session_write_close();
		            header("Location: login.php");
        		}else{
        			$response = array(
		                "status" => "error",
		                "message" => "Sign up error, please try again."
		            );
        		}
        	}
        	return $response;
		}

		public function getMember($username)
	    {
	    	include 'dbConnection.php';
	        $sql = "SELECT * FROM `admin_accounts` WHERE `username` = '$username'";
	        $result = $conn->query($sql);
			if ($result->num_rows > 0){
				// output data of each row
                while($row = $result->fetch_assoc())
                {
                	$memberRecord[] = $row;
                }
			}
			if (! empty($memberRecord)) {
	            return $memberRecord;
	        }
	    }

	    /**
	     * To login a user
	     *
	     * @return string
	     */
	    public function loginMember()
	    {
	        $memberRecord = $this->getMember($_POST["username"]);
	        $loginPassword = 0;
	        if (! empty($memberRecord)) {
	            if (! empty($_POST["login-password"])) {
	                $password = $_POST["login-password"];
	            }
	            $hashedPassword = $memberRecord[0]["password"];
	            $loginPassword = 0;
	            if (password_verify($password, $hashedPassword)) {
	                $loginPassword = 1;
	            }
	        } else {
	            $loginPassword = 0;
	        }
	        if ($loginPassword == 1) {
	            // login sucess so store the member's username in
	            // the session
	            session_start();
	            $_SESSION["ID"] = $memberRecord[0]["ID"];
	            $_SESSION["username"] = $memberRecord[0]["username"];
	            $_SESSION["email"] = $memberRecord[0]["email"];
	            // $_SESSION["password"] = $memberRecord[0]["password"];
	            // $_SESSION["remember"];
	            session_write_close();
	            $url = "./home.php";
	            header("Location: $url");
	        } else if ($loginPassword == 0) {
	            $loginStatus = "Invalid username or password.";
	            return $loginStatus;
	        }
	    }
	}
?>