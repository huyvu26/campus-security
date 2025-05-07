<?php include 'db.php'; ?>

<h2>Register</h2>
<form method="post">
    <input type="text" name="name" placeholder="Name" required><br><br>
    <input type="text" name="identity_number" placeholder="Identity Number" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="staff">Security Staff</option>
        <option value="manager">Manager</option>
    </select><br><br>

    <button type="submit" name="register">Register</button>
</form>

<?php
if (isset($_POST['register'])) {
    $name            = $_POST['name'];
    $identity_number = $_POST['identity_number'];
    $email           = $_POST['email'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure
    $role            = $_POST['role'];

    if ($role == "staff") {
        $sql = "INSERT INTO security_staff (name, identity_number, email, password) 
                VALUES ('$name', '$identity_number', '$email', '$password')";
    } else if ($role == "manager") {
        $sql = "INSERT INTO manager (name, identity_number, email, password) 
                VALUES ('$name', '$identity_number', '$email', '$password')";
    }

    if ($conn->query($sql)) {
        echo "<p>✅ Registered successfully!</p>";
    } else {
        echo "<p>❌ Error: " . $conn->error . "</p>";
    }
}
?>
