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

// This week range
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

// Duties this week
$duties_result = $conn->query("SELECT * FROM duty WHERE staff_id = $staff_id AND duty_date BETWEEN '$monday' AND '$sunday'");
$duties_list = [];
while ($row = $duties_result->fetch_assoc()) {
    $duties_list[] = $row;
}

// Stats
$active_staff = $conn->query("SELECT COUNT(*) AS total FROM security_staff")->fetch_assoc()['total'];

// Monthly Salary
$salary_per_duty = 500000;
$overtime_bonus = 100000;
$leave_penalty = 100000;
$max_allowed_leaves = 3;
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

$leaves_taken = $approved_leaves;
$leaves_remaining = max(0, $max_allowed_leaves - $leaves_taken);

$requests = $conn->query("SELECT * FROM leave_request WHERE staff_id = $staff_id ORDER BY leave_date DESC");

// Salary History for Chart
$salary_history = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));

    $duties = $conn->query("SELECT COUNT(*) AS total FROM duty WHERE staff_id = $staff_id AND MONTH(duty_date) = $m AND YEAR(duty_date) = $y")->fetch_assoc()['total'];
    $leaves = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $staff_id AND type='Leave' AND status='Approved' AND MONTH(leave_date) = $m AND YEAR(leave_date) = $y")->fetch_assoc()['total'];
    $ots = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $staff_id AND type='Overtime' AND status='Approved' AND MONTH(leave_date) = $m AND YEAR(leave_date) = $y")->fetch_assoc()['total'];

    $base = $duties * $salary_per_duty;
    $ot_bonus = $ots * $overtime_bonus;
    $pen = max(0, $leaves - $max_allowed_leaves) * $leave_penalty;
    $final = $base + $ot_bonus - $pen;

    $salary_history[] = [
        'label' => $label,
        'amount' => $final
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Staff Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: center; }
    th { background-color: #f1f5f9; font-weight: 600; }
    .stat { font-size: 16px; margin: 8px 0; }
    .stat strong { color: #1e293b; }
    .highlight { font-weight: bold; color: #0d47a1; }
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

  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-title">Active Staff</div>
      <div class="stat-value"><?= $active_staff ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-title">Leaves Taken (This Month)</div>
      <div class="stat-value"><?= $leaves_taken ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-title">Leaves Remaining</div>
      <div class="stat-value"><?= $leaves_remaining ?></div>
    </div>
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
                echo "<div class='job-task'>{$d['place']} - {$d['start_time']} to {$d['end_time']}</div>";
                $found = true;
            }
        }
        if (!$found) echo "<div class='job-task'>No jobs scheduled</div>";
        echo "</div>";
    }
    ?>
  </div>

  <div class="section">
    <h2 style="cursor:pointer;" onclick="toggleRequests()">Your Leave & OT Requests <span id="toggle-icon">▼</span></h2>
    <div class="card" id="requests-section">
      <?php if ($requests->num_rows > 0): ?>
        <table>
          <tr><th>Date</th><th>Type</th><th>Status</th></tr>
          <?php while ($r = $requests->fetch_assoc()): ?>
            <tr><td><?= $r['leave_date'] ?></td><td><?= $r['type'] ?></td><td><?= $r['status'] ?></td></tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p>No requests found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleRequests() {
      const section = document.getElementById("requests-section");
      const icon = document.getElementById("toggle-icon");
      section.style.display = (section.style.display === "none") ? "block" : "none";
      icon.textContent = (icon.textContent === "▼") ? "▲" : "▼";
    }
  </script>

  <div class="section">
    <h2>Monthly Salary</h2>
    <div class="card">
      <p class="stat">Duties Completed: <strong><?= $duties_done ?></strong></p>
      <p class="stat">Approved Leaves: <strong><?= $approved_leaves ?></strong></p>
      <p class="stat">Approved Overtime: <strong><?= $approved_ot ?></strong></p>
      <p class="stat">Overtime Bonus: <strong><?= number_format($bonus) ?> VND</strong></p>
      <p class="stat">Penalty: <strong><?= number_format($penalty) ?> VND</strong></p>
      <p class="stat highlight">Total Salary: <strong><?= number_format($final_salary) ?> VND</strong></p>
    </div>

    <canvas id="salaryChart" height="100"></canvas>
    <script>
      const ctx = document.getElementById('salaryChart').getContext('2d');
      const salaryChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: <?= json_encode(array_column($salary_history, 'label')) ?>,
          datasets: [{
            label: 'Monthly Salary (VND)',
            data: <?= json_encode(array_column($salary_history, 'amount')) ?>,
            borderColor: '#0d47a1',
            backgroundColor: 'rgba(13,71,161,0.08)',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => value.toLocaleString() + ' VND'
              }
            }
          }
        }
      });
    </script>
  </div>
</div>
</body>
</html>
