<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Check if is logged in to Google Account or Facebook Account
    $isSocialLoggedin = false;
    if (isset($_SESSION["isSocialAccount"])) {
    	$isSocialLoggedin = true;
    } 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Settings | Attend and Certify</title>

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
    		color: #000;
    	}
    	.nav-tabs .nav-link.active {
    		color: #007bff;
    		border-bottom: 4px solid #007bff;
    	}
    	.nav-tabs .nav-link.active:hover {
    		border-bottom: 4px solid #007bff;
    	}
    	.nav-tabs .nav-link:hover {
    		border-bottom: 4px solid #8ec3fd;
    	}
        .form-control:disabled, .form-control[readonly] {
            background-color: #fff;
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
	<?php 
		 // Initialize Active Page for Navbar Highlight
        $activePage = "settings";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
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
                <h1 class="font-weight-bold header-title">SETTINGS</h1>
                <h2 class="pl-3 font-weight-normal">Change the settings pertaining to your account.</h2>
            </div>
	        <div class="container shadow-sm p-3 my-3 mt-4 border-form-override">
	        	<!-- Toogle Between Account Info and Change Password -->
	        	<ul class="nav nav-tabs" id="settingsTab" role="tablist">
	        		<li class="nav-item settings-tab-list">
	        			<a class="nav-link active" id="accountInfoTab" data-toggle="tab" href="#account-information" role="tab" aria-controls="account-information" aria-selected="true"><i class="fas fa-info-circle"></i> Account Information</a>
	        		</li>
	        		<li class="nav-item settings-tab-list">
	        			<a class="nav-link" id="changePassTab" data-toggle="tab" href="#change-password" role="tab" aria-controls="change-password" aria-selected="false"><i class="fas fa-key"></i> Change Password</a>
	        		</li>
				</ul>
				<!-- Tab Content Between Account Info and Change Password -->
				<div class="tab-content p-3" id="settingsTabContent">
					<!-- Account Info Pane -->
					<div class="tab-pane fade show active" id="account-information" role="tabpanel" aria-labelledby="accountInfoTab">
						<!-- Account Username Form -->
						<form method="post" id="accountUsernameForm">
							<!-- Username Form Group -->
							<div class="form-group" id="usernameField">
								<label for="usernameAdmin" class="label-add-edit-event mb-0"><i class="fas fa-user"></i> Username:</label>
								<div class="form-inline">
									<!-- Username input -->
									<input type="text" class="form-control col-sm-4 ml-2 mt-2" id="usernameAdminInput" name="usernameAdminInput" value="<?php echo $username;?>" disabled>
									<!-- Edit Username Button -->
									<button type="button" id="editUsernameAdminBtn" class="btn btn-primary ml-2 mt-2"><i class="fas fa-edit"></i> Edit</button>
								</div>
								<div class="error-field ml-2" id="currentUsername-Error" style="color: #ee0000;"></div>
							</div>
							<!-- Save or Cancel Account Username Change -->
							<div class="form-group" id="savecancelAccountUsername" style="display: none;">
								<!-- Save Account Info Button -->
								<button type="submit" id="saveAccountUsername" name="saveAccountUsername" class="btn btn-success ml-2 mt-2"><i class="fas fa-save"></i> Save</button>
								<!-- Cancel Account Info Change Button -->
								<button type="button" id="cancelUsernameChangeBtn" class="btn btn-secondary ml-2 mt-2"><i class="fa fa-times"></i> Cancel</button>
							</div>
						</form>
						<?php
							if (!$isSocialLoggedin) { ?>
								<!-- Account Email Form -->
								<form method="post" id="accountEmailForm">
									<!-- Email Form Group -->
									<div class="form-group" id="emailAdminField">
										<label for="emailAdmin" class="label-add-edit-event mb-0"><i class="fas fa-envelope"></i> Email:</label>
										<div class="form-inline">
											<!-- Email input -->
											<input type="text" class="form-control col-sm-4 ml-2 mt-2" id="emailAdminInput" name="emailAdminInput" value="<?php echo $adminEmail;?>" disabled>
											<!-- Edit Email Button -->
											<button type="button" id="editEmailAdminBtn" class="btn btn-primary ml-2 mt-2"><i class="fas fa-edit"></i> Edit</button>
										</div>
										<div class="error-field ml-2" id="currentEmail-Error" style="color: #ee0000;"></div>
									</div>
									<!-- Save or Cancel Account Email Change -->
									<div class="form-group" id="savecancelAccountEmail" style="display: none;">
										<!-- Save Account Info Button -->
										<button type="submit" id="saveAccountEmail" name="saveAccountEmail" class="btn btn-success ml-2 mt-2"><i class="fas fa-save"></i> Save</button>
										<!-- Cancel Account Info Change Button -->
										<button type="button" id="cancelEmailChangeBtn" class="btn btn-secondary ml-2 mt-2"><i class="fa fa-times"></i> Cancel</button>
									</div>
								</form>
							<?php }
						?>
						<!-- Delete Account Button Group -->
						<div class="form-group" id="deleteAdminAccountInput">
							<label for="deleteAdminAccountBtn" class="label-add-edit-event mb-0"><i class="fas fa-user-slash"></i> Delete Account:</label>
							<div class="form-inline">
								<!-- Delete Account Button -->
								<button type="button" id="deleteAdminAccountBtn" class="btn btn-danger ml-2 mt-2" data-toggle="modal" data-target="#deleteAccountModal"><i class="fa fa-trash"></i> Delete</button>
							</div>
						</div>
						<!-- Delete Account Modal -->
						<div class="modal" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModal" aria-hidden="true" data-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h4 class="modal-title text-light" id="exampleModalDelete"><i class="fas fa-exclamation-triangle"></i> Are you sure to delete your account?</h5>
                                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>After deleting your account. You will not able to retrieve this, please be careful.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="post" id="deleteAccountModalForm">
                                            <button type="submit" name="deleteYesAccountBtn" id="deleteYesAccountBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Yes</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-arrow-circle-left"></i> No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
					<!-- Change Password Pane -->
					<div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="changePassTab">
						<!-- Change Password Form -->
						<form method="post" id="accountPasswordForm">
							<!-- Edit Password Button Group -->
							<div class="form-group" id="changePasswordBtnGroup">
								<label for="changePasswordBtn" class="label-add-edit-event mb-0"><i class="fas fa-lock"></i> Change Password:</label>
								<div class="form-inline">
									<!-- Edit Password Button-->
								<button type="button" id="changePasswordBtn" class="btn btn-primary ml-2 mt-2"><i class="fas fa-edit"></i> Edit</button>	
								</div>
							</div>
							<!-- Change Password Fields -->
							<div id="changePasswordFields" style="display: none;">
								<!-- Current Username Form Group -->
								<div class="form-group">
									<label for="currentPassword" class="label-add-edit-event mb-0"><i class="fas fa-lock"></i> Current Password:</label>
									<!-- Current Password input -->
									<input type="password" class="form-control col-sm-4 ml-2 mt-2" id="currentPasswordInput" name="currentPasswordInput">	
									<div class="error-field ml-2" id="currentPassword-Error" style="color: #ee0000;"></div>
								</div>
                                <div class="error-field ml-2" id="bothPassword-Error" style="color: #ee0000;"></div>
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
								<!-- Save or Cancel Password Change -->
								<div class="form-group" id="savecancelPassword">
									<!-- Save Password Button -->
									<button type="submit" id="savePassword" name="savePassword" class="btn btn-success ml-2 mt-2"><i class="fas fa-save"></i> Save</button>
									<!-- Cancel Password Change Button -->
									<button type="button" id="cancelPasswordChangeBtn" class="btn btn-secondary ml-2 mt-2"><i class="fa fa-times"></i> Cancel</button>
								</div>
							</div>
						</form>
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

    	// Current Username and Email
    	var currentUsername = $("#usernameAdminInput").val();
    	var currentEmail = $("#emailAdminInput").val();

    	// Toggle Between Two Tabs
    	// Account Info Tab Button
    	$("#accountInfoTab").click(function() {
    		$("#usernameAdminInput").val(currentUsername).prop("disabled", true);
    		$("#usernameField").show();
    		$("#editUsernameAdminBtn").show();
    		$("#emailAdminInput").val(currentEmail).prop("disabled", true);
    		$("#emailAdminField").show();
    		$("#editEmailAdminBtn").show();
    		$("#deleteAdminAccountInput").show();
    		$("#savecancelAccountUsername").hide();
    		$("#savecancelAccountEmail").hide();
    	});

    	// Change Password Tab
    	$("#changePassTab").click(function() {
    		$("#changePasswordBtnGroup").show();
    		$("#changePasswordFields").hide();
    		$("#currentPasswordInput").val("");
    		$("#newPasswordInput").val("");
    		$("#currentPasswordInput").val("");
    		$("#reenterNewPasswordInput").val("");
    	});

    	// Edit Username
    	$("#editUsernameAdminBtn").click(function() {
    		$("#usernameAdminInput").prop("disabled", false);
    		$("#editUsernameAdminBtn").hide();
    		$("#emailAdminField").hide();
    		$("#deleteAdminAccountInput").hide();
    		$("#savecancelAccountUsername").show();
    	});

    	// Delete Account
    	$("#deleteAccountModalForm").submit(function(event) {
    		event.preventDefault();
    		$.ajax({
    			url: './model/settings-member',
				method:"POST",
				data: {
					saveSettingsAction: 'deleteAccount'
				},
	            dataType:"json",
	            beforeSend: function() {
					$("#loadingModal").modal('show');
				},
				success: function(data) {
					$("#loadingModal").modal('hide');
					if (data.deleteStatus == "failed") {
						statusSnackBar.style.backgroundColor = "#d9534f";
				        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Delete Account Failed';
			            displayStatusSnackBar();
					} else {
						location.replace("./login");
					}
				}
    		})
    		.fail(function() {
    			$("#loadingModal").modal('hide');
				statusSnackBar.style.backgroundColor = "#d9534f";
		        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Delete Account Error';
	            displayStatusSnackBar();
    		});
    		
    	});

    	// Save Username Change
    	$("#accountUsernameForm").submit(function(event) {
			event.preventDefault();
			if ($("#usernameAdminInput").val().trim() == "") {
				$("#currentUsername-Error").html("* Username must not be empty.").show();
				$("#currentUsername-Error").fadeOut(2000);
			} else {
				if ($("#usernameAdminInput").val() == currentUsername) {
					$("#currentUsername-Error").html("* Please enter new Username.").show();
					$("#currentUsername-Error").fadeOut(2000);
				} else {
					$.ajax({
						url: './model/settings-member',
						method:"POST",
						data: {
							saveSettingsAction: 'saveUsername',
							newUsername: $("#usernameAdminInput").val()
						},
			            dataType:"json",
			            beforeSend: function() {
							$("#loadingModal").modal('show');
						},
						success: function(data) {
							$("#loadingModal").modal('hide');
							if (data.usernameStatus == "existing") {
								$("#currentUsername-Error").html("* Username already exists.").show();
								$("#currentUsername-Error").fadeOut(2000);
							} else if (data.usernameStatus == "failed") {
								statusSnackBar.style.backgroundColor = "#d9534f";
						        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Username Change Failed';
					            displayStatusSnackBar();
							} else {
								$("#currentLoggedInUsername").html($("#usernameAdminInput").val());
								currentUsername = $("#usernameAdminInput").val();
								$("#usernameAdminInput").prop("disabled", true);
					    		$("#editUsernameAdminBtn").show();
					    		$("#emailAdminField").show();
					    		$("#deleteAdminAccountInput").show();
					    		$("#savecancelAccountUsername").hide();
								statusSnackBar.style.backgroundColor = "#5cb85c";
					            statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Username Change Successfully';
					            displayStatusSnackBar();
							}
						}
					})
					.fail(function() {
						$("#loadingModal").modal('hide');
						statusSnackBar.style.backgroundColor = "#d9534f";
				        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Username Change Error';
			            displayStatusSnackBar();
					});
				}
				
			}
		});

    	// Cancel Edit Username
    	$("#cancelUsernameChangeBtn").click(function() {
    		$("#usernameAdminInput").val(currentUsername).prop("disabled", true);
    		$("#editUsernameAdminBtn").show();
    		$("#emailAdminField").show();
    		$("#deleteAdminAccountInput").show();
    		$("#savecancelAccountUsername").hide();
    	});

    	// Edit Email
    	$("#editEmailAdminBtn").click(function() {
    		$("#usernameField").hide();
    		$("#editEmailAdminBtn").hide();
    		$("#editUsernameAdminBtn").hide();
    		$("#emailAdminInput").prop("disabled", false);
    		$("#deleteAdminAccountInput").hide();
    		$("#savecancelAccountEmail").show();
    	});

    	// Save Email Change
    	<?php
    		if (!$isSocialLoggedin) { ?>
    			$("#accountEmailForm").submit(function(event) {
	    		event.preventDefault();
	    		if ($("#emailAdminInput").val().trim() == "") {
					$("#currentEmail-Error").html("* Email must not be empty.").show();
					$("#currentEmail-Error").fadeOut(2000);
				} else {
					if (emailValidationCustom($("#emailAdminInput").val()) == true) {
						if ($("#emailAdminInput").val() == currentEmail) {
							$("#currentEmail-Error").html("* Please enter new email address.").show();
							$("#currentEmail-Error").fadeOut(2000);
						} else {
							$.ajax({
								url: './model/settings-member',
								method:"POST",
								data: {
									saveSettingsAction: 'saveEmail',
									newEmail: $("#emailAdminInput").val()
								},
					            dataType:"json",
					            beforeSend: function() {
									$("#loadingModal").modal('show');
								},
								success: function(data) {
									$("#loadingModal").modal('hide');
									if (data.emailStatus == "existing") {
										$("#currentEmail-Error").html("* Email already exists.").show();
										$("#currentEmail-Error").fadeOut(2000);
									} else if (data.emailStatus == "failed") {
										statusSnackBar.style.backgroundColor = "#d9534f";
								        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Email Change Failed';
							            displayStatusSnackBar();
									} else {
										currentEmail = $("#emailAdminInput").val();
										$("#usernameField").show();
							    		$("#editEmailAdminBtn").show();
							    		$("#editUsernameAdminBtn").show();
							    		$("#emailAdminInput").prop("disabled", true);
							    		$("#deleteAdminAccountInput").show();
							    		$("#savecancelAccountEmail").hide();
										statusSnackBar.style.backgroundColor = "#5cb85c";
							            statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Email Change Successfully';
							            displayStatusSnackBar();
									}
								}
							})
							.fail(function() {
								$("#loadingModal").modal('hide');
								statusSnackBar.style.backgroundColor = "#d9534f";
						        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Email Change Error';
					            displayStatusSnackBar();
							});
							
						}
					} else {
						$("#currentEmail-Error").html("* Invalid email address.").show();
						$("#currentEmail-Error").fadeOut(2000);
					}
				} 
	    	});
			<?php }
    	?>

    	// Cancel Edit Email
    	$("#cancelEmailChangeBtn").click(function() {
    		$("#usernameField").show();
    		$("#editEmailAdminBtn").show();
    		$("#editUsernameAdminBtn").show();
    		$("#emailAdminInput").val(currentEmail).prop("disabled", true);
    		$("#deleteAdminAccountInput").show();
    		$("#savecancelAccountEmail").hide();
    	});

    	// Edit Password
    	$("#changePasswordBtn").click(function() {
    		$("#changePasswordBtnGroup").hide();
    		$("#changePasswordFields").show();
    		$("#savecancelPassword").show();
    	});

    	// Save Password Change
    	$("#accountPasswordForm").submit(function(event) {
			event.preventDefault();
			if (passwordValidationCustom() == true) {
				$.ajax({
					url: './model/settings-member',
					method:"POST",
					data: {
						saveSettingsAction: 'savePassword',
						currentPassword: $("#currentPasswordInput").val(),
						newPassword: $("#newPasswordInput").val()
					},
		            dataType:"json",
		            beforeSend: function() {
						$("#loadingModal").modal('show');
					},
					success: function(data) {
						$("#loadingModal").modal('hide');
						if (data.passwordStatus == "invalid") {
							$("#currentPassword-Error").html("* Invalid password.").show();
							$("#currentPassword-Error").fadeOut(2000);
						} else if (data.passwordStatus == "matched") {
							$("#newPassword-Error").html("* Please enter new password.").show();
							$("#newPassword-Error").fadeOut(2000);
						} else if (data.passwordStatus == "failed") {
							statusSnackBar.style.backgroundColor = "#d9534f";
					        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Password Change Failed';
				            displayStatusSnackBar();
						} else {
                            $("#changePasswordBtnGroup").show();
                            $("#changePasswordFields").hide();
                            $("#currentPasswordInput").val("");
                            $("#newPasswordInput").val("");
                            $("#currentPasswordInput").val("");
                            $("#reenterNewPasswordInput").val("");
							statusSnackBar.style.backgroundColor = "#5cb85c";
				            statusSnackBar.innerHTML = '<i class="fas fa-check"></i> Password Change Successfully';
				            displayStatusSnackBar();
						}
					}
				})
				.fail(function() {
					$("#loadingModal").modal('hide');
					statusSnackBar.style.backgroundColor = "#d9534f";
			        statusSnackBar.innerHTML = '<i class="fas fa-times-circle"></i> Password Change Error';
		            displayStatusSnackBar();
				});
				
			} 
		});

		// Cancel Edit Password
		$("#cancelPasswordChangeBtn").click(function() {
    		$("#changePasswordBtnGroup").show();
    		$("#changePasswordFields").hide();
    		$("#currentPasswordInput").val("");
    		$("#newPasswordInput").val("");
    		$("#currentPasswordInput").val("");
    		$("#reenterNewPasswordInput").val("");
    	});

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

    	// Custom Password Validation
    	function passwordValidationCustom(){
    		var valid = true;

    		// Current Password Validation
    		if ($("#currentPasswordInput").val().trim() == "") {
    			$("#currentPassword-Error").html("* Current Password must not be empty.").show();
				$("#currentPassword-Error").fadeOut(2000);
    			valid = false;
    		}

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