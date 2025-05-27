<?php
$host = "sql12.freesqldatabase.com";
$user = "sql12781473";
$pass = "w1ngCaQiIe";
$db = "sql12781473";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
