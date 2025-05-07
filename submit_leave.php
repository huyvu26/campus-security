<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $leave_date = $_POST['leave_date'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO leave_request (staff_id, leave_date, type)
            VALUES ('$staff_id', '$leave_date', 'Leave')";

    if ($conn->query($sql)) {
        echo "<p>✅ Leave request submitted successfully.</p>";
    } else {
        echo "<p>❌ Error: " . $conn->error . "</p>";
    }
}
?>
