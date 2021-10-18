<?php
	// Validate if the admin logged in
    include 'validateLogin.php';
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
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/man.png" alt="Card image cap" height="200" width="200">
	                		<div class="card-body">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">CARL JONAR N. PALADO</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-star"></i> Lead Researcher</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-code"></i> Programmer</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-pencil-ruler"></i> Designer</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="#" class="card-link social-info" style="color: #1877f2;"><i class="fab fa-facebook"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #00acee;"><i class="fab fa-twitter"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #bc2a8d;"><i class="fab fa-instagram"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #28a745;"><i class="fas fa-phone"></i></a>
	                		</div>
	                	</div>
            		</div>
            		<!-- Member 1 -->
            		<div class="col">
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/man.png" alt="Card image cap" height="200" width="200">
	                		<div class="card-body">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">STEVEN D. BALUDDA</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-pencil-ruler"></i> Designer</h6>
	                				</li>
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-file-alt"></i> Documenter</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="#" class="card-link social-info" style="color: #1877f2;"><i class="fab fa-facebook"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #bc2a8d;"><i class="fab fa-instagram"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #28a745;"><i class="fas fa-phone"></i></a>
	                		</div>
	                	</div>
            		</div>
            		<!-- Member 2 -->
            		<div class="col">
            			<div class="card profle-card">
            				<img class="card-img-top w-100 h-100" src="img/man.png" alt="Card image cap" height="200" width="200">
	                		<div class="card-body">
	                			<div class="card-title">
	                				<h4 class="font-weight-bold">JOHN OLIVER D. PONCE</h4>
	                			</div>
	                			<ul class="list-group list-group-flush">
	                				<li class="list-group-item">
	                					<h6><i class="fas fa-file-alt"></i> Documenter</h6>
	                				</li>
	                			</ul>
	                		</div>
	                		<div class="card-footer text-center">
	                			<a href="#" class="card-link social-info" style="color: #1877f2;"><i class="fab fa-facebook"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #00acee;"><i class="fab fa-twitter"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #bc2a8d;"><i class="fab fa-instagram"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #EA4335;"><i class="fas fa-envelope-square"></i></a>
	                			<a href="#" class="card-link social-info" style="color: #28a745;"><i class="fas fa-phone"></i></a>
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
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
</body>
</html>