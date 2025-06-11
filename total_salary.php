<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != "manager") {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['manager_id'];
$salary_per_duty = 500000;
$overtime_bonus = 100000;
$leave_penalty = 100000;
$max_allowed_leaves = 3;
$month = date('m');
$year = date('Y');
$total_cost = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Total Monthly Salary</title>
  <style>
    body { font-family: Arial; margin: 0; background-color: #f2f4f8; }
    .container { display: flex; }
    .sidebar {
      width: 220px;
      background-color: #1e293b;
      color: white;
      padding: 20px;
      height: 100vh;
    }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin: 15px 0;
      padding: 8px;
      border-radius: 5px;
    }
    .sidebar a:hover { background-color: #334155; }

    .main {
      flex: 1;
      padding: 30px;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f1f5f9;
    }

    tr:last-child td {
      font-weight: bold;
      background-color: #f9fafb;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <h2>Manager</h2>
    <a href="monitor.php">Dashboard</a>
    <a href="create_duty.php">Assign Duty</a>
    <a href="assign_staff.php">Assign Staff</a>
    <a href="total_salary.php">Total Salary</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="main">
    <h2>Total Monthly Salary (<?= date('F Y') ?>)</h2>
    <table>
      <tr>
        <th>Staff</th>
        <th>Duties</th>
        <th>Approved Leave</th>
        <th>Approved OT</th>
        <th>Penalty</th>
        <th>Salary</th>
      </tr>
      <?php
      $staff_result = $conn->query("SELECT id, name FROM security_staff WHERE manager_id = $manager_id");
      while ($staff = $staff_result->fetch_assoc()) {
          $id = $staff['id'];
          $name = $staff['name'];

          $duties = $conn->query("SELECT COUNT(*) AS total FROM duty WHERE staff_id = $id AND MONTH(duty_date) = $month AND YEAR(duty_date) = $year")->fetch_assoc()['total'];
          $leaves = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $id AND type = 'Leave' AND status = 'Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];
          $ots = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $id AND type = 'Overtime' AND status = 'Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];

          $base = $duties * $salary_per_duty;
          $bonus = $ots * $overtime_bonus;
          $penalty = max(0, $leaves - $max_allowed_leaves) * $leave_penalty;
          $final = $base + $bonus - $penalty;
          $total_cost += $final;

          echo "<tr>
                  <td>$name</td>
                  <td>$duties</td>
                  <td>$leaves</td>
                  <td>$ots</td>
                  <td>" . number_format($penalty) . " VND</td>
                  <td><strong>" . number_format($final) . " VND</strong></td>
                </tr>";
      }
      ?>
      <tr>
        <td colspan="5">Total Monthly Salary to Pay</td>
        <td><strong style="color:#0d47a1;"><?= number_format($total_cost) ?> VND</strong></td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>
