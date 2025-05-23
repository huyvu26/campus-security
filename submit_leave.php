<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $leave_date = $_POST['leave_date'];
    $type = $_POST['type'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO leave_request (staff_id, leave_date, type, status)
            VALUES ('$staff_id', '$leave_date', '$type', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>✅ $type request submitted successfully.</p>";
    } else {
        echo "<p>❌ Error: " . $conn->error . "</p>";
    }
}
?>
