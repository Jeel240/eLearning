<?php
session_start();
require '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>👤 My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        .profile-heading {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #343a40;
        }

        .profile-label {
            font-weight: 500;
            color: #495057;
        }

        .profile-value {
            color: #212529;
            margin-bottom: 15px;
        }

        @media (max-width: 576px) {
            .profile-heading {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="container profile-container">
    <h2 class="profile-heading text-center">👤 My Profile</h2>

    <div>
        <p class="profile-label">Name:</p>
        <p class="profile-value"><?= htmlspecialchars($user['name']) ?></p>

        <p class="profile-label">Email:</p>
        <p class="profile-value"><?= htmlspecialchars($user['email']) ?></p>
    </div>
</div>

</body>
</html>
