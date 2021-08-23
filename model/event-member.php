<?php 
	namespace Eventpot;

	/**
	 * 
	 */
	class EventMember
	{

		/**
		 * To add up an event
		*/
		public function addEvent()
		{
			// Set Default Time Zone
			date_default_timezone_set("Asia/Manila");

			// Database Connection
			include 'dbConnection.php';

			// Current Admin ID
			$currentAdminID = $_SESSION["ID"];

			// File Upload Tests
			$targetDir = "certificate-templates/";
			$fileName = basename($_FILES["certAttachment"]["name"]);
			$fileType = pathinfo($fileName,PATHINFO_EXTENSION);
			$newFileName  = "Cert-template-".$currentAdminID."-".date("Y-m-d-H-i-s-u");
			$targetFilePath = $targetDir . $newFileName . "." . $fileType;
			if(move_uploaded_file($_FILES["certAttachment"]["tmp_name"], $targetFilePath)){

				// Post Event Information
				$eventTitle = $_POST["title"];
				$eventDate = $_POST["eventDate"];
				$eventTimeInclusive = $_POST["inclusiveTime"];
				$eventTimeConclusive = $_POST["conclusiveTime"];
				$eventVenue = $_POST["venue"];
				$eventDesciption = $_POST["description"];
				$eventAgenda = $_POST["agenda"];
				$eventTheme = $_POST["theme"];
				$certTemplate = $newFileName . "." . $fileType;

				// Insert Query
				$query = "INSERT INTO `events`(`admin_ID`, `event_title`, `date`, `time_inclusive`, `time_conclusive`, `venue`, `description`, `agenda`, `theme`, `certificate_template`) VALUES ('$currentAdminID','$eventTitle','$eventDate','$eventTimeInclusive','$eventTimeConclusive','$eventVenue','$eventDesciption','$eventAgenda','$eventTheme','$certTemplate')";

				// Validate if Insert Query is successful or not
				if ($conn->query($query) === TRUE) {
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "add-success";
		            session_write_close();
		            header("Location: events.php");
				}else{
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "add-error";
		            session_write_close();
		            header("Location: add-event.php");
				}
			}else{
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "add-error";
	            session_write_close();
	            header("Location: add-event.php");
			}
		}

		/**
		 * To edit up an event
		*/
		public function editEvent()
		{
			// Set Default Time Zone
			date_default_timezone_set("Asia/Manila");

			// Database Connection
			include 'dbConnection.php';

			// Current Admin ID
			$currentAdminID = $_SESSION["ID"];

			// File Upload Tests
			$targetDir = "certificate-templates/";
			$fileName = basename($_FILES["certAttachment"]["name"]);
			$fileType = pathinfo($fileName,PATHINFO_EXTENSION);
			$newFileName  = "Cert-template-".$currentAdminID."-".date("Y-m-d-H-i-s-u");
			$targetFilePath = $targetDir . $newFileName . "." . $fileType;
			if(move_uploaded_file($_FILES["certAttachment"]["tmp_name"], $targetFilePath)){

				// Post Event Information
				$eventID = $_POST["eventId"];
				$eventTitle = $_POST["title"];
				$eventDate = $_POST["eventDate"];
				$eventTimeInclusive = $_POST["inclusiveTime"];
				$eventTimeConclusive = $_POST["conclusiveTime"];
				$eventVenue = $_POST["venue"];
				$eventDesciption = $_POST["description"];
				$eventAgenda = $_POST["agenda"];
				$eventTheme = $_POST["theme"];
				$certTemplate = $newFileName . "." . $fileType;

				// Update Query
				$query = "UPDATE `events` SET `event_title`='$eventTitle',`date`='$eventDate',`time_inclusive`='$eventTimeInclusive',`time_conclusive`='$eventTimeConclusive',`venue`='$eventVenue',`description`='$eventDesciption',`agenda`='$eventAgenda',`theme`='$eventTheme',`certificate_template`='$certTemplate' WHERE `ID` = $eventID";

				// Validate if Insert Query is successful or not
				if ($conn->query($query) === TRUE) {
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "edit-success";
		            session_write_close();
				}else{
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "edit-error";
		            session_write_close();
				}
				header("Location: events.php");
			}else{
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "edit-error";
	            session_write_close();
	            header("Location: events.php");
			}
		}

		/*
		 * To delete up an event
		*/
		public function deleteEvent()
		{
			// Current Admin ID
			$currentAdminID = $_SESSION["ID"];

			// Event ID to be deleted
			$eventID = $_POST["eventId"];

			// Database Connection
			include 'dbConnection.php';

			// Delete Query
			$query = "UPDATE `events` SET `status`= 0 WHERE `admin_ID` = $currentAdminID AND `ID` = $eventID";

			// Validate if Delete Query is successful or not
			if ($conn->query($query) === TRUE) {
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "delete-success";
	            session_write_close();
			}else{
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "delete-error";
	            session_write_close();
			}
			header("Location: events.php");
		}
	}
?>