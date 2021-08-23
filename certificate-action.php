<?php
	use CertificatePot\CertificateMember;
	if(!empty($_POST['certificateAction']) && $_POST['certificateAction'] == 'listCertificate') {
	    require_once './model/certificate-member.php';
	    $member = new CertificateMember();
	    $addResponse = $member->listCertificate($_POST['eventID']);
	}
	if(!empty($_POST['certificateAction']) && $_POST['certificateAction'] == 'validateCertificate') {
	    require_once './model/certificate-member.php';
	    $member = new CertificateMember();
	    $addResponse = $member->validateCertificate($_POST['eventID'], $_POST['certificateCode']);
	}
	if(!empty($_POST['certificateAction']) && $_POST['certificateAction'] == 'getCertificate') {
	    require_once './model/certificate-member.php';
	    $member = new CertificateMember();
	    $addResponse = $member->getCertificate($_POST['inviteeCode'], $_POST['currentEventID']);
	}
	if(!empty($_POST['certificateAction']) && $_POST['certificateAction'] == 'sendCertificate') {
	    require_once './model/certificate-member.php';
	    $member = new CertificateMember();
	    $addResponse = $member->sendCertificate($_POST['inviteeCode'], $_POST['currentEventID']);
	}
?>