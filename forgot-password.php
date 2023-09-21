<!DOCTYPE html>
<html>
<head>
	<title>Forgot Password | Attend and Certify</title>
	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
     <!-- Modified Tab Panel Styles -->
    <style>
        .settings-tab-list {
            font-size: 1.5rem;
            font-weight: 600;
            text-decoration: none;
        }
        .nav-tabs .nav-link {
            color: #9797a5;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom: 4px solid #007bff;
        }
        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            color: #fff;
            text-align: center;
            border-radius: 10px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            right: 50%;
            top: 100px;
            font-size: 17px;
        }
        #snackbar.show {
            visibility: visible;
        }
    </style>
</head>
<body class="d-flex flex-column" >
	<!-- Navbar -->
	<div class="container-fluid mb-5">
		<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
			<!-- Logo -->
            <a class="navbar-brand" href="." style="margin-right: 0.25rem;">
                <img src="img/logo_circle.svg" alt="Logo" width="50" onContextMenu="return false;"  ondragstart="return false;">
            </a>
            <!-- Button for Collapsible Navbar -->
            <button id="navbar-button" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span id="navbar-button-icon" class="navbar-toggler-icon"></span>
            </button>
            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
            	<!-- Logo Text -->
            	<div class="justify-content-center text-dark mt-2">
                    <img src="img/logo_text.svg" alt="ATTEND and CERTIFY" height="32.5" onContextMenu="return false;" ondragstart="return false;">
                </div>
            </div>
		</nav>
	</div>
	<div class="main-body container-fluid flex-grow-1 mt-5">
        <!-- The loading modal -->
        <div  id="loadingModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-sm " >
                <div class="modal-content border-form-override p-3 text-center" id="myModal" style="max-height: 190px;">
                    <div class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="loadingio-spinner-rolling-lefr816gl">
                                <div class="ldio-eeg8hrr2lac">
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 disable-select" style="font-size: 1.25rem;" unselectable="on"><strong>Please Wait</strong></p>
                </div>
            </div>
        </div>
        <!-- The setting status snackbar -->
        <div id="snackbar"></div>
		<!-- Main Body -->
        <div class="container-fluid px-2 pt-2">
        	<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold header-title" style="font-size: 2.25rem;">FORGOT PASSWORD</h1>
                <h2 class="pl-3 font-weight-normal">Reset your password if you forgot one.</h2>
            </div>
            <div class="container shadow-sm p-3 my-3 mt-4 border-form-override">
                <!-- Tabs Between Forgot Password -->
                <ul class="nav nav-tabs" id="forgotPasswordTab" role="tablist">
                    <li class="nav-item settings-tab-list">
                        <a style="pointer-events: none;" class="nav-link active" id="searchEmailTab" data-toggle="tab" href="#search-email" role="tab" aria-controls="search-email" aria-selected="true"><i class="fas fa-search"></i> Find Your Email</a>
                    </li>
                    <li class="nav-item settings-tab-list">
                        <a style="pointer-events: none;" class="nav-link" id="enterCodeTab" data-toggle="tab" href="#enter-code" role="tab" aria-controls="enter-code" aria-selected="true"><i class="fas fa-key"></i> Verification Code</a>
                    </li>
                    <li class="nav-item settings-tab-list">
                        <a style="pointer-events: none;" class="nav-link" id="enterNewPasswordTab" data-toggle="tab" href="#enter-new-password" role="tab" aria-controls="enter-new-password" aria-selected="true"><i class="fas fa-lock"></i> New Password</a>
                    </li>
                    <li class="nav-item settings-tab-list">
                        <a style="pointer-events: none;" class="nav-link" id="doneNewPasswordTab" data-toggle="tab" href="#done-new-password" role="tab" aria-controls="done-new-password" aria-selected="true"><i class="fas fa-check"></i> Done</a>
                    </li>
                </ul>
                <!-- Tab Content Forgot Password Progress -->
                <div class="tab-content p-3" id="searchEmailTabContent">
                    <!-- Search Email Pane -->
                    <div class="tab-pane fade show active" id="search-email" role="tabpanel" aria-labelledby="searchEmailTab">
                        <p style="font-size: 1.125rem;"><i class="fas fa-info-circle"></i> Please enter your email below to reset your password.</p>
                        <!-- Account Email Form -->
                        <form  method="post" id="accountEmailForm">
                            <!-- Email Form Group -->
                            <div class="form-group" id="emailAdminField">
                                <div class="form-inline">
                                    <!-- Email input -->
                                    <input type="text" class="form-control col-sm-4 ml-2" id="emailAdminInput" name="emailAdminInput" placeholder="Email Address">
                                </div>
                                <div class="error-field ml-2" id="currentEmail-Error" style="color: #ee0000;"></div>
                            </div>
                            <!-- Search or Cancel Forgot Password -->
                            <div class="form-group" id="savecancelAccountEmail">
                                <!-- Cancel Forgot Password Button -->
                                <button type="button" id="cancelForgotPassBtn" class="btn btn-secondary ml-2 mt-2" onclick="location.href='./login.php';"><i class="fa fa-times"></i> Cancel</button>
                                <!-- Search Account Button -->
                                <button type="submit" id="goVerificationCode" name="goVerificationCode" class="btn btn-primary ml-2 mt-2"><i class="fas fa-search"></i> Search</button>
                            </div>
                        </form>
                    </div>
                    <!-- Verification Pane -->
                    <div class="tab-pane fade" id="enter-code" role="tabpanel" aria-labelledby="enterCodeTab">
                        <p style="font-size: 1.25rem;"><i class="fas fa-info-circle"></i> Please check your email for a message with your code. Your Code is 12 characters long.</p>
                        <!-- Verification Form -->
                        <form method="post" id="verificationForm">
                            <!-- Code Form Group -->
                            <div class="form-group">
                                <div class="form-inline">
                                    <!-- Code Input -->
                                    <input type="text" class="form-control col-sm-4 ml-2" id="verificationCodeInput" name="verificationCodeInput" placeholder="Enter Code">
                                </div>
                                <div class="error-field ml-2" id="codeInput-Error" style="color: #ee0000;"></div>
                                <div class="ml-2 mt-2">Didn't get a code? <a href="#" id="resendCode">Resend Code</a></div>
                            </div>
                            <!-- Continue or Back -->
                            <div class="form-group" id="backContinueVerification">
                                <!-- Back to Search Email -->
                                <button type="button" id="backToSearchEmailBtn" class="btn btn-secondary ml-2 mt-2"><i class="fas fa-chevron-circle-left"></i> Back</button>
                                <!-- Continue to New Password Button -->
                                <button type="submit" id="continueToNewPassword" name="continueToNewPassword" class="btn btn-primary ml-2 mt-2"><i class="fas fa-chevron-circle-right"></i> Continue</button>
                            </div>
                        </form>
                    </div>
                    <!-- New Password Pane -->
                    <div class="tab-pane fade" id="enter-new-password" role="tabpanel" aria-labelledby="enterNewPasswordTab">
                        <p style="font-size: 1.25rem;"><i class="fas fa-info-circle"></i> Please enter your New Password.</p>
                        <div class="error-field ml-2" id="bothPassword-Error" style="color: #ee0000;"></div>
                        <!-- New Password Form -->
                        <form method="post" id="accountPasswordForm">
                            <!-- New Password Form Group -->
                            <div class="form-group">
                                <label for="newPassword" class="label-add-edit-event mb-0"><i class="fas fa-lock"></i> New Password:</label>
                                <!-- New Password input -->
                                <input type="password" class="form-control col-sm-4 ml-2 mt-2" id="newPasswordInput" name="newPasswordInput">   
                                <div class="error-field ml-2" id="newPassword-Error" style="color: #ee0000;"></div>
                            </div>
                            <!-- Re-enter New Password Form Group -->
                                <div class="form-group">
                                    <label for="reenterNewPassword" class="label-add-edit-event mb-0"><i class="fas fa-lock"></i> Re-enter New Password:</label>
                                    <!-- Re-enter New Password input -->
                                    <input type="password" class="form-control col-sm-4 ml-2 mt-2" id="reenterNewPasswordInput" name="reenterNewPasswordInput">
                                    <div class="error-field ml-2" id="reenterPassword-Error" style="color: #ee0000;"></div>
                                </div>
                            <!-- Save or Back New Password -->
                            <div class="form-group" id="savebackPassword">
                                <!-- Back to Verification Button -->
                                <button type="button" id="backToVerificationBtn" class="btn btn-secondary ml-2 mt-2"><i class="fas fa-chevron-circle-left"></i> Back</button>
                                <!-- Save Password Button -->
                                <button type="submit" id="savePassword" name="savePassword" class="btn btn-success ml-2 mt-2"><i class="fas fa-save"></i> Save</button>
                            </div>
                        </form>
                    </div>
                    <!-- Done Pane -->
                    <div class="tab-pane fade" id="done-new-password" role="tabpanel" aria-labelledby="doneNewPasswordTab">
                        <p style="font-size: 1.25rem;"><i class="fas fa-info-circle"></i> Your password has been reset. You can now login your account.</p>
                        <!-- Proceed to Login Account -->
                        <div class="form-group" id="savebackPassword">
                            <!-- Proceed to Login Button -->
                            <button type="button" class="btn btn-primary ml-2 mt-2" onclick="location.href='./login.php';"><i class="fas fa-sign-in-alt"></i> Proceed to Login</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <script>
        // Get Certificate Status Snackbar
        var statusSnackBar = document.getElementById("snackbar");

        // Temporary Store Admin ID
        var adminIDTemp;

        // Search Account Upon Clicking Search Button
        $("#accountEmailForm").submit(function(event) {
            event.preventDefault();
            if ($("#emailAdminInput").val().trim() == "") {
                $("#currentEmail-Error").html("* Email must not be empty.").show();
                $("#currentEmail-Error").fadeOut(2000);
            } else {
                if (emailValidationCustom($("#emailAdminInput").val()) == true) {
                    $.ajax({
                        url: './model/forgot-member',
                        method:"POST",
                        data: {
                            forgotAction: 'searchAccount',
                            emailSearch: $("#emailAdminInput").val()
                        },
                        dataType:"json",
                        beforeSend: function() {
                            $("#loadingModal").modal('show');
                        },
                        success: function(data) {
                            $("#loadingModal").modal('hide');
                            if (data.emailStatus == "existing") {
                                // Assign temporary Admin ID Value
                                adminIDTemp = data.adminID;
                                // Proceed to Verification
                                $("#verificationCodeInput").val("");
                                $("#enterCodeTab").tab('show');
                            } else if (data.emailStatus == "notExisting") {
                                $("#currentEmail-Error").html("* Email does not exist.").show();
                                $("#currentEmail-Error").fadeOut(2000);
                            } else {
                                $("#currentEmail-Error").html("* Email Error.").show();
                                $("#currentEmail-Error").fadeOut(2000);
                            }
                        }
                    })
                    .fail(function() {
                        $("#loadingModal").modal('hide');
                        statusSnackBar.style.backgroundColor = "#d9534f";
                        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Error';
                        displayStatusSnackBar();
                    });
                    
                } else {
                    $("#currentEmail-Error").html("* Invalid email address.").show();
                    $("#currentEmail-Error").fadeOut(2000);
                }

            }
        });

        // Go Back to Search Email
        $("#backToSearchEmailBtn").click(function() {
            $("#searchEmailTab").tab('show');
        });

        $("#resendCode").click(function(event) {
            event.preventDefault();
            $.ajax({
                url: './model/forgot-member',
                method:"POST",
                data: {
                    forgotAction: 'resendCode',
                    emailSearch: $("#emailAdminInput").val()
                },
                dataType:"json",
                beforeSend: function() {
                    $("#loadingModal").modal('show');
                },
                success: function(data) {
                    $("#loadingModal").modal('hide');
                    if (data.resendStatus == "success") {
                        statusSnackBar.style.backgroundColor = "#5cb85c";
                        statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Your code has been resend successfully';
                        displayStatusSnackBar();
                    } else {
                        statusSnackBar.style.backgroundColor = "#d9534f";
                        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Your code failed to resend';
                        displayStatusSnackBar();
                    }
                }
            })
            .fail(function() {
                $("#loadingModal").modal('hide');
                statusSnackBar.style.backgroundColor = "#d9534f";
                statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Error';
                displayStatusSnackBar();
            });
        });

        // Verify Code
        $("#verificationForm").submit(function(event) {
            event.preventDefault();
            if ($("#verificationCodeInput").val().trim() == "") {
                $("#codeInput-Error").html("* Code must not be empty.").show();
                $("#codeInput-Error").fadeOut(2000);
            } else {
                $.ajax({
                    url: './model/forgot-member',
                    method:"POST",
                    data: {
                        forgotAction: 'verifyCode',
                        verificationCode: $("#verificationCodeInput").val()
                    },
                    dataType:"json",
                    beforeSend: function() {
                        $("#loadingModal").modal('show');
                    },
                    success: function(data) {
                        $("#loadingModal").modal('hide');
                        if (data.codeStatus == "valid") {
                            // Proceed to New Password
                            $("#verificationCodeInput").val("");
                            $("#enterNewPasswordTab").tab('show');
                        } else {
                            $("#codeInput-Error").html("* Invalid Code.").show();
                            $("#codeInput-Error").fadeOut(2000);
                        }
                    }
                })
                .fail(function() {
                    $("#loadingModal").modal('hide');
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Error';
                    displayStatusSnackBar();
                });
                
            }
        });

        // Go Back to Verification
        $("#backToVerificationBtn").click(function() {
            $("#enterCodeTab").tab('show');
        });

        // Save New Password
        $("#accountPasswordForm").submit(function(event) {
            event.preventDefault();
            if (passwordValidationCustom() == true) {
                $.ajax({
                    url: './model/forgot-member',
                    method:"POST",
                    data: {
                        forgotAction: 'savePassword',
                        adminID: adminIDTemp,
                        newPassword: $("#newPasswordInput").val()
                    },
                    dataType:"json",
                    beforeSend: function() {
                        $("#loadingModal").modal('show');
                    },
                    success: function(data) {
                        $("#loadingModal").modal('hide');
                        if (data.resetPasswordStatus == "success") {
                            // Proceed to Done Reset
                            $("#doneNewPasswordTab").tab('show');
                        } else {
                            statusSnackBar.style.backgroundColor = "#d9534f";
                            statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Password Reset Failed';
                            displayStatusSnackBar();
                        }
                    }
                })
                .fail(function() {
                    $("#loadingModal").modal('hide');
                    statusSnackBar.style.backgroundColor = "#d9534f";
                    statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Password Reset Error';
                    displayStatusSnackBar();
                });
            }
        });

        // Custom Password Validation
        function passwordValidationCustom(){
            var valid = true;

            // New Password Validation
            if ($("#newPasswordInput").val().trim() == "") {
                $("#newPassword-Error").html("* New Password must not be empty.").show();
                $("#newPassword-Error").fadeOut(2000);
                valid = false;
            }

            // Re-enter New Password Validation
            if ($("#reenterNewPasswordInput").val().trim() == "") {
                $("#reenterPassword-Error").html("* Re-enter New Password must not be empty.").show();
                $("#reenterPassword-Error").fadeOut(2000);
                valid = false;
            }

            // Validate if New and Re-enter New Passwords are matched
            if ($("#newPasswordInput").val() != $("#reenterNewPasswordInput").val()) {
                $("#bothPassword-Error").html("* Both passwords must be same.").show();
                $("#bothPassword-Error").fadeOut(2000);
                valid = false;
            }

            return valid;
        }

        // Custom Email Validation
        function emailValidationCustom(emailInput) {
            var valid = true;

            // Email Regex
            var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

            if (!emailRegex.test(emailInput)) {
                valid = false;
            }
            return valid;
        }

        // Show status snackback
        function displayStatusSnackBar() {
            // Add the "show" class to DIV
            statusSnackBar.className = "show";

            // After 2 seconds, remove the show class from DIV
            setTimeout(function(){ 
                statusSnackBar.className = statusSnackBar.className.replace("show", "");
                statusSnackBar.style.backgroundColor = "";
                statusSnackBar.innerHTML = '';
            }, 2000);
        }
    </script>
</body>
</html>