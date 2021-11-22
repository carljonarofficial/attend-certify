<?php
	$currentEventID = $_GET['eventID'];
	use AttendancePot\AttendanceMember;
	if(!empty($_POST['attendanceAction']) && $_POST['attendanceAction'] == 'listAttendance') {
	    require_once './model/attendance-member.php';
	    $member = new AttendanceMember();
	    $addResponse = $member->listAttendance($currentEventID);
	}
	if(!empty($_POST['attendanceAction']) && $_POST['attendanceAction'] == 'listInvitee') {
	    require_once './model/attendance-member.php';
	    $member = new AttendanceMember();
	    $addResponse = $member->listInvitee($currentEventID);
	}
	if(!empty($_POST['attendanceAction']) && $_POST['attendanceAction'] == 'scanAttendance') {
	    require_once './model/attendance-member.php';
	    $member = new AttendanceMember();
	    $addResponse = $member->scanAttendance($currentEventID);
	}
	if(!empty($_POST['attendanceAction']) && $_POST['attendanceAction'] == 'sendCertificate') {
	    require_once './model/attendance-member.php';
	    $member = new AttendanceMember();
	    $addResponse = $member->sendCertificate($currentEventID, $_POST['inviteeCode']);
	}
	if(!empty($_POST['attendanceAction']) && $_POST['attendanceAction'] == 'sendSelectedCertificate') {
	    require_once './model/attendance-member.php';
	    $member = new AttendanceMember();
	    $addResponse = $member->sendSelectedCertificate($currentEventID);
	}
?>