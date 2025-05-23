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
        echo "<p style='text-align:center; color:green;'>✅ Registered successfully!</p>";
    } else {
        echo "<p style='text-align:center; color:red;'>❌ Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        * { margin: 0px; padding: 0px; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }

        .form-container { margin-top: 50px; }
        .form-header { text-align: center; padding: 10px 0; }

        .mid-form {
            display: block;
            box-sizing: border-box;
            border: 4px solid rgb(169, 84, 0);
            border-radius: 15px;
            margin: auto;
            box-shadow: 10px 10px 10px rgb(169, 84, 0);
            width: 400px;
            background-color: rgb(240, 233, 228);
            padding: 30px 20px;
        }

        .form-group { padding: 10px 0; text-align: center; }
        input, select {
            height: 40px;
            width: 90%;
            margin-bottom: 10px;
            border: 1px solid black;
            border-radius: 15px;
            text-align: center;
        }

        .gender-container {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .gender-option {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid rgb(169, 84, 0);
            border-radius: 15px;
            background-color: #fff;
            cursor: pointer;
        }

        .submit-btn {
            text-align: center;
            margin-top: 20px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 15px;
            background-color: rgb(169, 84, 0);
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="mid-form">
        <div class="form-header">
            <h1>Register Form</h1>
        </div>
        <form method="post">
            <div class="form-group">
                <input type="text" name="first_name" placeholder="First name" required>
                <input type="text" name="last_name" placeholder="Last name">
            </div>
            <div class="form-group">
                <input type="text" name="identity_number" placeholder="Identity Number" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="date" name="dob" required>
            </div>
            <div class="gender-container">
                <label class="gender-option">
                    <input type="radio" name="gender" value="female" required> Female
                </label>
                <label class="gender-option">
                    <input type="radio" name="gender" value="male" required> Male
                </label>
            </div>
            <div class="form-group">
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="staff">Security Staff</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <div class="submit-btn">
                <button type="submit" name="register">Register</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
