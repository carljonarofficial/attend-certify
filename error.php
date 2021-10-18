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
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $serverName;?>/attend-certify/img/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $serverName;?>/attend-certify/img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $serverName;?>/attend-certify/img/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php echo $serverName;?>/attend-certify/img/favicon/site.webmanifest">
	<link rel="mask-icon" href="<?php echo $serverName;?>/attend-certify/img/faviconsafari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<!-- Bootstrap Stylesheet -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

	<!-- Other Custom Stylesheet -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" crossorigin="anonymous">
	<link href="<?php echo $serverName;?>/attend-certify/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>

	<!-- Main Custom Stylesheet -->
	<link rel="stylesheet" href="<?php echo $serverName;?>/attend-certify/style/style.css">

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
			<div class="col d-flex justify-content-center">
				<img src="<?php echo $serverName;?>/attend-certify/img/logo_circle.svg" width="200" onContextMenu="return false;" ondragstart="return false;">
			</div>
		</div>
		<div class="row">
			<div class="col d-flex justify-content-center p-3 ">
				<div class="bg-white p-3 border rounded">
					<img src="<?php echo $serverName;?>/attend-certify/img/logo_text.svg" height="30" onContextMenu="return false;" ondragstart="return false;">
				</div>
			</div>
		</div>
		<div class="border-form-override mx-auto" style="max-width: fit-content;">
			<div class="row">
				<div class="col p-3 d-flex justify-content-center">
					<img src="<?php echo $serverName;?>/attend-certify/img/<?php echo $imgFile;?>" width="200" onContextMenu="return false;" ondragstart="return false;">
				</div>
				<div class="col p-3 my-auto">
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