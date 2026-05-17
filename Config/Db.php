<?php
date_default_timezone_set('Asia/Dhaka');

// This file handles the database connection.
// Every controller will include this file to talk to the database.

class Db {
    function connection() {
        $dbHost = "100.117.12.1";
        $dbUser = "wt_collborator";
        $dbPassword = "WT_G03_SP25-26[C]";
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
