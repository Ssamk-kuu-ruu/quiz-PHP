<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "quizapp";
$port = "3307";

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if ($conn -> connect_error) {
    die("Connection failed: " . $conn -> connect_error)
}