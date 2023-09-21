<?php
	use Phppot\Member;
	if (! empty($_POST["login-btn"])) {
	    require_once __DIR__ . '/model/account-member.php';
	    $member = new Member();
	    $loginResult = $member->loginMember();
	}
	$signup_msg = "";
	$deleteAccountMsg = "";
	session_start();
	if (isset($_SESSION["sign-up-validation-msg"])) {
        $signup_msg = $_SESSION["sign-up-validation-msg"];
    }
    if (isset($_SESSION['delete-account-validation-msg'])) {
    	$deleteAccountMsg = $_SESSION['delete-account-validation-msg'];
    }
    unset($_SESSION["sign-up-validation-msg"]);
    unset($_SESSION['delete-account-validation-msg']);
    session_write_close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login | Attend and Certify</title>
	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
    <!-- Login Form Styles -->
    <link rel="stylesheet" href="style/login_signup.css">
    <!-- Modified Styles -->
    <style>
	    .user_card {
	    	height: 620px;
		}
	</style>
</head>
<body>
	<div class="container-fluid h-100">
        <!-- Main Body -->
		<div class="d-flex justify-content-center h-100">
			<!-- Login Form -->
			<div style="width: 100%; height: 710px; display: flex; justify-content: center;">
				<div class="user_card">
					<div class="d-flex justify-content-center">
						<div class="brand_logo_container">
							<a href=".">
								<img src="img/logo_circle.svg" class="brand_logo" alt="Logo" onContextMenu="return false;" ondragstart="return false;">
							</a>
							<h3 class="display-5 py-2 text-truncate mt-1" style="font-weight: bold; font-size: 2rem;">Login</h3>
						</div>
					</div>
					<div class="d-flex justify-content-center form_container">
						<form name="login" action="" method="post" onsubmit="return loginValidation()">
							<?php
						        if (!empty($signup_msg)) {
				            ?>
		                    <div class="server-response success-msg">You have registered successfully.</div>
		                    <?php
						        }
					        ?>
							<?php if (!empty($loginResult)){?>
								<div class="error-msg"><?php echo $loginResult;?></div>
							<?php }?>
							<?php if (!empty($deleteAccountMsg)) { ?>
								<div class="server-response error-msg">Your account has been deleted.</div>
							<?php }?>
							<!-- Username Block -->
							<span class="required error" id="username-info"></span>
							<div class="input-group mb-3">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-user"></i></span>
								</div>
								<input type="text" name="username" id="username" class="form-control input_user" placeholder="Username" value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>">
							</div>
							<!-- Password Block -->
							<span class="required error" id="login-password-info"></span>
							<div class="input-group mb-3">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input type="password" name="login-password" id="login-password" class="form-control input_pass" placeholder="Password" value="<?php if(isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>" >
							</div>
							<!-- Type Block -->
							<select class="form-control mb-3" name="login-type" id="login-type">
								<option value="admin">Admin</option>
								<option value="user">User</option>
							</select>
							<!-- Remember Me Block -->
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="rememberMe" class="custom-control-input" id="rememberMe" <?php if(isset($_COOKIE["username"])) { echo "checked"; } ?>>
									<label class="custom-control-label" for="rememberMe">Remember me</label>
								</div>
							</div>
							<div class="mt-3 login_container">
					 			<button type="submit" name="login-btn" id="login-btn" value="Login" class="btn login_btn"><i class="fas fa-sign-in-alt"></i> Log In</button>
					 			<p class="text-center my-2">OR</p>
					 			<button type="button" id="google-login-btn" class="social-login-container">
					 				<div class="social-login-btn google-btn">
						 				<div class="social-icon-wrapper">
						 					<img class="social-icon" src="img/icon_google.svg">
						 				</div>
						 				<p class="social-login-btn-text"><b>Log in with Google</b></p>
						 			</div>
					 			</button>
					 			<button type="button" id="fb-login-btn" class="social-login-container">
					 				<div class="social-login-btn facebook-btn">
						 				<div class="social-icon-wrapper">
						 					<img class="social-icon" src="img/icon_fb.svg">
						 				</div>
						 				<p class="social-login-btn-text"><b>Log in with Facebook</b></p>
						 			</div>
					 			</button>
					   		</div>
						</form>
					</div>
					<div class="mt-4">
						<div class="d-flex justify-content-center links">
							Don't have an account? <a href="signup.php" class="ml-2">Sign Up</a>
						</div>
						<div class="d-flex justify-content-center links">
							<a href="forgot-password.php">Forgot your password?</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Login Validation
		function loginValidation() {
			var valid = true;
			$("#username").removeClass("error-field");
			$("#password").removeClass("error-field");

			var UserName = $("#username").val();
			var Password = $('#login-password').val();

			$("#username-info").html("").hide();

			if (UserName.trim() == "") {
				$("#username-info").html("* Required.").css("color", "#ee0000").show();
				$("#username").addClass("error-field");
				valid = false;
			}
			if (Password.trim() == "") {
				$("#login-password-info").html("* Required.").css("color", "#ee0000").show();
				$("#login-password").addClass("error-field");
				valid = false;
			}
			if (valid == false) {
				$('.error-field').first().focus();
				valid = false;
			}
			return valid;
		}

		// Google Login Button
		$("#google-login-btn").click(function() {
			var loginType = "";
			if ($("#login-type").val() == "User") {
				loginType = "?user";
			}
			location.href='./auth/google-login.php' + loginType;
		});

		// Facebook Login Button
		$("#fb-login-btn").click(function() {
			location.href='./auth/facebook-confirm.php';
		});		
	</script>
</body>
</html>