<?php
session_start();
require '../config.php';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $specialization = trim($_POST['specialization']);
    $image = $_FILES['image']['name'];

    // Upload Image
    $target = "uploads/" . basename($image);
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Creates the folder if it doesn't exist
    }

    $target = $uploadDir . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Failed to upload image.";
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO instructors (name, email, password, specialization, image, rating) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sssss", $name, $email, $password, $specialization, $image);

    if ($stmt->execute()) {
        $_SESSION['instructor_id'] = $conn->insert_id;
        header("Location: instructor_dashboard.php");
    } else {
        $error = "Registration failed. Email may already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f3f6;
            font-family: 'Segoe UI', sans-serif;
        }

        .register-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #198754;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 25px;
        }

        .btn-success {
            width: 100%;
            padding: 10px;
        }

        @media (max-width: 576px) {
            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="container register-container">
    <h2 class="form-title text-center">📋 Instructor Registration</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="example@domain.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Choose a secure password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Specialization</label>
            <input type="text" name="specialization" class="form-control" placeholder="E.g. Data Science, Web Development" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="image" class="form-control" required>
        </div>
        <button name="register" class="btn btn-success">Register as Instructor</button>
    </form>
</div>

</body>
</html>
