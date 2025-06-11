<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (empty($role)) {
        $error = "❌ Please select a role.";
    } else {
        if ($role == "staff") {
            $sql = "SELECT * FROM security_staff WHERE email='$email'";
            $redirect = "dashboard_staff.php";
        } elseif ($role == "manager") {
            $sql = "SELECT * FROM manager WHERE email='$email'";
            $redirect = "monitor.php";
        }

        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['name'];
                $_SESSION['role'] = $role;
                $_SESSION['staff_id'] = $row['id'];
                if ($role === "manager") {
                    $_SESSION['manager_id'] = $row['id'];
                }
                header("Location: $redirect");
                exit();
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Campus Security</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      background: white;
      padding: 40px 35px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    h1 {
      color: #0d47a1;
      font-size: 26px;
      margin-bottom: 30px;
    }
    input, select {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background-color: #108ABE;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #0b6c92;
    }
    .error {
      color: red;
      font-weight: bold;
      margin-top: 10px;
    }
    .register-link {
      display: block;
      margin-top: 25px;
      color: #108ABE;
      font-size: 15px;
      text-decoration: none;
    }
    .register-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h1>Campus Security Login</h1>
  <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

  <form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role" required>
      <option value="">Select Role</option>
      <option value="staff">Security Staff</option>
      <option value="manager">Manager</option>
    </select>
    <button type="submit" name="login">Login</button>
  </form>

  <a class="register-link" href="register.php">Don't have an account? Register</a>
</div>

</body>
</html>
