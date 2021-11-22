<?php
	$currentEventID = $_GET['eventID'];
	use InviteePot\InviteeMember;
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'addInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->addInvitee($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'editInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->editInvitee($currentEventID);
	}
	if(!empty($_POST['inviteeDeleteAction']) && $_POST['inviteeDeleteAction'] == 'deleteInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->deleteInvitee($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'listInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->listInvitee($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'getInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->getInvitee($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'sendEmailInvitee') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->sendEmailInvitation($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'sendSelectedInvitation') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->sendSelectedInvitation($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'getSelectedInvitees') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->getSelectedInvitees($currentEventID);
	}
	if(!empty($_POST['inviteeAction']) && $_POST['inviteeAction'] == 'deleteSelectedInvitees') {
	    require_once './model/invitee-member.php';
	    $member = new InviteeMember();
	    $addResponse = $member->deleteSelectedInvitees($currentEventID);
	}
?>