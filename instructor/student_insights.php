<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

$courses = $conn->query("SELECT id, title FROM courses WHERE instructor_id = $instructor_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Insights</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 12px rgba(0,0,0,0.05);
      margin-top: 40px;
      max-width: 1000px;
    }

    h3 {
      font-weight: 600;
    }

    .progress-bar {
      background-color: #0d6efd;
    }

    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }

      table th, table td {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h3 class="mb-4">📊 Student Insights</h3>

  <form method="GET" class="mb-4">
    <label class="form-label">Select Course:</label>
    <select name="course_id" class="form-select mb-2" required>
      <option value="">-- Select Course --</option>
      <?php while ($c = $courses->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>" <?= (isset($_GET['course_id']) && $_GET['course_id'] == $c['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['title']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button class="btn btn-primary">View Insights</button>
  </form>

  <?php if (isset($_GET['course_id'])):
    $course_id = intval($_GET['course_id']);

    $stmt = $conn->prepare("
      SELECT u.name, u.email, e.completion_percent, r.rating, r.review 
      FROM enrollments e
      JOIN users u ON e.student_email = u.email
      LEFT JOIN reviews r ON r.course_id = e.course_id AND r.student_id = u.id
      WHERE e.course_id = ?
    ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $students = $stmt->get_result();
  ?>

  <h5 class="mb-3">📘 Enrolled Students (<?= $students->num_rows ?>)</h5>

  <?php if ($students->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Progress</th>
            <th>Rating</th>
            <th>Review</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($s = $students->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($s['name']) ?></td>
              <td><?= htmlspecialchars($s['email']) ?></td>
              <td>
                <?= $s['completion_percent'] ?>%
                <div class="progress mt-1" style="height: 8px;">
                  <div class="progress-bar" style="width: <?= $s['completion_percent'] ?>%"></div>
                </div>
              </td>
              <td><?= $s['rating'] ? '⭐ ' . $s['rating'] : '-' ?></td>
              <td><?= $s['review'] ? htmlspecialchars($s['review']) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">No students found for this course.</div>
  <?php endif; ?>

  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
