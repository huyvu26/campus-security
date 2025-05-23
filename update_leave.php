<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $sql = "UPDATE leave_request SET status='$status' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: monitor.php"); // ✅ redirect back to the unified dashboard
        exit();
    } else {
        echo "❌ Error updating request: " . $conn->error;
    }
} else {
    echo "❌ Missing parameters.";
}
?>
