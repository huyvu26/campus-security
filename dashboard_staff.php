<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staff_name = $_SESSION['user'];
$staff_id = $_SESSION['staff_id'];
$week_offset = isset($_GET['week_offset']) ? (int)$_GET['week_offset'] : 0;

// Calculate Monday to Sunday range
$monday = date('Y-m-d', strtotime("monday +{$week_offset} week"));
$sunday = date('Y-m-d', strtotime("sunday +{$week_offset} week"));

$salary_per_duty = 500000;
$overtime_bonus = 100000;
$max_allowed_leaves = 3;
$leave_penalty = 100000;

// Duties in the selected week
$duties_result = $conn->query("SELECT * FROM duty 
    WHERE staff_id = $staff_id 
    AND duty_date BETWEEN '$monday' AND '$sunday' 
    ORDER BY duty_date ASC");
$duties_list = [];
while ($row = $duties_result->fetch_assoc()) {
    $duties_list[] = $row;
}

// Stats
$today = date('Y-m-d');
$total_jobs = count($duties_list);
$active_staff = $conn->query("SELECT COUNT(*) AS total FROM security_staff")->fetch_assoc()['total'];
$completed_today = $conn->query("SELECT COUNT(*) AS total FROM duty WHERE staff_id = $staff_id AND duty_date = '$today'")->fetch_assoc()['total'];

// Salary
$month = date('m');
$year = date('Y');
$duties_done = $conn->query("SELECT COUNT(*) AS total FROM duty WHERE staff_id = $staff_id AND MONTH(duty_date) = $month AND YEAR(duty_date) = $year")->fetch_assoc()['total'];
$approved_leaves = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $staff_id AND type = 'Leave' AND status = 'Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];
$approved_ot = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $staff_id AND type = 'Overtime' AND status = 'Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];

$base_salary = $duties_done * $salary_per_duty;
$bonus = $approved_ot * $overtime_bonus;
$extra_leaves = max(0, $approved_leaves - $max_allowed_leaves);
$penalty = $extra_leaves * $leave_penalty;
$final_salary = $base_salary + $bonus - $penalty;

$requests = $conn->query("SELECT * FROM leave_request WHERE staff_id = $staff_id ORDER BY leave_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Staff Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { margin: 0; font-family: 'Inter', sans-serif; background: linear-gradient(to right, #e0eafc, #cfdef3); display: flex; min-height: 100vh; }
    .sidebar { width: 220px; background-color: white; padding: 30px 20px; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05); }
    .sidebar h2 { font-size: 20px; margin-bottom: 30px; }
    .sidebar a { display: block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
    .dashboard { flex: 1; padding: 30px; }
    .dashboard h1 { font-size: 26px; margin-bottom: 25px; }
    .week-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 15px; }
    .day-card { background-color: white; padding: 20px; border-radius: 15px; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); min-height: 150px; }
    .day-title { font-weight: 600; font-size: 16px; margin-bottom: 10px; color: #555; }
    .job-task { font-size: 14px; color: #333; line-height: 1.4; padding-left: 10px; position: relative; }
    .job-task::before { content: "•"; position: absolute; left: 0; color: #007bff; }
    .stats-bar { margin-top: 20px; display: flex; gap: 20px; }
    .stat-card { flex: 1; background-color: #fff; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08); }
    .stat-title { font-size: 14px; color: #777; }
    .stat-value { font-size: 24px; font-weight: bold; margin-top: 10px; color: #333; }
    .section { margin-top: 40px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: center; }
    .button { padding: 10px 15px; background: #108ABE; color: white; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 15px; }
    .week-nav { margin-top: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
    .week-nav a { padding: 6px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2><?= $staff_name ?></h2>
  <a href="leave_request.php">Request Leave/OT</a>
  <a href="logout.php">Logout</a>
</div>

<div class="dashboard">
  <h1>Weekly Duty Schedule (<?= date('d M', strtotime($monday)) ?> - <?= date('d M Y', strtotime($sunday)) ?>)</h1>

  <div class="week-nav">
    <a href="?week_offset=<?= $week_offset - 1 ?>">&larr; Previous</a>
    <a href="?week_offset=<?= $week_offset + 1 ?>">Next &rarr;</a>
  </div>

  <div class="stats-bar">
    <div class="stat-card"><div class="stat-title">Total Jobs</div><div class="stat-value"><?= $total_jobs ?></div></div>
    <div class="stat-card"><div class="stat-title">Active Staff</div><div class="stat-value"><?= $active_staff ?></div></div>
    <div class="stat-card"><div class="stat-title">Completed Today</div><div class="stat-value"><?= $completed_today ?></div></div>
  </div>

  <div class="week-grid section">
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("$monday +$i days"));
        $day_label = $days[$i];
        echo "<div class='day-card'><div class='day-title'>$day_label (" . date('d/m', strtotime($date)) . ")</div>";
        $found = false;
        foreach ($duties_list as $d) {
            if ($d['duty_date'] == $date) {
                echo "<div class='job-task'>{$d['place']} - {$d['start_time']}</div>";
                $found = true;
            }
        }
        if (!$found) echo "<div class='job-task'>No jobs scheduled</div>";
        echo "</div>";
    }
    ?>
  </div>

  <div class="section">
    <h2>Your Leave & OT Requests</h2>
    <?php if ($requests->num_rows > 0): ?>
      <table><tr><th>Date</th><th>Type</th><th>Status</th></tr>
      <?php while ($r = $requests->fetch_assoc()): ?>
        <tr><td><?= $r['leave_date'] ?></td><td><?= $r['type'] ?></td><td><?= $r['status'] ?></td></tr>
      <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No requests found.</p>
    <?php endif; ?>
  </div>

  <div class="section">
    <h2>Monthly Salary</h2>
    <p>Duties Completed: <?= $duties_done ?></p>
    <p>Approved Leaves: <?= $approved_leaves ?></p>
    <p>Approved Overtime: <?= $approved_ot ?></p>
    <p>Overtime Bonus: <?= number_format($bonus) ?> VND</p>
    <p>Penalty: <?= number_format($penalty) ?> VND</p>
    <p><strong>Total Salary: <?= number_format($final_salary) ?> VND</strong></p>
  </div>
</div>

</body>
</html>
