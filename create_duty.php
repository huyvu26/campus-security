<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != "manager") {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    
    // Convert from dd/mm/yyyy to yyyy-mm-dd
    $date_parts = explode('/', $_POST['duty_date']);
    if (count($date_parts) === 3) {
        $duty_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    } else {
        $message = "<p style='color:red;'>❌ Invalid date format.</p>";
    }

    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $place = $_POST['place'];

    $today = date('Y-m-d');
    $max_date = date('Y-m-d', strtotime('+7 days'));

    // 1️⃣ Date range validation
    if ($duty_date < $today || $duty_date > $max_date) {
        $message = "<p style='color:red;'>❌ Duty date must be within the next 7 days.</p>";
    } else {
        // 2️⃣ Leave conflict check
        $leave_check = $conn->query("SELECT * FROM leave_request 
            WHERE staff_id = $staff_id 
            AND leave_date = '$duty_date' 
            AND status = 'Approved'");

        if ($leave_check->num_rows > 0) {
            $message = "<p style='color:red;'>❌ Cannot assign duty on an approved leave/overtime date.</p>";
        } else {
            // 3️⃣ Overlapping time check
            $overlap_check = $conn->query("SELECT * FROM duty 
                WHERE staff_id = $staff_id 
                AND duty_date = '$duty_date' 
                AND (
                    (start_time < '$end_time' AND end_time > '$start_time')
                )");

            if ($overlap_check->num_rows > 0) {
                $message = "<p style='color:red;'>❌ Duty time overlaps with an existing duty.</p>";
            } else {
                // ✅ Insert duty
                $sql = "INSERT INTO duty (staff_id, duty_date, start_time, end_time, place)
                        VALUES ('$staff_id', '$duty_date', '$start_time', '$end_time', '$place')";
                if ($conn->query($sql)) {
                    $message = "<p style='color:green;'>✅ Duty assigned successfully.</p>";
                } else {
                    $message = "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Duty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { font-family: Arial; margin: 40px; background-color: #f4f6f8; }
        h2 { text-align: center; color: #333; }
        form {
            max-width: 400px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        label { display: block; margin-top: 10px; }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #108ABE;
            color: white;
            font-weight: bold;
            border: none;
        }
        .message { text-align: center; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>Create Duty Schedule</h2>
<?php if ($message) echo "<div class='message'>$message</div>"; ?>

<form method="post">
    <label>Select Staff:</label>
    <select name="staff_id" required>
        <option value="">-- Select Staff --</option>
        <?php
        $staffs = $conn->query("SELECT id, name FROM security_staff");
        while ($row = $staffs->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label>Duty Date:</label>
    <input type="text" name="duty_date" class="datepicker" required placeholder="dd/mm/yyyy">

    <label>Start Time:</label>
    <input type="text" name="start_time" class="timepicker" required placeholder="HH:MM">

    <label>End Time:</label>
    <input type="text" name="end_time" class="timepicker" required placeholder="HH:MM">

    <label>Place:</label>
    <input type="text" name="place" required placeholder="e.g. Gate A, Lobby">

    <button type="submit">Assign Duty</button>
</form>

<!-- Flatpickr Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  flatpickr(".datepicker", {
    dateFormat: "d/m/Y",
    minDate: "today",
    maxDate: new Date().fp_incr(7)
  });

  flatpickr(".timepicker", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
  });
</script>

</body>
</html>
