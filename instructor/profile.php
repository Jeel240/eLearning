<?php
session_start();
include '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$id = $_SESSION['instructor_id'];
$message = '';

// Fetch instructor details
$query = $conn->query("SELECT * FROM instructors WHERE id = $id");
$instructor = $query->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $bio = trim($_POST['bio']);
    $email = trim($_POST['email']);

    // Handle image upload if any
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $conn->query("UPDATE instructors SET name='$name', specialization='$specialization', image='$image', email='$email' WHERE id=$id");
    } else {
        $conn->query("UPDATE instructors SET name='$name', specialization='$specialization', email='$email' WHERE id=$id");
    }
    $message = "Profile updated successfully.";
    header("Refresh:1");
}

// Handle password update
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $instructor['password'])) {
        $message = "Incorrect current password.";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $conn->query("UPDATE instructors SET password='$newHash' WHERE id=$id");
        $message = "Password updated successfully.";
    }
    header("Refresh:1");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instructor Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-top: 40px;
      max-width: 900px;
    }

    .form-label {
      font-weight: 500;
    }

    img.profile-img {
      border: 2px solid #ccc;
      border-radius: 8px;
      max-width: 100px;
      height: auto;
    }

    h3, h4 {
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h3>👤 My Profile</h3>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- Profile Update Form -->
  <form method="POST" enctype="multipart/form-data" class="mb-5">
    <input type="hidden" name="update_profile" value="1">
    
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($instructor['name']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($instructor['email']) ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Specialization</label>
      <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($instructor['specialization']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Bio</label>
      <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($instructor['bio'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Profile Image</label>
      <input type="file" name="image" class="form-control">
      <?php if (!empty($instructor['image'])): ?>
        <div class="mt-2">
          <img src="../uploads/<?= $instructor['image'] ?>" class="profile-img" alt="Profile Image">
        </div>
      <?php endif; ?>
    </div>

    <button class="btn btn-primary">💾 Update Profile</button>
  </form>

  <!-- Password Change -->
  <h4>🔒 Change Password</h4>
  <form method="POST">
    <input type="hidden" name="update_password" value="1">

    <div class="mb-3">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" name="new_password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>

    <button class="btn btn-success">🔁 Change Password</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
