<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Handle status toggle
if (isset($_GET['toggle'])) {
    $course_id = $_GET['toggle'];
    $conn->query("UPDATE courses SET status = IF(status='published','draft','published') WHERE id = $course_id AND instructor_id = $instructor_id");
    header("Location: manage_courses.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];
    $conn->query("DELETE FROM courses WHERE id = $course_id AND instructor_id = $instructor_id");
    header("Location: manage_courses.php");
    exit;
}

// Get courses for this instructor
$result = $conn->query("SELECT * FROM courses WHERE instructor_id = $instructor_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instructor | Manage Courses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    h2 {
      font-weight: 600;
    }

    .table img {
      object-fit: cover;
      border-radius: 6px;
    }

    .modal-title {
      font-weight: 600;
    }

    @media (max-width: 576px) {
      .table thead {
        display: none;
      }
      .table tbody tr {
        display: block;
        margin-bottom: 1rem;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        border-radius: 10px;
        padding: 10px;
      }
      .table td {
        display: flex;
        justify-content: space-between;
        padding: 8px 10px;
        border: none;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="mb-4 text-center">🎓 My Courses</h2>
  <div class="text-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">+ Add Course</button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered align-middle bg-white">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Price (₹)</th>
          <th>Duration</th>
          <th>Image</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= $row['price'] ?></td>
          <td><?= htmlspecialchars($row['duration']) ?></td>
          <td>
            <?php if ($row['image']): ?>
              <img src="../uploads/<?= $row['image'] ?>" width="60" height="50" alt="Course Image">
            <?php else: ?> N/A <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($row['status'])): ?>
              <a href="?toggle=<?= $row['id'] ?>" class="btn btn-sm <?= $row['status'] == 'published' ? 'btn-success' : 'btn-secondary' ?>">
                <?= ucfirst($row['status']) ?>
              </a>
            <?php else: ?>
              <span class="badge bg-warning text-dark">No Status</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="edit_course.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this course?')" class="btn btn-sm btn-danger">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="add_course.php" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">📘 Add New Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Price (₹)</label>
            <input type="number" name="price" class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Duration</label>
            <input type="text" name="duration" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Course Image</label>
          <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Videos (ZIP/MP4)</label>
          <input type="file" name="videos[]" class="form-control" multiple>
        </div>

        <div class="mb-3">
          <label class="form-label">PDF Files</label>
          <input type="file" name="pdfs[]" class="form-control" multiple>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Course</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
