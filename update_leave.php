<?php
include 'db.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $sql = "UPDATE leave_request SET status = '$status' WHERE id = $id";

    if ($conn->query($sql)) {
        echo "<p>Leave request $status successfully.</p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    echo "<p><a href='manage_leaves.php'>Go Back</a></p>";
}
?>
