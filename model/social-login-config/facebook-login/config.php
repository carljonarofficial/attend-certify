<?php
	// Require Facebook Autoload File and Start Session
	session_start();
	require_once 'Facebook/autoload.php';

	// Facebook Configuration
	$facebookConfig = new Facebook\Facebook([
		'app_id' => '479606853515276',
		'app_secret' => 'f4cc9420246ce9f4ba05e19286e42ea4',
		'default_graph_version' => 'v2.5',
	]);

	// Facebook Helper Config
	$fbHelper = $facebookConfig->getRedirectLoginHelper();
?>