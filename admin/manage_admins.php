<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include '../config.php';

// Add new admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        echo "<script>alert('New admin added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding admin!');</script>";
    }
}

// Delete admin
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Admin deleted successfully!'); window.location='manage_admins.php';</script>";
    }
}

// Fetch all admins
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
</head>
<body>
    <h2>Manage Admins</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="New Admin Username" required>
        <input type="email" name="email" placeholder="New Admin Email" required>
        <input type="password" name="password" placeholder="New Admin Password" required>
        <button type="submit" name="add_admin">Add Admin</button>
    </form>

    <h3>Existing Admins</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while ($admin = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $admin['id']; ?></td>
            <td><?php echo $admin['username']; ?></td>
            <td><?php echo $admin['email']; ?></td>
            <td>
                <a href="manage_admins.php?delete_id=<?php echo $admin['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
