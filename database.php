<?php

//db con

$host = "localhost";
$user = "root";
$password = "";
$database = "technical_test";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Database Connection failed");
}


    ?>