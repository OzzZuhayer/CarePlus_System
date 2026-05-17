<?php
date_default_timezone_set('Asia/Dhaka');

// This file handles the database connection.
// Every controller will include this file to talk to the database.

class Db {
    function connection() {
        $dbHost = "";
        $dbUser = "";
        $dbPassword = "";
        $dbName = "project_wt";

        // Create the connection
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        // If connection fails, stop everything and show the error
        if ($conn->connect_error) {
            die("Connection failed : {$conn->connect_error}");
        }

        return $conn;
    }
}
