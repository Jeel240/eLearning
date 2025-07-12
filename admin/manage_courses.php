<?php
session_start();
include '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}
$instructor_id = $_SESSION['instructor_id'];

// Fetch courses
$sql = "SELECT 
            c.*, 
            i.name AS instructor_name, 
            COUNT(e.id) AS enrolled_count
        FROM courses c
        JOIN instructors i ON c.instructor_id = i.id
        LEFT JOIN enrollments e ON c.id = e.course_id AND e.payment_status = 'Success'
        GROUP BY c.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Courses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .table-img {
      width: 60px;
      height: 40px;
      object-fit: cover;
      border-radius: 6px;
    }
    .truncate-text {
      max-width: 160px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .page-title {
      font-weight: 600;
      margin-bottom: 20px;
    }
    .table-responsive {
      border-radius: 8px;
      overflow-x: auto;
      background-color: white;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    @media (max-width: 768px) {
      .table th, .table td {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
<div class="container my-5">
  <h2 class="page-title text-center text-md-start">📚 Manage Courses</h2>

  <div class="table-responsive">
    <?php if ($result && $result->num_rows > 0): ?>
      <table class="table table-striped align-middle table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Instructor</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Price</th>
            <th>Enrolled</th>
            <th>Image</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td class="truncate-text"><?= htmlspecialchars($row['title']); ?></td>
            <td><?= htmlspecialchars($row['instructor_name']); ?></td>
            <td><?= $row['duration']; ?></td>
            <td>
              <span class="badge bg-<?= $row['status'] === 'published' ? 'success' : 'secondary'; ?>">
                <?= ucfirst($row['status']); ?>
              </span>
            </td>
            <td>₹<?= number_format($row['price'], 2); ?></td>
            <td><?= $row['enrolled_count']; ?></td>
            <td>
              <img src="../uploads/<?= htmlspecialchars($row['image']); ?>" class="table-img" alt="Course">
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info text-center">No courses available.</div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

