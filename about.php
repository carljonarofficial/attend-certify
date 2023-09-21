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
    } 
    session_write_close();
?>
<!DOCTYPE html>
<html>
<head>
	<title>About | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file	
    ?>
    <!-- Social Info Styles -->
    <style>
    	.social-info {
    		font-size: 2rem;
    	}
    	.profle-card {
		    min-width: 180px;
		    max-width: 300px;
		}
    </style>
</head>
<body class="d-flex flex-column">
	<?php 
        // Initialize Active Page for Navbar Highlight
        $activePage = "about";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file
    ?>
    <div class="main-body container-fluid flex-grow-1 mt-5">
    	<div class="container-fluid px-2 pt-2">
    		<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <h1 class="font-weight-bold">ABOUT THIS SYSTEM</h1>
                <h2 class="pl-3 font-weight-normal">Information about this system and the team involved who craft this.</h2>
            </div>
            <!-- About System Informatiom -->
            <div class="container shadow-sm p-3 my-2 mt-4 border-form-override">
            	<div class="bg-info p-2 mw-100 rounded mb-2">
            		<h4 class="mb-0 text-light"><i class="fas fa-info-circle"></i> INFORMATION</h4>
            	</div>
            	<p class="h4 font-weight-normal text-justify">A web-based system that could help the facilitator conducts an event. This system will help to record the attendance of attendee and strengthen the integrity of the issued certificate by using the barcode technology. This will help the facilitator to monitor and record the attendance and issued a certificate in just a tap without any hassle.</p>
            </div>
            <!-- The team who craft this system -->
            <div class="container shadow-sm p-3 my-2 mt-4 border-form-override">
            	<div class="bg-info p-2 mw-100 rounded mb-2">
            		<h4 class="mb-0 text-light"><i class="fas fa-users"></i> MEET THE TEAM</h4>
            	</div>
            	<div class="row">
            		<!-- The team leader -->
            		<div class="col">
            			<div class="card profle-card mx-auto">
            				<img class="card-img-top w-100 h-100" src="img/profile/palado.png" alt="Card image cap" height="200" width="200" onContextMenu="return false;"  ondragstart="return false;">
	                		<div class="card-body" style="min-height: 263px;">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">CARL JONAR N. <br>PALADO</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-star" style="width: 20px; text-align: center;"></i> Lead Researcher</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-code" style="width: 20px; text-align: center;"></i> Programmer</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-pencil-ruler" style="width: 20px; text-align: center;"></i> Designer</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="mailto:cjnpalado.ccit@unp.edu.ph" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                		</div>
	                	</div>
            		</div>
            		<!-- Member 1 -->
            		<div class="col">
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/profile/baludda.png" alt="Card image cap" height="200" width="200" onContextMenu="return false;"  ondragstart="return false;">
	                		<div class="card-body" style="min-height: 263px;">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">STEVEN D. <br>BALUDDA</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-pencil-ruler" style="width: 20px; text-align: center;"></i> Designer</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-file-alt" style="width: 20px; text-align: center;"></i> Documenter</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="mailto:sdbaludda.ccit@unp.edu.ph" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                		</div>
	                	</div>
            		</div>
            		<!-- Member 2 -->
            		<div class="col">
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/profile/ponce.png" alt="Card image cap" height="200" width="200" onContextMenu="return false;"  ondragstart="return false;">
	                		<div class="card-body" style="min-height: 263px;">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">JOHN OLIVER D. <br>PONCE</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-file-alt" style="width: 20px; text-align: center;"></i> Documenter</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="mailto:jodponce.ccit@unp.edu.ph" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                		</div>
	                	</div>
            		</div>
            		<!-- Adviser -->
            		<div class="col">
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/profile/buen.png" alt="Card image cap" height="200" width="200" onContextMenu="return false;"  ondragstart="return false;">
	                		<div class="card-body" style="min-height: 263px;">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">DARYL V. <br>BUEN</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-graduation-cap" style="width: 20px; text-align: center;"></i> Adviser</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center" style="min-height: 73px;">
	                			<!-- Blank -->
	                		</div>
	                	</div>
            		</div>
            	</div>
            </div>
            <!-- The attributions for using this system -->
            <div class="container shadow-sm p-3 my-2 mt-4 mb-4 border-form-override">
            	<div class="bg-info p-2 mw-100 rounded mb-2">
            		<h4 class="mb-0 text-light"><i class="fab fa-creative-commons-by"></i> ATTRIBUTIONS</h4>
            	</div>
            	<ul>
            		<li><h6><a class="text-dark" href="https://stock.adobe.com/images/tiled-floor-background/337221810?prev_url=detail">Tiled floor background By Rawpixel.com - stock.adobe.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://stock.adobe.com/images/graduation-certificate-hand-drawn-outline-doodle-icon/195942140?prev_url=detail">Graduation certificate hand drawn outline doodle icon By Visual Generation - stock.adobe.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://stock.adobe.com/images/bar-code-vector-line-icon-isolated-on-white-background/169364106?prev_url=detail">Bar code vector line icon isolated on white background By Visual Generation - stock.adobe.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://www.freepik.com/vectors/background">Background vector created By Harryarts - www.freepik.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://fontawesome.com/">Font Awesome By Dave Gandy - fontawesome.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://plugins.krajee.com/file-input">Bootstrap File Input By Krajee - plugins.krajee.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://datatables.net/">DataTables designed and created By SpryMedia Ltd. - datatables.net</a></h6></li>
            		<li><h6><a class="text-dark" href="https://getbootstrap.com/">Bootstrap developed By Core Team - getbootstrap.com</a></h6></li>
            		<li><h6><a class="text-dark" href="https://jquery.com/">jQuery under MIT License - jquery.com</a></h6></li>
            		<li><h6>All brand icons are trademarks of their respective owners.</h6></li>
            	</ul>
            </div>
            <!-- Privacy Policy and Terms and Conditions Buttons -->
            <div class="container shadow-sm p-3 my-2 mt-4 mb-4 border-form-override">
            	<div class="row">
            		<div class="col-12 col-sm-6 text-center py-2">
            			<a href="privacy-policy" class="btn btn-primary btn-block" style="font-size: 1.5rem;"><i class="fas fa-user-shield"></i> Privacy Policy</a>	
            		</div>
            		<div class="col-12 col-sm-6 text-center py-2">
            			<a href="terms-conditions" class="btn btn-success btn-block" style="font-size: 1.5rem;"><i class="fas fa-file-alt"></i> Terms and Conditions</a>	
            		</div>
            	</div>
            </div>
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
</body>
</html>