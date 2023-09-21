<?php
	// Check if is already logged in
	$adminAccountName = "";
	$loggedIn = false;
	session_start();
    if (isset($_SESSION["username"])) {
    	$username = $_SESSION["username"];
    	$loggedIn = true;

        // Check if Full Name Session Exists
        if (isset($_SESSION["fullName"])) {
            $adminAccountName = $_SESSION["fullName"];
        } else {
            $adminAccountName = $username;
        }
    	$buttonStr = '<a href="home" class="btn btn-success" style="font-size: 2rem;"><i class="fas fa-home"></i> Go to Home Page</a>';
    } else {
    	$buttonStr = '<a href="login" class="btn btn-primary" style="font-size: 2rem;"><i class="fas fa-sign-in-alt"></i> Login to Continue</a>';
    }
    session_write_close();

?>
<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>
    <style type="text/css">
    	.four-fundamentals-text{
    		font-size: 1.5rem;
    		font-weight: 700;
    	}
        .four-fundamentals-img {
            transition: -webkit-transform .3s linear;
            transition: transform .3s linear;
            transition: transform .3s linear,-webkit-transform .3s linear;
        }
        .four-fundamentals-box:hover .four-fundamentals-img {
            -webkit-transform: scale(1.1);
            transform: scale(1.1);
        }
    </style>
</head>
<body class="d-flex flex-column">
	<?php 
        // Initialize Active Page for Navbar Highlight
        $activePage = "";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
    <div class="main-body container-fluid flex-grow-1 mt-5">
    	<div class="w-100 p-3 mb-3 shadow-sm rounded bg-light text-dark border-form-override text-center">
    		<!-- Logo with Text -->
    		<img src="img/logo_with_text.svg" width="100%" style="max-width: 250px" onContextMenu="return false;" ondragstart="return false;">
    		<!-- Welcome -->
    		<h1 class="my-5">Welcome to Attend and Certify</h1>
    		<!-- System Tagline -->
    		<p style="font-size: 1.5rem;">Where you create an event, invite to them, check their attendance using barcode on scheduled date and time, and get certified embedded with barcode.</p>
    		<!-- Four Fundamentals -->
    		<div class="row my-5">
    			<div class="col-sm-6 col-md-3">
                    <div style="width: fit-content;" class="four-fundamentals-box mx-auto">
                        <img class="four-fundamentals-img" src="img/assets/events.png" width="150" onContextMenu="return false;" ondragstart="return false;">
                        <p class="four-fundamentals-text">Create<br>Event</p>
                    </div>
    			</div>
                <div class="col-sm-6 col-md-3">
                    <div style="width: fit-content;" class="four-fundamentals-box mx-auto">
                        <img class="four-fundamentals-img" src="img/assets/invitees.png" width="150" onContextMenu="return false;" ondragstart="return false;">
                        <p class="four-fundamentals-text">Invite<br>Them</p>
                    </div>
                </div>
    			<div class="col-sm-6 col-md-3">
                    <div style="width: fit-content;" class="four-fundamentals-box mx-auto">
                        <img class="four-fundamentals-img" src="img/assets/attendance.png" width="150" onContextMenu="return false;" ondragstart="return false;">
                        <p class="four-fundamentals-text">Scan<br>Attendance</p>
                    </div>
    			</div>
    			<div class="col-sm-6 col-md-3">
                    <div style="width: fit-content;" class="four-fundamentals-box mx-auto">
                        <img class="four-fundamentals-img" src="img/assets/certificate.png" width="150" onContextMenu="return false;" ondragstart="return false;">
                    <p class="four-fundamentals-text">Generate<br>Certificate</p>
                    </div>
    			</div>
    		</div>
    		<!-- To Start -->
    		<h4>To start this, just:</h4>
    		<?php echo $buttonStr;?>
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
</body>
</html>