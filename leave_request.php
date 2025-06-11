<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['user'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $reason = $_POST['reason'];

    // Convert dd/mm/yyyy to yyyy-mm-dd
    $date_parts = explode('/', $_POST['leave_date']);
    if (count($date_parts) === 3) {
        $leave_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    } else {
        $leave_date = null;
    }

    if ($leave_date) {
        $sql = "INSERT INTO leave_request (staff_id, leave_date, type, reason, status)
                VALUES ('$staff_id', '$leave_date', '$type', '$reason', 'Pending')";
        if ($conn->query($sql)) {
            $message = "<p style='color:green;'>✅ Request submitted successfully.</p>";
        } else {
            $message = "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Invalid date format.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Leave or Overtime Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f7fa;
        }
        form {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
            display: block;
        }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .readonly {
            background: #f0f0f0;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
        }
        button {
            background-color: #108ABE;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h2>Submit Leave or Overtime Request</h2>
<?php if ($message) echo "<div class='message'>$message</div>"; ?>

<form method="post">
    <label>Staff:</label>
    <?php
// Get the staff's identity_number for display
    $result = $conn->query("SELECT identity_number FROM security_staff WHERE id = $staff_id");
    $identity = $result->fetch_assoc()['identity_number'];
    ?>
    <div class="readonly"><?= htmlspecialchars($staff_name) ?> (ID: <?= htmlspecialchars($identity) ?>)</div>


    <label>Date:</label>
    <input type="text" name="leave_date" class="datepicker" placeholder="dd/mm/yyyy" required>

    <label>Request Type:</label>
    <select name="type" required>
        <option value="">Select Type</option>
        <option value="Leave">Leave</option>
        <option value="Overtime">Overtime</option>
    </select>

    <label>Reason:</label>
    <input type="text" name="reason" placeholder="Reason (optional)">

    <button type="submit">Submit Request</button>
    <div style="text-align: center; margin-top: 10px;">
      <a href="dashboard_staff.php" style="text-decoration: none; color: #108ABE; font-weight: bold;">
           Back to Dashboard
      </a>
    </div>
</form>

<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  flatpickr(".datepicker", {
    dateFormat: "d/m/Y",
    minDate: "today"
  });
</script>

</body>
</html>
