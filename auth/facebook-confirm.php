<?php
	// Require Facebook Config File
	require '../model/social-login-config/facebook-login/config.php';

	// Permissions
	$permissions = ['email'];

	// Log in URL and Proceed
	$loginURL = $fbHelper->getLoginUrl('https://attend-certify.com/auth/facebook-login', $permissions);
	header("Location: $loginURL");
?>