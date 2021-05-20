<?php  

	// Create connection
	$conn = mysqli_connect("localhost", "Kornbeef-19", "osuyavcezlirhld", "attend_certify");

	//mysqli_connect() function opens a new connection to the mysql server. The previous version is mysql_connect (), then mysqli_connect() where i means improved version and is more secured.

    // Check connection
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
        // die () is an inbuilt function in PHP. It is used to print message and exit from the current php script
    }

?>