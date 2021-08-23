<?php
	use Phppot\Member;
	if (! empty($_POST["login-btn"])) {
	    require_once __DIR__ . '/model/member.php';
	    $member = new Member();
	    $loginResult = $member->loginMember();
	}
	$signup_msg = "";
	session_start();
	if (isset($_SESSION["sign-up-validation-msg"])) {
        $signup_msg = $_SESSION["sign-up-validation-msg"];
    }
    unset($_SESSION["sign-up-validation-msg"]);
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
    <style>
	    .user_card {
	    	height: 490px;
		}
	</style>
</head>
<body style="background-image: url('style/img/background_add.jpeg')">
	<div class="container-fluid h-100">
		<div class="d-flex justify-content-center h-100">
			<!-- Login Form -->
			<div class="user_card">
				<div class="d-flex justify-content-center">
					<div class="brand_logo_container">
						<img src="img/logo.svg" class="brand_logo" alt="Logo" onContextMenu="return false;" ondragstart="return false;">
						<h3 class="display-5 py-2 text-truncate mt-1">Login</h3>
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
						<?php if(!empty($loginResult)){?>
							<div class="error-msg"><?php echo $loginResult;?></div>
						<?php }?>
						<!-- Username Block -->
						<span class="required error" id="username-info"></span>
						<div class="input-group mb-3">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-user"></i></span>
							</div>
							<input type="text" name="username" id="username" class="form-control input_user" placeholder="Username">
						</div>
						<!-- Password Block -->
						<span class="required error" id="login-password-info"></span>
						<div class="input-group mb-3">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-key"></i></span>
							</div>
							<input type="password" name="login-password" id="login-password" class="form-control input_pass" placeholder="Password">
						</div>
						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" name="remember" class="custom-control-input" id="customControlInline">
								<label class="custom-control-label" for="customControlInline">Remember me</label>
							</div>
						</div>
						<div class="d-flex justify-content-center mt-3 login_container">
				 			<button type="submit" name="login-btn" id="login-btn" value="Login" class="btn login_btn"><i class="fas fa-sign-in-alt"></i> Login</button>
				   		</div>
					</form>
				</div>
				<div class="mt-4">
					<div class="d-flex justify-content-center links">
						Don't have an account? <a href="signup.php" class="ml-2">Sign Up</a>
					</div>
					<div class="d-flex justify-content-center links">
						<a href="#">Forgot your password?</a>
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
	</script>
</body>
</html>