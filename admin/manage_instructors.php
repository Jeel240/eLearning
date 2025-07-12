<?php
session_start();
require '../config.php';

// Add instructor
if (isset($_POST['add_instructor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO instructors (name, specialization, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $specialization, $image);
    $stmt->execute();
    header("Location: manage_instructors.php");
    exit();
}

// Delete instructor
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM instructors WHERE id = $id");
    header("Location: manage_instructors.php");
    exit();
}

// Update instructor
if (isset($_POST['update_instructor'])) {
    $id = $_POST['instructor_id'];
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $conn->query("UPDATE instructors SET name='$name', specialization='$specialization', image='$image' WHERE id=$id");
    } else {
        $conn->query("UPDATE instructors SET name='$name', specialization='$specialization' WHERE id=$id");
    }
    header("Location: manage_instructors.php");
    exit();
}

$sql = "SELECT * FROM instructors ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Instructors</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f8f9fa;
    }

    .table-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ddd;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.04);
    }

    .btn-sm {
      margin-right: 6px;
    }

    .table td, .table th {
      vertical-align: middle;
    }

    @media (max-width: 576px) {
      .table th, .table td {
        font-size: 14px;
      }

      .page-header h3 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="page-header mb-4">
    <h3 class="mb-2">👨‍🏫 Manage Instructors</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addForm">+ Add New Instructor</button>
  </div>

  <!-- Add Instructor Form -->
  <div class="collapse mb-4" id="addForm">
    <div class="card card-body">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_instructor" value="1">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Specialization</label>
            <input name="specialization" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control" required>
          </div>
        </div>
        <button class="btn btn-success">Add Instructor</button>
      </form>
    </div>
  </div>

  <!-- Table Section -->
  <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Specialization</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($instructor = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($instructor['name']); ?></td>
            <td><?= htmlspecialchars($instructor['specialization']); ?></td>
            <td><img src="../uploads/<?= htmlspecialchars($instructor['image']); ?>" class="table-img" alt="Instructor"></td>
            <td>
              <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#editForm<?= $instructor['id']; ?>">Edit</button>
              <a href="?delete=<?= $instructor['id']; ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</a>
            </td>
          </tr>
          <tr class="collapse" id="editForm<?= $instructor['id']; ?>">
            <td colspan="4">
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_instructor" value="1">
                <input type="hidden" name="instructor_id" value="<?= $instructor['id']; ?>">
                <div class="row">
                  <div class="col-md-4 mb-2">
                    <input name="name" value="<?= htmlspecialchars($instructor['name']); ?>" class="form-control" required>
                  </div>
                  <div class="col-md-4 mb-2">
                    <input name="specialization" value="<?= htmlspecialchars($instructor['specialization']); ?>" class="form-control" required>
                  </div>
                  <div class="col-md-4 mb-2">
                    <input type="file" name="image" class="form-control">
                  </div>
                </div>
                <button class="btn btn-success btn-sm">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No instructors found.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

