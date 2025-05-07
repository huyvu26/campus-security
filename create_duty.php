<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $duty_date = $_POST['duty_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $place = $_POST['place'];

    $sql = "INSERT INTO duty (staff_id, duty_date, start_time, end_time, place)
            VALUES ('$staff_id', '$duty_date', '$start_time', '$end_time', '$place')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>✅ Duty scheduled successfully.</p>";
    } else {
        echo "<p>❌ Error: " . $conn->error . "</p>";
    }
}
?>
