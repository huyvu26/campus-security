<?php
include 'db.php';

if (isset($_POST['register'])) {
    $first_name      = $_POST['first_name'];
    $last_name       = $_POST['last_name'];
    $name            = $first_name . ' ' . $last_name;
    $identity_number = $_POST['identity_number'];
    $email           = $_POST['email'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender          = $_POST['gender'];
    $dob             = $_POST['dob'];
    $role            = $_POST['role'];

    if ($role == "staff") {
        $sql = "INSERT INTO security_staff (name, identity_number, email, password, gender, dob)
                VALUES ('$name', '$identity_number', '$email', '$password', '$gender', '$dob')";
    } else if ($role == "manager") {
        $sql = "INSERT INTO manager (name, identity_number, email, password, gender, dob)
                VALUES ('$name', '$identity_number', '$email', '$password', '$gender', '$dob')";
    }

    if ($conn->query($sql)) {
        $message = "<p class='success'>✅ Registered successfully!</p>";
    } else {
        $message = "<p class='error'>❌ Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register - Campus Security</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .form-box {
      background-color: white;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 450px;
    }

    .form-box h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #0d47a1;
    }

    input, select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    .radio-group {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .radio-group label {
      display: flex;
      align-items: baseline; /* change from center to baseline */
      gap: 8px;
      font-size: 14px;
      line-height: 1.5;
}
    .radio-group input[type="radio"] {
      transform: translateY(1px); /* fine-tune if needed */
}

    button {
      width: 100%;
      padding: 12px;
      background-color: #108ABE;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0b6c92;
    }

    .error, .success {
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .error { color: red; }
    .success { color: green; }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #108ABE;
      font-size: 14px;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h1>Register</h1>
  <?php if (isset($message)) echo $message; ?>

  <form method="post">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name">
    <input type="text" name="identity_number" placeholder="Identity Number" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="date" name="dob" required>

    <div class="radio-group">
      <label><input type="radio" name="gender" value="male" required> Male</label>
      <label><input type="radio" name="gender" value="female" required> Female</label>
    </div>

    <select name="role" required>
      <option value="">Select Role</option>
      <option value="staff">Security Staff</option>
      <option value="manager">Manager</option>
    </select>

    <button type="submit" name="register">Register</button>
  </form>

  <a href="login.php" class="back-link">Back to Login</a>
</div>

</body>
</html>
