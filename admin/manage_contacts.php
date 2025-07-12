<?php
session_start();
include '../config.php';

// Ensure only admin can access
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Access Denied!'); window.location='../index.html';</script>";
    exit();
}

// Fetch messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>Contact Messages</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Received At</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['name']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
        <td><?= $row['created_at']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
