<?php
    session_start();
    if (isset($_SESSION["username"])) {
        $id = $_SESSION["ID"];
        $username = $_SESSION["username"];
        $adminEmail = $_SESSION["email"];
        session_write_close();
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