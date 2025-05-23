<?php
ini_set('session.gc_maxlifetime', 3600);  // 1 hour
session_set_cookie_params(3600);          // 1 hour cookie

session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Make sure role is selected
    if (empty($role)) {
        $error = "❌ Please select a role.";
    } else {
        // Set query and redirect based on role
        if ($role == "staff") {
            $sql = "SELECT * FROM security_staff WHERE email='$email'";
            $redirect = "dashboard_staff.php";
        } elseif ($role == "manager") {
            $sql = "SELECT * FROM manager WHERE email='$email'";
            $redirect = "monitor.php";  // ✅ your updated dashboard
        }

        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['name'];
                $_SESSION['role'] = $role;
                $_SESSION['staff_id'] = $row['id'];
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
<html>
<head>
    <style>
        * { margin: 0; padding: 0; }
        body {
            background: radial-gradient(circle, rgba(209, 203, 203, 1) 0%, rgba(148, 187, 233, 1) 100%);
            font-family: Arial;
        }
        h1 {
            text-align: center;
            padding-top: 50px;
            padding-bottom: 50px;
            font-size: 50px;
            color: #006d77;
        }
        h3 {
            text-align: center;
            padding-top: 30px;
            font-size: x-large;
        }
        form {
            border: 4px solid rgb(169, 84, 0);
            border-radius: 15px;
            margin: auto;
            width: 400px;
            height: 500px;
            box-shadow: 10px 10px 10px rgb(169, 84, 0);
            background-color: rgb(240, 233, 228);
        }
        input, select, button {
            display: block;
            height: 40px;
            width: 90%;
            margin: 10px auto;
            padding: 0 10px;
            border: 1px solid blue;
            border-radius: 25px;
            box-sizing: border-box;
        }
        button {
            background-color: #108ABE;
            color: white;
            font-weight: bold;
        }
        #registerbtn {
            width: 50%;
            background-color: #70acb3;
        }
        .dashed-line {
            width: 85%;
            height: 1px;
            border-top: 2px dashed #c4801b;
            margin: 20px auto;
        }
        .error {
            text-align: center;
            color: red;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Campus Security Staff Management System</h1>

    <form method="post">
        <h3>Login</h3>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="staff">Security Staff</option>
            <option value="manager">Manager</option>
        </select>
        <button type="submit" name="login">Enter</button>
        <div class="dashed-line"></div>
        <a href="register.php"><button type="button" id="registerbtn">Register an account</button></a>
    </form>
</body>
</html>
