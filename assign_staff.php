<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['manager_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'];
    $update = $conn->query("UPDATE security_staff SET manager_id = $manager_id WHERE id = $staff_id");
    $message = $update ? "<div class='success'>✅ Staff assigned successfully.</div>" : "<div class='error'>❌ Failed to assign staff.</div>";
}

$staff_result = $conn->query("SELECT id, name FROM security_staff WHERE manager_id IS NULL");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Staff</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: #ffffff;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    h2 {
      margin-bottom: 25px;
      font-size: 24px;
      color: #333;
    }

    select, button {
      width: 100%;
      padding: 12px;
      margin-top: 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
      box-sizing: border-box;
    }

    select:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .success {
      background-color: #d1e7dd;
      color: #0f5132;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }

    .error {
      background-color: #f8d7da;
      color: #842029;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Assign Staff</h2>
  <?= $message ?>

  <form method="post">
    <label for="staff_id">Select Staff:</label>
    <select name="staff_id" id="staff_id" required>
      <option value="">-- Choose Staff --</option>
      <?php while ($row = $staff_result->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?> (ID: <?= $row['id'] ?>)</option>
      <?php endwhile; ?>
    </select>

    <button type="submit">Assign</button>
  </form>
</div>

</body>
</html>
