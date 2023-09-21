<?php
	$title = "Error";
	$imgFile = "warning.png";
	$message = "YOU ENCOUNTERED AN ERROR, PLEASE TRY AGAIN.";
	if(isset($_GET['code'])) {
		if ($_GET['code'] == 404) {
			$title = "404 Not Found";
			$imgFile = "repairman.png";
			$message = "THE PAGE YOU ARE LOOKING FOR CAN'T BE FOUND.";
		} else if ($_GET['code'] == 403) {
			$title = "403 Forbidden";
			$imgFile = "guard.png";
			$message = "YOU DON'T HAVE PERMISSION TO ACCESS THIS RESOURCE.";
		}
	}
	$serverName =  ((!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'];
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?> | Attend and Certify</title>
	<!-- Stylesheets -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $serverName;?>/img/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $serverName;?>/img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $serverName;?>/img/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php echo $serverName;?>/img/favicon/site.webmanifest">
	<link rel="mask-icon" href="<?php echo $serverName;?>/img/faviconsafari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<!-- Bootstrap Stylesheet -->
	<link rel="stylesheet" href="<?php echo $serverName;?>/style/bootstrap/bootstrap.min.css">
	<script src="<?php echo $serverName;?>/scripts/jquery-3.5.1.min.js"></script>
	<script src="<?php echo $serverName;?>/scripts/bootstrap/bootstrap.bundle.min.js"></script>

	<!-- Other Custom Stylesheet -->
	<link href="<?php echo $serverName;?>/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>

	<!-- Main Custom Stylesheet -->
	<link rel="stylesheet" href="<?php echo $serverName;?>/style/style.css">

    <!-- Modified Styles -->
    <style>
    	.rounded {
		    border-radius: .75rem!important;
		}
    </style>
</head>
<body class="d-flex flex-column">
	<div class="container-fluid p-3">
		<div class="row">
			<div class="col d-flex justify-content-center my-3">
				<a href=".">
					<img class="bg-white p-3 rounded" src="<?php echo $serverName;?>/img/logo_with_text.svg" width="250" onContextMenu="return false;" ondragstart="return false;">
				</a>
			</div>
		</div>
		<div class="border-form-override mx-auto" style="max-width: fit-content;">
			<div class="row">
				<div class="col p-4 d-flex justify-content-center">
					<img src="<?php echo $serverName;?>/img/<?php echo $imgFile;?>" width="200" onContextMenu="return false;" ondragstart="return false;">
				</div>
				<div class="col p-4 my-auto">
					<h1 class="text-center">Oops!</h1>
					<h2 class="text-center" style="font-weight: 600;"><?php echo $title;?></h2>
					<div class="text-center" style="font-weight: 600;">
						<p><?php echo $message;?></p>
					</div>	
				</div>
			</div>
		</div>
	</div>
</body>
</html>