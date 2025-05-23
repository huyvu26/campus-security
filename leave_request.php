<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    
    // Convert date to yyyy-mm-dd for database
    $date_parts = explode('/', $_POST['leave_date']);
    if (count($date_parts) === 3) {
        $leave_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    } else {
        $leave_date = null;
    }

    $type = $_POST['type'];
    $reason = $_POST['reason'];

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
        body { font-family: Arial; padding: 40px; background: #f5f7fa; }
        h2 { text-align: center; color: #333; }
        form {
            max-width: 500px;
            margin: auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        label { display: block; margin-top: 10px; }
        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            background-color: #108ABE;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .message { text-align: center; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>Submit Leave or Overtime Request</h2>
<?php if ($message) echo "<div class='message'>$message</div>"; ?>

<form method="post">
    <label>Select Staff:</label>
    <select name="staff_id" required>
        <option value="">-- Select Staff --</option>
        <?php
        $staffs = $conn->query("SELECT id, name, identity_number FROM security_staff");
        while ($row = $staffs->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']} (ID: {$row['identity_number']})</option>";
        }
        ?>
    </select>

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
</form>

<!-- Flatpickr Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  flatpickr(".datepicker", {
    dateFormat: "d/m/Y",
    minDate: "today"
  });
</script>

</body>
</html>
