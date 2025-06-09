<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
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

$selected_staff_id = isset($_GET['view_duties']) ? intval($_GET['view_duties']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manager Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { margin: 0; font-family: "Segoe UI", sans-serif; background-color: #f0f4f8; }
    .dashboard-container { display: flex; height: 100vh; }
    .sidebar { width: 220px; background-color: #1e293b; color: white; padding: 20px; }
    .sidebar h2 { font-size: 20px; margin-bottom: 30px; }
    .sidebar a { color: white; text-decoration: none; margin-bottom: 15px; display: block; padding: 8px 10px; border-radius: 5px; }
    .sidebar a:hover { background-color: #334155; }
    .dashboard { flex: 1; padding: 30px; overflow-y: auto; }
    .topbar { background-color: white; padding: 15px 30px; margin-bottom: 20px; font-weight: bold; font-size: 22px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .section { background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eaeaea; }
    th { background-color: #f8fafc; }
    tr:hover { background-color: #f1f5f9; }
    .action-link { text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 13px; }
    .approve { background-color: #22c55e; color: white; }
    .decline { background-color: #ef4444; color: white; }
  </style>
</head>
<body>

<div class="dashboard-container">
  <div class="sidebar">
    <h2>Manager</h2>
    <a href="monitor.php">Dashboard</a>
    <a href="create_duty.php">Assign Duty</a>
    <a href="assign_staff.php">Assign Staff</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="dashboard">
    <div class="topbar">Monitoring & Salary Dashboard</div>

    <!-- Salary Summary -->
    <div class="section">
      <h3>Monthly Salary Summary</h3>
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
            $approved_leaves = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $id AND type='Leave' AND status='Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];
            $approved_ot = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $id AND type='Overtime' AND status='Approved' AND MONTH(leave_date) = $month AND YEAR(leave_date) = $year")->fetch_assoc()['total'];

            $duties_salary = $duties * $salary_per_duty;
            $ot_salary = $approved_ot * $overtime_bonus;
            $extra_leaves = max(0, $approved_leaves - $max_allowed_leaves);
            $penalty = $extra_leaves * $leave_penalty;
            $final_salary = $duties_salary + $ot_salary - $penalty;

            echo "<tr>
                    <td><a href='?view_duties=$id'><strong>$name</strong></a></td>
                    <td>$duties</td>
                    <td>$approved_leaves</td>
                    <td>$approved_ot</td>
                    <td>" . number_format($penalty) . " VND</td>
                    <td><strong>" . number_format($final_salary) . " VND</strong></td>
                  </tr>";
        }
        ?>
      </table>
    </div>

    <!-- Optional Duties and Salary Chart -->
    <?php
    if ($selected_staff_id) {
        $staff = $conn->query("SELECT name FROM security_staff WHERE id = $selected_staff_id")->fetch_assoc();
        echo "<div class='section'>";
        echo "<h3>Assigned Duties for {$staff['name']}</h3>";

        $duties = $conn->query("SELECT duty_date, start_time, end_time, place FROM duty 
                                WHERE staff_id = $selected_staff_id 
                                ORDER BY duty_date ASC");

        if ($duties->num_rows > 0) {
            echo "<table>
                    <tr><th>Date</th><th>Start</th><th>End</th><th>Place</th></tr>";
            while ($d = $duties->fetch_assoc()) {
                echo "<tr>
                        <td>{$d['duty_date']}</td>
                        <td>{$d['start_time']}</td>
                        <td>{$d['end_time']}</td>
                        <td>{$d['place']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No duties assigned for this staff.</p>";
        }
        echo "</div>";

        // Get salary history
        $labels = [];
        $salary_data = [];

        for ($i = 5; $i >= 0; $i--) {
            $target_month = date('m', strtotime("-$i months"));
            $target_year = date('Y', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            $labels[] = $label;

            $duties = $conn->query("SELECT COUNT(*) AS total FROM duty WHERE staff_id = $selected_staff_id AND MONTH(duty_date) = $target_month AND YEAR(duty_date) = $target_year")->fetch_assoc()['total'];
            $ot = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $selected_staff_id AND type='Overtime' AND status='Approved' AND MONTH(leave_date) = $target_month AND YEAR(leave_date) = $target_year")->fetch_assoc()['total'];
            $leaves = $conn->query("SELECT COUNT(*) AS total FROM leave_request WHERE staff_id = $selected_staff_id AND type='Leave' AND status='Approved' AND MONTH(leave_date) = $target_month AND YEAR(leave_date) = $target_year")->fetch_assoc()['total'];

            $salary = ($duties * $salary_per_duty) + ($ot * $overtime_bonus) - (max(0, $leaves - $max_allowed_leaves) * $leave_penalty);
            $salary_data[] = $salary;
        }

        $json_labels = json_encode($labels);
        $json_data = json_encode($salary_data);

        echo "<div class='section'>
                <h3>Monthly Salary History</h3>
                <canvas id='salaryChart' width='400' height='150'></canvas>
              </div>";
    }
    ?>

    <!-- Leave/OT Approval Section -->
    <div class="section">
      <h3>Pending Leave/Overtime Requests</h3>
      <table>
        <tr><th>Staff</th><th>Date</th><th>Type</th><th>Action</th></tr>
        <?php
        $pending = $conn->query("SELECT lr.id, ss.name, lr.leave_date, lr.type 
                                 FROM leave_request lr 
                                 JOIN security_staff ss ON lr.staff_id = ss.id 
                                 WHERE lr.status = 'Pending' AND ss.manager_id = $manager_id 
                                 ORDER BY lr.leave_date DESC");

        if ($pending->num_rows > 0) {
            while ($row = $pending->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['leave_date']}</td>
                        <td>{$row['type']}</td>
                        <td>
                            <a class='action-link approve' href='update_leave.php?id={$row['id']}&status=Approved'>Approve</a>
                            <a class='action-link decline' href='update_leave.php?id={$row['id']}&status=Declined'>Decline</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No pending requests.</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>
</div>

<?php if ($selected_staff_id): ?>
<script>
  const ctx = document.getElementById('salaryChart').getContext('2d');
  const salaryChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= $json_labels ?>,
      datasets: [{
        label: 'Monthly Salary (VND)',
        data: <?= $json_data ?>,
        borderColor: '#108ABE',
        backgroundColor: 'rgba(16,138,190,0.1)',
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
<?php endif; ?>

</body>
</html>
