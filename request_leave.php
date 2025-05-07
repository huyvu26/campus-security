<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $leave_date = $_POST['leave_date'];
    $type = $_POST['type'];

    $sql = "INSERT INTO leave_request (staff_id, leave_date, type)
            VALUES ('$staff_id', '$leave_date', '$type')";

    if ($conn->query($sql) === TRUE) {
        echo "✅ Leave request submitted.";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
