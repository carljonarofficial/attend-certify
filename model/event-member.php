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

			// Post Event Information
			$eventTitle = $_POST["title"];
			$eventDate = $_POST["eventDate"];
			$eventTimeInclusive = $_POST["inclusiveTime"];
			$eventTimeConclusive = $_POST["conclusiveTime"];
			$eventVenue = $_POST["venue"];
			$eventDesciption = $_POST["description"];
			$eventAgenda = $_POST["agenda"];
			$eventTheme = $_POST["theme"];

			// Check if Date And Time Already Set an Event
			$checkExistEvent = $this->checkEventExist($currentAdminID, $eventDate, $eventTimeInclusive, $eventTimeConclusive, 0);
			if ($checkExistEvent) {
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "existing";
	            session_write_close();
	            header("Location: add-event.php");
			} else {
				// File Upload Tests
				$targetDir = "certificate-templates/";
				$fileName = basename($_FILES["certAttachment"]["name"]);
				$fileType = pathinfo($fileName,PATHINFO_EXTENSION);
				$newFileName  = "Cert-template-".$currentAdminID."-".date("Y-m-d-H-i-s-u");
				$targetFilePath = $targetDir . $newFileName . "." . $fileType;
				if(move_uploaded_file($_FILES["certAttachment"]["tmp_name"], $targetFilePath)){
					// Certificate Template
					$certTemplate = $newFileName . "." . $fileType;

					// Insert Query
					$insertStmt = $conn->prepare("INSERT INTO `events`(`admin_ID`, `event_title`, `date`, `time_inclusive`, `time_conclusive`, `venue`, `description`, `agenda`, `theme`, `certificate_template`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$insertStmt->bind_param("isssssssss", $currentAdminID, $eventTitle, $eventDate, $eventTimeInclusive, $eventTimeConclusive, $eventVenue, $eventDesciption, $eventAgenda, $eventTheme, $certTemplate);

					// Validate if Insert Query is successful or not
					if ($insertStmt->execute()) {
						$insertStmt->close();
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

			// Check if Date And Time Already Set an Event
			$checkExistEvent = $this->checkEventExist($currentAdminID, $eventDate, $eventTimeInclusive, $eventTimeConclusive, $eventID);
			if ($checkExistEvent) {
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "existing";
	            session_write_close();
	            header("Location: events.php");
			} else {
				// File Upload Tests
				$targetDir = "certificate-templates/";
				$fileName = basename($_FILES["certAttachment"]["name"]);
				$fileType = pathinfo($fileName,PATHINFO_EXTENSION);
				$newFileName  = "Cert-template-".$currentAdminID."-".date("Y-m-d-H-i-s-u");
				$targetFilePath = $targetDir . $newFileName . "." . $fileType;
				if(move_uploaded_file($_FILES["certAttachment"]["tmp_name"], $targetFilePath)){
					// Certificate Template
					$certTemplate = $newFileName . "." . $fileType;

					// Update Query
					$updateStmt = $conn->prepare("UPDATE `events` SET `event_title`=?, `date`=?, `time_inclusive`=?, `time_conclusive`=?, `venue`=?, `description`=?, `agenda`=?, `theme`=?, `certificate_template`=? WHERE `ID` = ?");
					$updateStmt->bind_param('sssssssssi', $eventTitle, $eventDate, $eventTimeInclusive, $eventTimeConclusive, $eventVenue, $eventDesciption, $eventAgenda, $eventTheme, $certTemplate, $eventID);

					// Validate if Update Query is successful or not
					if ($updateStmt->execute()) {
						$updateStmt->close();
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

		}

		/**
		 * To check if the date and time have existing event
		*/
		public function checkEventExist($adminID, $date, $startTime, $endTime, $eventID)
		{
			// Database Connection
			include 'dbConnection.php';

			// Check if Date And Time Already Set an Event
			$checkExistStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` != ? AND `date` = ? AND (? >= `time_inclusive` AND ? <= `time_conclusive`) AND `status` = 1");
			$checkExistStmt->bind_param('iisss', $adminID, $eventID, $date, $endTime, $startTime);
			$checkExistStmt->execute();
		    $checkExistNum =  $checkExistStmt->get_result();
		    $checkExistStmt->close();
			if ($checkExistNum->num_rows > 0) {
				$existFlag = true;
			} else {
				$existFlag = false;
			}
			return $existFlag;
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

			// Delete Query Statement
			$deleteStmt = $conn->prepare("UPDATE `events` SET `status`= 0 WHERE `admin_ID` = ? AND `ID` = ?");
			$deleteStmt->bind_param('ii', $currentAdminID, $eventID);

			// Validate if Delete Query Statement is successful or not
			if ($deleteStmt->execute()) {
				$deleteStmt->close();
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