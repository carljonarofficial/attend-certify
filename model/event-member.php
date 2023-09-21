<?php 
	namespace Eventpot;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require 'model/Exception.php';
	require 'model/PHPMailer.php';
	require 'model/SMTP.php';

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

			// Current Date and Time
			$currentDateTime = date("Y-m-d H:i:s");

			// Post Event Information
			$eventTitle = $_POST["title"];
			$eventDate = $_POST["eventDate"];
			$eventDateEnd = $_POST["eventDateEnd"];
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
					$insertStmt = $conn->prepare("INSERT INTO `events`(`admin_ID`, `event_title`, `date`, `date_end`, `time_inclusive`, `time_conclusive`, `venue`, `description`, `agenda`, `theme`, `certificate_template`, `datetime_added`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$insertStmt->bind_param("isssssssssss", $currentAdminID, $eventTitle, $eventDate, $eventDateEnd, $eventTimeInclusive, $eventTimeConclusive, $eventVenue, $eventDesciption, $eventAgenda, $eventTheme, $certTemplate, $currentDateTime);

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

			// Current Date and Time
			$currentDateTime = date("Y-m-d H:i:s");

			// Validate if the admin logged in
    		include 'validateLogin.php';

			// Database Connection
			include 'dbConnection.php';

			// Current Admin ID
			$currentAdminID = $_SESSION["ID"];

			// Post Event Information
			$eventID = $_POST["eventId"];
			$eventTitle = $_POST["title"];
			$eventDate = $_POST["eventDate"];
			$eventDateEnd = $_POST["eventDateEnd"];
			$eventTimeInclusive = $_POST["inclusiveTime"];
			$eventTimeConclusive = $_POST["conclusiveTime"];
			$eventVenue = $_POST["venue"];
			$eventDesciption = $_POST["description"];
			$eventAgenda = $_POST["agenda"];
			$eventTheme = $_POST["theme"];

			// Post Current Event Title, Date and Time
			$currentEventTitle = $_POST["currentTitle"];
			$currentEventDate = $_POST["currentDate"];
			$currentTimeInclusive = $_POST["currentStartTime"];
			$currentTimeConclusive = $_POST["currentEndTime"];

			// Get Certificate Template File name
			$fileNameStmt = $conn->prepare("SELECT `certificate_template` FROM `events` WHERE `admin_ID` = ? AND `ID` = ? AND `status` = 1");
			$fileNameStmt->bind_param('ii', $currentAdminID, $eventID);
			$fileNameStmt->execute();
		    $fileNameResult =  $fileNameStmt->get_result();
		    $fileNameStmt->close();
			if ($fileNameResult->num_rows > 0) {
				while ($row = $fileNameResult->fetch_assoc()) {
					$eventCertificateTemplate = $row['certificate_template'];
				}
				// Check if Date And Time Already Set an Event
				$checkExistEvent = $this->checkEventExist($currentAdminID, $eventDate, $eventTimeInclusive, $eventTimeConclusive, $eventID);
				if ($checkExistEvent) {
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "existing";
		            session_write_close();
		            header("Location: events.php");
				} else {
					// Check if admin wants to change certificate template
					if (isset($_POST["changeCertTemplateCheckbox"])) {
						// File Upload Tests
						$targetDir = "certificate-templates/";
						$targetFilePath = $targetDir . $eventCertificateTemplate;
						// Check if File Exists
						if (file_exists($targetFilePath)) {
							// Check if Unlik Successfully
							if (unlink($targetFilePath)) {
								if(move_uploaded_file($_FILES["certAttachment"]["tmp_name"], $targetFilePath)){
									// Certificate Template
									$certTemplate = $eventCertificateTemplate;

									// Update Query
									$updateStmt = $conn->prepare("UPDATE `events` SET `event_title`=?, `date`=?, `date_end`=?, `time_inclusive`=?, `time_conclusive`=?, `venue`=?, `description`=?, `agenda`=?, `theme`=?, `certificate_template`=?, `datetime_edited`=? WHERE `ID` = ?");
									$updateStmt->bind_param('sssssssssssi', $eventTitle, $eventDate, $eventDateEnd, $eventTimeInclusive, $eventTimeConclusive, $eventVenue, $eventDesciption, $eventAgenda, $eventTheme, $certTemplate, $currentDateTime, $eventID);

									// Validate if Update Query is successful or not
									if ($updateStmt->execute()) {
										$updateStmt->close();
										// Validate if Postpone Event is Checked
										if (isset($_POST["postponeEvent"]) && $currentEventDate != $eventDate) {
											// Formatting Date and Time
											$currentEventDate = date_format(date_create($currentEventDate),"F d, Y");
											$currentTimeInclusive = date_format(date_create($currentTimeInclusive),"h:iA");
											$currentTimeConclusive = date_format(date_create($currentTimeConclusive),"h:iA");
											$eventDate = date_format(date_create($eventDate),"F d, Y");
											$eventTimeInclusive = date_format(date_create($eventTimeInclusive),"h:iA");
											$eventTimeConclusive = date_format(date_create($eventTimeConclusive),"h:iA");

											// Fetch up selected Invitees info from database
											$selectedStmt = $conn -> prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1");
											$selectedStmt->bind_param("i", $eventID);
											$selectedStmt->execute();
											$selectedResults =  $selectedStmt->get_result();
											$selectedStmt->close();

											if ($selectedResults->num_rows > 0) {
												// passing true in constructor enables exceptions in PHPMailer
												$mail = new PHPMailer(true);

												// Server settings
												$mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
												$mail->isSMTP();
												$mail->Host = 'smtp.hostinger.com';
												$mail->SMTPAuth = true;
												$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
												$mail->Port = 587;

												$mail->Username = 'invitations@attend-certify.com';
												$mail->Password = 'VLDHpmnE_c2ZhLA';

												// Setting the general email content
												$mail->IsHTML(true);
												$mail->Subject = $eventTitle." - Event Postponement | Attend and Certify";

												$dateToday = date("F d, Y");

												try {
													// Send Selected Invitees' Postponement Event
													while ($row = $selectedResults->fetch_assoc()) {
														$inviteeName =  $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
														$inviteeEmail =  $row["email"];
														
														// Sender and recipient settings
														$mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
														$mail->addAddress($inviteeEmail, $inviteeName);

														// Add Body Message Using HTML Email
														$mail->Body = include 'model/html-email-template-for-postpone-event.php';

														$mail->send();

														$mail->clearAddresses();
													}
													session_start();
													$_SESSION["event-crud-validation-msg"] = "edit-success-postpone-success";
													session_write_close();
												} catch (Exception $e) {
													session_start();
													$_SESSION["event-crud-validation-msg"] = "edit-success-postpone-error";
													session_write_close();
												}
											} else {
												session_start();
												$_SESSION["event-crud-validation-msg"] = "edit-success";
												session_write_close();
											}
										} else {
											session_start();
											$_SESSION["event-crud-validation-msg"] = "edit-success";
											session_write_close();
										}
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
							} else {
								session_start();
								$_SESSION["event-crud-validation-msg"] = "edit-error";
								session_write_close();
								header("Location: events.php");
							}
						} else {
							session_start();
							$_SESSION["event-crud-validation-msg"] = "edit-error";
							session_write_close();
							header("Location: events.php");
						}	
					} else {
						// Update Query
						$updateStmt = $conn->prepare("UPDATE `events` SET `event_title`=?, `date`=?, `date_end`=?, `time_inclusive`=?, `time_conclusive`=?, `venue`=?, `description`=?, `agenda`=?, `theme`=?, `datetime_edited`=? WHERE `ID` = ?");
						$updateStmt->bind_param('ssssssssssi', $eventTitle, $eventDate, $eventDateEnd, $eventTimeInclusive, $eventTimeConclusive, $eventVenue, $eventDesciption, $eventAgenda, $eventTheme, $currentDateTime, $eventID);

						// Validate if Update Query is successful or not
						if ($updateStmt->execute()) {
							$updateStmt->close();
							// Validate if Postpone Event is Checked
							if (isset($_POST["postponeEvent"]) && $currentEventDate != $eventDate) {
								// Formatting Date and Time
								$currentEventDate = date_format(date_create($currentEventDate),"F d, Y");
								$currentTimeInclusive = date_format(date_create($currentTimeInclusive),"h:iA");
								$currentTimeConclusive = date_format(date_create($currentTimeConclusive),"h:iA");
								$eventDate = date_format(date_create($eventDate),"F d, Y");
								$eventTimeInclusive = date_format(date_create($eventTimeInclusive),"h:iA");
								$eventTimeConclusive = date_format(date_create($eventTimeConclusive),"h:iA");

								// Fetch up selected Invitees info from database
								$selectedStmt = $conn -> prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1");
								$selectedStmt->bind_param("i", $eventID);
								$selectedStmt->execute();
								$selectedResults =  $selectedStmt->get_result();
								$selectedStmt->close();

								if ($selectedResults->num_rows > 0) {
									// passing true in constructor enables exceptions in PHPMailer
									$mail = new PHPMailer(true);

									// Server settings
									$mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
									$mail->isSMTP();
									$mail->Host = 'smtp.hostinger.com';
									$mail->SMTPAuth = true;
									$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
									$mail->Port = 587;

									$mail->Username = 'invitations@attend-certify.com';
									$mail->Password = 'VLDHpmnE_c2ZhLA';

									// Setting the general email content
									$mail->IsHTML(true);
									$mail->Subject = $eventTitle." - Event Postponement | Attend and Certify";

									$dateToday = date("F d, Y");

									try {
										// Send Selected Invitees' Postponement Event
										while ($row = $selectedResults->fetch_assoc()) {
											$inviteeName =  $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
											$inviteeEmail =  $row["email"];
											
											// Sender and recipient settings
											$mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
											$mail->addAddress($inviteeEmail, $inviteeName);

											// Add Body Message Using HTML Email
											$mail->Body = include 'model/html-email-template-for-postpone-event.php';

											$mail->send();

											$mail->clearAddresses();
										}
										session_start();
										$_SESSION["event-crud-validation-msg"] = "edit-success-postpone-success";
										session_write_close();
									} catch (Exception $e) {
										session_start();
										$_SESSION["event-crud-validation-msg"] = "edit-success-postpone-error";
										session_write_close();
									}
								} else {
									session_start();
									$_SESSION["event-crud-validation-msg"] = "edit-success";
									session_write_close();
								}
							} else {
								session_start();
								$_SESSION["event-crud-validation-msg"] = "edit-success";
								session_write_close();
							}
						}else{
							session_start();
							$_SESSION["event-crud-validation-msg"] = "edit-error";
							session_write_close();
						}
						header("Location: events.php");
					}
				}
			} else {
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "edit-error";
	            session_write_close();
	            header("Location: events.php");
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
			// Set Default Time Zone
			date_default_timezone_set("Asia/Manila");

			// Current Admin ID
			$currentAdminID = $_SESSION["ID"];

			// Event ID to be deleted
			$eventID = $_POST["eventId"];

			// Validate if the admin logged in
    		include 'validateLogin.php';

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an event information
			$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ?");
	        $eventStmt->bind_param('ii', $id, $eventID);
	        $eventStmt->execute();
	        $eventInfo =  $eventStmt->get_result();
	        $eventStmt->close();
	        while ($row = $eventInfo->fetch_assoc()) {
	            $eventTitle = $row["event_title"];
	            $eventDate = date_format(date_create($row["date"]),"F d, Y");
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	        }

			// Delete Query Statement
			$deleteStmt = $conn->prepare("UPDATE `events` SET `status`= 0 WHERE `admin_ID` = ? AND `ID` = ?");
			$deleteStmt->bind_param('ii', $currentAdminID, $eventID);

			// Validate if Delete Query Statement is successful or not
			if ($deleteStmt->execute()) {
				$deleteStmt->close();
				// Validate if Cancel Event is Checked
				if (isset($_POST["cancelEvent"])) {
					// Fetch up selected Invitees info from database
					$selectedStmt = $conn -> prepare("SELECT * FROM `invitees` WHERE `event_ID` = ? AND `status` = 1");
					$selectedStmt->bind_param("i", $eventID);
					$selectedStmt->execute();
					$selectedResults =  $selectedStmt->get_result();
					$selectedStmt->close();
					if ($selectedResults->num_rows > 0) {
						// passing true in constructor enables exceptions in PHPMailer
						$mail = new PHPMailer(true);

						// Server settings
						$mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
						$mail->isSMTP();
						$mail->Host = 'smtp.hostinger.com';
						$mail->SMTPAuth = true;
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
						$mail->Port = 587;

						$mail->Username = 'invitations@attend-certify.com';
						$mail->Password = 'VLDHpmnE_c2ZhLA';

						// Setting the general email content
						$mail->IsHTML(true);
						$mail->Subject = $eventTitle." - Event Cancellation | Attend and Certify";

						$dateToday = date("F d, Y");

						try {
							// Send  Invitees' Cancellation Event
							while ($row = $selectedResults->fetch_assoc()) {
								$inviteeName =  $row["firstname"] . " ". $row["middlename"] . " ". $row["lastname"];
								$inviteeEmail =  $row["email"];
								
								// Sender and recipient settings
								$mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
								$mail->addAddress($inviteeEmail, $inviteeName);

								// Add Body Message Using HTML Email
								$mail->Body = include 'model/html-email-template-for-cancel-event.php';

								$mail->send();

								$mail->clearAddresses();
							}
							session_start();
				            $_SESSION["event-crud-validation-msg"] = "delete-success-cancel-success";
				            session_write_close();
						} catch (Exception $e) {
							session_start();
				            $_SESSION["event-crud-validation-msg"] = "delete-success-cancel-error";
				            session_write_close();
						}
					} else {
						session_start();
			            $_SESSION["event-crud-validation-msg"] = "delete-success";
			            session_write_close();
					}
				} else {
					session_start();
		            $_SESSION["event-crud-validation-msg"] = "delete-success";
		            session_write_close();
				}
			}else{
				session_start();
	            $_SESSION["event-crud-validation-msg"] = "delete-error";
	            session_write_close();
			}
			header("Location: events.php");
		}
	}
?>