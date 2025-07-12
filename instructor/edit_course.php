<?php
session_start();
$conn = new mysqli("localhost", "root", "", "moocs_db");

if (!isset($_SESSION['instructor_id'])) {
    die("Unauthorized access.");
}

$instructor_id = $_SESSION['instructor_id'];

if (!isset($_GET['id'])) {
    die("Invalid course.");
}

$course_id = $_GET['id'];

// Fetch course
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND instructor_id = ?");
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die("Course not found.");
}

// Update form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $status = $_POST['status'];

    $imageName = $course['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $imageName);
    }

    $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, price=?, duration=?, status=?, image=? WHERE id=? AND instructor_id=?");
    $stmt->bind_param("ssdsssii", $title, $description, $price, $duration, $status, $imageName, $course_id, $instructor_id);
    $stmt->execute();

    header("Location: manage_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      margin-top: 50px;
      max-width: 700px;
    }

    h2 {
      font-weight: 600;
      margin-bottom: 25px;
    }

    label {
      font-weight: 500;
    }

    img {
      border-radius: 6px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>✏️ Edit Course</h2>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Course Title</label>
      <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($course['description']) ?></textarea>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Price (₹)</label>
        <input type="number" name="price" value="<?= $course['price'] ?>" class="form-control">
      </div>
      <div class="col-md-6 mb-3">
        <label>Duration</label>
        <input type="text" name="duration" value="<?= htmlspecialchars($course['duration']) ?>" class="form-control">
      </div>
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="draft" <?= $course['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $course['status'] == 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Current Image</label><br>
      <?php if ($course['image']): ?>
        <img src="../uploads/<?= $course['image'] ?>" width="100" height="80" class="mb-2"><br>
      <?php endif; ?>
      <label>Change Image</label>
      <input type="file" name="image" class="form-control mt-1">
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="manage_courses.php" class="btn btn-outline-secondary">⬅️ Back</a>
      <button type="submit" class="btn btn-success">💾 Update Course</button>
    </div>
  </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
