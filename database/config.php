<?php

        $host = 'localhost';
        $user = 'root';
        $pass = 'root';
        $dbname = 'team-27-project-database'; // change this to what your database is called for now, we'll sort out a name for it later on
        $port = 8889;

        $conn = new mysqli($host, $user, $pass, $dbname, $port);

        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

?>
