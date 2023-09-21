<?php
	use Phppot\Member;
	if (! empty($_POST["signup-btn"])) {
	    require_once './model/account-member.php';
	    $member = new Member();
	    $registrationResponse = $member->registerMember();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sign up | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>
    <!-- Sign Up Form Styles -->
    <link rel="stylesheet" href="style/login_signup.css">
    <style>
	    .user_card {
	    	height: 690px;
		}
	</style>
</head>
<body>
	<div class="container-fluid h-100">
		<div class="d-flex justify-content-center h-100">
			<!-- Sign up Form -->
			<div style="width: 100%; height: 780px; display: flex; justify-content: center;">
				<div class="user_card">
					<div class="d-flex justify-content-center">
						<div class="brand_logo_container">
							<a href=".">
								<img src="img/logo_circle.svg" class="brand_logo" alt="Logo" onContextMenu="return false;" ondragstart="return false;">
							</a>
							<h3 class="display-5 py-2 text-truncate mt-1" style="font-weight: bold; font-size: 2rem;">Sign up</h3>
						</div>
					</div>
					<div class="d-flex justify-content-center form_container">
						<form name="sign-up" action="" method="post" onsubmit="return signupValidation()">
							<?php
							    if (! empty($registrationResponse["status"])) {
					        ?>
	                		<?php
						        if ($registrationResponse["status"] == "error") {
				            ?>
						    <div class="server-response error-msg"><?php echo $registrationResponse["message"]; ?></div>
		                    <?php
						        } else if ($registrationResponse["status"] == "success") {
				            ?>
		                    <div class="server-response success-msg"><?php echo $registrationResponse["message"]; ?></div>
		                    <?php
						        }
					        ?>
							<?php
						    	}
						    ?>
							<!-- Username Block -->
							<span class="required error" id="username-info"></span>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-user"></i></span>
								</div>
								<input type="text" name="username" id="username" class="form-control input_user" placeholder="Username">
							</div>
							<!-- Email Block -->
							<span class="required error" id="email-info"></span>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-envelope"></i></span>
								</div>
								<input type="text" name="email" id="email" class="form-control input_user" placeholder="Email Address">
							</div>
							<!-- Password Block -->
							<span class="required error" id="signup-password-info"></span>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input type="password" name="signup-password" id="signup-password" class="form-control input_pass" placeholder="Password">
							</div>
							<!-- Confirmed Password Block -->
							<span class="required error" id="confirm-password-info"></span>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input type="password" name="confirm-password" id="confirm-password" class="form-control input_pass" placeholder="Confirm Password">
							</div>
							<!-- Agree Terms and Conditions -->
							<span class="required error" id="agree-conditions-info"></span>
							<div class="input-group mb-2">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="agree-conditions" id="agree-conditions" class="custom-control-input">
									<label class="custom-control-label" for="agree-conditions" style="max-width: 220px;">Check here that you agree with <a href="terms-conditions">Terms and Conditions</a> and you acknowledge that you read <a href="privacy-policy">Privacy Policy</a>.</label>
								</div>
							</div>
							<!-- Sign Up Button -->
							<div class="d-flex justify-content-center mt-3 login_container">
					 			<button type="submit" name="signup-btn" id="signup-btn" value="Sign up" class="btn login_btn"><i class="fas fa-sign-in-alt"></i> Sign up</button>
					   		</div>
						</form>
					</div>
					<!-- Already have an account? Just login. -->
					<div class="mt-4">
						<div class="d-flex justify-content-center links">
							Already have an account? <a href="login.php" class="ml-2">Login</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Login Validation
		function signupValidation() {
			var valid = true;
			$("#username").removeClass("error-field");
			$("#email").removeClass("error-field");
			$("#password").removeClass("error-field");
			$("#confirm-password").removeClass("error-field");
			$("#agree-conditions").removeClass("error-field");

			var UserName = $("#username").val();
			var email = $("#email").val();
			var Password = $('#signup-password').val();
			var ConfirmPassword = $('#confirm-password').val();
			var CheckConditions = $('#agree-conditions').is(":checked");
			var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

			$("#username-info, #email-info, #signup-password-info, #confirm-password-info, #agree-conditions-info").html("").hide();

			if (UserName.trim() == "") {
				$("#username-info").html("* Required.").css("color", "#ee0000").show();
				$("#username").addClass("error-field");
				valid = false;
			}
			if (email == "") {
				$("#email-info").html("* Required.").css("color", "#ee0000").show();
				$("#email").addClass("error-field");
				valid = false;
			} else if (email.trim() == "") {
				$("#email-info").html("Invalid email address.").css("color", "#ee0000").show();
				$("#email").addClass("error-field");
				valid = false;
			} else if (!emailRegex.test(email)) {
				$("#email-info").html("Invalid email address.").css("color", "#ee0000").show();
				$("#email").addClass("error-field");
				valid = false;
			}
			if (Password.trim() == "") {
				$("#signup-password-info").html("* Required.").css("color", "#ee0000").show();
				$("#signup-password").addClass("error-field");
				valid = false;
			}
			if (ConfirmPassword.trim() == "") {
				$("#confirm-password-info").html("* Required.").css("color", "#ee0000").show();
				$("#confirm-password").addClass("error-field");
				valid = false;
			}
			if (Password != ConfirmPassword) {
		        $("#signup-password-info, #confirm-password-info").html("* Both must be same.").css("color", "#ee0000").show();
		        valid = false;
		    } 
		    if (!CheckConditions) {
		    	$("#agree-conditions-info").html("* Required.").css("color", "#ee0000").show();
				$("#agree-conditions").addClass("error-field");
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