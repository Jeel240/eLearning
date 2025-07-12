<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$assignment_id = $_GET['assignment_id'] ?? 0;

// Fetch assignment & course info
$infoSql = "
    SELECT a.title AS assignment_title, c.title AS course_title 
    FROM assignments a 
    JOIN courses c ON a.course_id = c.id 
    WHERE a.id = ? AND c.instructor_id = ?
";
$stmt = $conn->prepare($infoSql);
$stmt->bind_param("ii", $assignment_id, $_SESSION['instructor_id']);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();

if (!$assignment) {
    echo "<h4>Invalid Assignment</h4>";
    exit();
}

// Fetch submissions
$sql = "
    SELECT s.*, u.name AS student_name, u.email 
    FROM assignment_submissions s 
    JOIN users u ON s.student_id = u.id 
    WHERE s.assignment_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$submissions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submissions - <?= htmlspecialchars($assignment['assignment_title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-top: 40px;
    }

    .btn-sm {
      font-size: 0.8rem;
      padding: 3px 8px;
    }

    @media (max-width: 768px) {
      table th, table td {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h4 class="mb-3">
    📝 <?= htmlspecialchars($assignment['assignment_title']) ?> 
    <small class="text-muted">(<?= htmlspecialchars($assignment['course_title']) ?>)</small>
  </h4>

  <a href="instructor_assignments.php" class="btn btn-outline-secondary mb-4">⬅ Back to Assignments</a>

  <?php if ($submissions->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Email</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          <?php $sn = 1; while ($row = $submissions->fetch_assoc()): ?>
            <tr>
              <td class="text-center"><?= $sn++ ?></td>
              <td><?= htmlspecialchars($row['student_name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td class="text-center"><?= ucfirst($row['status']) ?></td>
              <td><?= date("d M Y, h:i A", strtotime($row['submitted_at'])) ?></td>
              <td>
                <?= isset($row['grade']) && $row['grade'] !== '' ? htmlspecialchars($row['grade']) : 'Pending' ?>
                <?php if (!isset($row['grade']) || $row['grade'] === ''): ?>
                  <br>
                  <a href="grade_submission.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success mt-1">Grade</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No submissions yet.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
