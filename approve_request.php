<?php
include 'db.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("❌ Missing parameters.");
}

$id = intval($_GET['id']);
$action = $_GET['action'];

// Determine new status
if ($action == 'approve') {
    $status = 'Approved';
} elseif ($action == 'decline') {
    $status = 'Declined';
} else {
    die("❌ Invalid action.");
}

// Update the request status
$sql = "UPDATE leave_request SET status='$status' WHERE id=$id";
$conn->query($sql);

header("Location: dashboard_manager.php");
exit();
?>
