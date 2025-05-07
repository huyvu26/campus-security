<?php
include 'db.php';

$result = $conn->query("SELECT lr.id, s.name, lr.leave_date, lr.type, lr.status 
                        FROM leave_request lr 
                        JOIN security_staff s ON lr.staff_id = s.id 
                        WHERE lr.status = 'Pending'");

echo "<h2>Pending Leave Requests</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>
            <tr><th>Staff</th><th>Date</th><th>Type</th><th>Status</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['leave_date']}</td>
                <td>{$row['type']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='update_leave.php?id={$row['id']}&status=Approved'>✅ Approve</a> |
                    <a href='update_leave.php?id={$row['id']}&status=Declined'>❌ Decline</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pending leave requests.</p>";
}
?>
