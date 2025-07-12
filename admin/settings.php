<?php
session_start();
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $contact_email = $_POST['contact_email'];

    mysqli_query($conn, "UPDATE settings SET site_name='$site_name', contact_email='$contact_email' WHERE id=1");
    $success = "Settings updated.";
}

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .settings-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.06);
        }
        h3 {
            color: #333;
        }
    </style>
</head>
<body class="p-3 p-md-5">
    <div class="container">
        <h3 class="mb-4">⚙️ Site Settings</h3>

        <?php if (isset($success)) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="settings-card">
            <form method="POST">
                <div class="mb-3">
                    <label for="siteName" class="form-label">Site Name</label>
                    <input type="text" name="site_name" id="siteName" class="form-control" 
                           value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="contactEmail" class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" id="contactEmail" class="form-control" 
                           value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">💾 Update Settings</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
