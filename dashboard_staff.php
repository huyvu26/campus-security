<?php
session_start();
include 'db.php';

// Check if user is logged in and is a staff member
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// Get staff name from session
$staff_name = $_SESSION['user'];

// Get staff ID from database
$staff_query = $conn->query("SELECT id FROM security_staff WHERE name = '$staff_name'");
$staff = $staff_query->fetch_assoc();
$staff_id = $staff['id'];

echo "<h2>Welcome, Security Staff: $staff_name</h2>";
echo "<a href='logout.php'>Logout</a><hr>";

// Show upcoming duties
$duties = $conn->query("SELECT * FROM duty WHERE staff_id = $staff_id AND duty_date >= CURDATE() ORDER BY duty_date ASC");

echo "<h3>Upcoming Duties (Next 7 Days)</h3>";
if ($duties->num_rows > 0) {
    echo "<ul>";
    while ($row = $duties->fetch_assoc()) {
        echo "<li>{$row['duty_date']} | {$row['start_time']} - {$row['end_time']} at {$row['place']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No duties scheduled.</p>";
}

// Show leave request history
$leaves = $conn->query("SELECT * FROM leave_request WHERE staff_id = $staff_id ORDER BY leave_date DESC");

echo "<h3>Your Leave Requests</h3>";
if ($leaves->num_rows > 0) {
    echo "<table border='1'><tr><th>Date</th><th>Type</th><th>Status</th></tr>";
    while ($row = $leaves->fetch_assoc()) {
        echo "<tr><td>{$row['leave_date']}</td><td>{$row['type']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No leave requests submitted.</p>";
}

// Show total approved leaves
$leave_count = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $staff_id AND status = 'Approved'");
$leave_total = $leave_count->fetch_assoc()['total'];

echo "<p><strong>Total Approved Leaves Taken:</strong> $leave_total</p>";
?>
<?php
echo "<hr><h3>Monthly Salary Calculation</h3>";

// Settings
$salary_per_duty = 100000; // VND
$required_duties = 15;
$max_allowed_leaves = 3;
$leave_penalty = 50000; // per extra leave

// Get current month and year
$month = date('m');
$year = date('Y');

// Get duties done this month
$duty_result = $conn->query("SELECT COUNT(*) AS total FROM duty 
    WHERE staff_id = $staff_id 
    AND MONTH(duty_date) = $month 
    AND YEAR(duty_date) = $year");
$duties_done = $duty_result->fetch_assoc()['total'];

// Get approved leaves this month
$leave_result = $conn->query("SELECT COUNT(*) AS total FROM leave_request 
    WHERE staff_id = $staff_id 
    AND status = 'Approved'
    AND MONTH(leave_date) = $month 
    AND YEAR(leave_date) = $year");
$approved_leaves = $leave_result->fetch_assoc()['total'];

// Base salary from duties
$salary = $duties_done * $salary_per_duty;

// Deduct fine if leaves exceed allowed
$extra_leaves = max(0, $approved_leaves - $max_allowed_leaves);
$penalty = $extra_leaves * $leave_penalty;

// Final salary
$final_salary = $salary - $penalty;

echo "<p>Duties Completed: $duties_done</p>";
echo "<p>Approved Leaves: $approved_leaves</p>";
echo "<p>Extra Leave Penalty: " . number_format($penalty) . " VND</p>";
echo "<p><strong>Total Salary: " . number_format($final_salary) . " VND</strong></p>";
?>

