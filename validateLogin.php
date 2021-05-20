<?php
    session_start();
    if (isset($_SESSION["username"])) {
        $id = $_SESSION["ID"];
        $username = $_SESSION["username"];
        session_write_close();
    } else {
        // since the username is not set in session, the user is not-logged-in
        // he is trying to access this page unauthorized
        // so let's clear all session variables and redirect him to index
        session_unset();
        session_write_close();
        $url = "./index.php";
        header("Location: $url");
    }
?>