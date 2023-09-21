<?php
    session_start();
    if (isset($_SESSION["username"])) {
        $id = $_SESSION["ID"];
        $username = $_SESSION["username"];
        $adminEmail = $_SESSION["email"];
        $loggedIn = true;

        // Check if Full Name Session Exists
        if (isset($_SESSION["fullName"])) {
            $adminAccountName = $_SESSION["fullName"];
        } else {
            $adminAccountName = $username;
        }
        session_write_close();

        // Default Time Zone
        date_default_timezone_set('Asia/Manila');
    } else {
        // since the username is not set in session, the user is not-logged-in
        // he is trying to access this page unauthorized
        // so let's clear all session variables and redirect him to index
        session_unset();
        session_write_close();
        $url = "./login.php";
        header("Location: $url");
    }
?>