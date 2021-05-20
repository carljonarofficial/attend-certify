<?php 
	namespace Eventpot;

	/**
	 * 
	 */
	class EventMember
	{
		/**
		 * To add up an event
		 *
		 * @return string[] registration status message
		*/
		public function addEvent()
		{
			//include 'dbConnection.php';
			session_start();
            $_SESSION["event-crud-validation-msg"] = "success";
            session_write_close();
			header("Location: events.php");
		}
	}
?>