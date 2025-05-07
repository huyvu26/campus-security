<?php
session_start();
include 'db.php';
?>

<h2>Login</h2>
<form method="post">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="staff">Security Staff</option>
        <option value="manager">Manager</option>
    </select><br><br>

    <button type="submit" name="login">Login</button>
</form>

<?php
if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if ($role == "staff") {
        $sql = "SELECT * FROM security_staff WHERE email='$email'";
        $redirect = "dashboard_staff.php";
    } else {
        $sql = "SELECT * FROM manager WHERE email='$email'";
        $redirect = "dashboard_manager.php";
    }

    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['name'];
            $_SESSION['role'] = $role;
            header("Location: $redirect");
            exit();
        } else {
            echo "<p>❌ Incorrect password.</p>";
        }
    } else {
        echo "<p>❌ User not found.</p>";
    }
}
?>
