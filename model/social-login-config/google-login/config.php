<?php
	// Site URL
	define("SITE_URL", 'https://attend-certify.com/');
	// The page where you will be redirected for authorzation
	define('REDIRECT_URL', SITE_URL."auth/google-login");

	// Google Related Activities
	define("CLIENT_ID", "98168373274-jhuutq5mupcmknpgjs1t3m6k050og334.apps.googleusercontent.com");
	define("CLIENT_SECRET", "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");
	
	// Permission
	define("SCOPE", 'https://www.googleapis.com/auth/userinfo.email '.
		'https://www.googleapis.com/auth/userinfo.profile' );
	
?>