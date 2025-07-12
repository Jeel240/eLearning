<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch instructor name
$stmt = $conn->prepare("SELECT name FROM instructors WHERE id = ?");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$instructor = $stmt->get_result()->fetch_assoc();

// Total Courses
$courseResult = $conn->prepare("SELECT COUNT(*) as total FROM courses WHERE instructor_id = ?");
$courseResult->bind_param("i", $instructor_id);
$courseResult->execute();
$totalCourses = $courseResult->get_result()->fetch_assoc()['total'];

// Total Students
$studentStmt = $conn->prepare("
    SELECT COUNT(DISTINCT e.student_email) as students
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE c.instructor_id = ?
");
$studentStmt->bind_param("i", $instructor_id);
$studentStmt->execute();
$totalStudents = $studentStmt->get_result()->fetch_assoc()['students'] ?? 0;

// Total Earnings 
$query = "
    SELECT SUM(c.price * (
        SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id
    )) as earnings
    FROM courses c
    WHERE c.instructor_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$totalEarnings = $data['earnings'] ?? 0;

// Total Assignments by instructor
$assignmentResult = $conn->query("
    SELECT COUNT(*) AS total_assignments 
    FROM assignments 
    WHERE course_id IN (
        SELECT id FROM courses WHERE instructor_id = $instructor_id
    )
");
$totalAssignments = $assignmentResult->fetch_assoc()['total_assignments'] ?? 0;

// Total Assignment Submissions by students
$submissionResult = $conn->query("
    SELECT COUNT(*) AS total_submissions 
    FROM assignment_submissions 
    WHERE assignment_id IN (
        SELECT id FROM assignments 
        WHERE course_id IN (SELECT id FROM courses WHERE instructor_id = $instructor_id)
    )
");
$totalSubmissions = $submissionResult->fetch_assoc()['total_submissions'] ?? 0;

// Enrolled Students List Query (needed for the Students tab)
$studentQuery = "
    SELECT u.name AS student_name, u.email AS student_email,
           c.title AS course_title, e.enrolled_at
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN users u ON e.student_email = u.email
    WHERE c.instructor_id = ?
    ORDER BY e.enrolled_at DESC
";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instructor Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .card h2 {
      font-size: 2rem;
    }

    .tab-content {
      margin-top: 1rem;
    }

    @media (max-width: 768px) {
      .card h5, .card h2, .card h6 {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#">🎓 Instructor Panel</a>
  <div class="ms-auto">
    <a href="instructor_logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<!-- Main Container -->
<div class="container py-4">
  <h3 class="mb-4">👋 Welcome, <?= htmlspecialchars($instructor['name']) ?></h3>

  <!-- Summary Cards -->
  <div class="row g-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card bg-primary text-white p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Total Courses</h5>
            <h2><?= $totalCourses ?></h2>
          </div>
          <i class="bi bi-journal-bookmark fs-1"></i>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card bg-success text-white p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Students</h5>
            <h2><?= $totalStudents ?></h2>
          </div>
          <i class="bi bi-people fs-1"></i>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card bg-info text-white p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Assignments</h5>
            <h6><?= $totalAssignments ?> Created</h6>
            <h6><?= $totalSubmissions ?> Submitted</h6>
          </div>
          <i class="bi bi-file-text fs-1"></i>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card bg-warning text-dark p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Earnings</h5>
            <h2>₹<?= number_format($totalEarnings, 2) ?></h2>
          </div>
          <i class="bi bi-currency-rupee fs-1"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mt-5" id="dashboardTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#students" type="button">👨‍🎓 Students</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#earnings" type="button">💰 Earnings</button>
    </li>
  </ul>

  <div class="tab-content">
    <!-- Students Tab -->
    <div class="tab-pane fade show active" id="students">
      <h5 class="mt-3">Enrolled Students</h5>
      <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive mt-3">
          <table class="table table-bordered table-hover">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Email</th>
                <th>Course</th>
                <th>Enrolled On</th>
              </tr>
            </thead>
            <tbody>
              <?php $sn = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $sn++ ?></td>
                  <td><?= htmlspecialchars($row['student_name']) ?></td>
                  <td><?= htmlspecialchars($row['student_email']) ?></td>
                  <td><?= htmlspecialchars($row['course_title']) ?></td>
                  <td><?= date("d M Y", strtotime($row['enrolled_at'])) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info mt-3">No students enrolled yet.</div>
      <?php endif; ?>
    </div>

    <!-- Earnings Tab -->
    <div class="tab-pane fade" id="earnings">
      <h5 class="mt-3">Course Earnings</h5>
      <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Course</th>
              <th>Students</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $query = "
                SELECT c.id, c.title, c.price, 
                       (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS enrolled_students
                FROM courses c WHERE c.instructor_id = ?";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("i", $instructor_id);
              $stmt->execute();
              $result = $stmt->get_result();

              $sn = 1;
              $grandTotal = 0;

              while ($row = $result->fetch_assoc()):
                $total = $row['price'] * $row['enrolled_students'];
                $grandTotal += $total;
            ?>
              <tr>
                <td><?= $sn++ ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['enrolled_students'] ?></td>
                <td>₹<?= number_format($row['price'], 2) ?></td>
                <td>₹<?= number_format($total, 2) ?></td>
              </tr>
            <?php endwhile; ?>
            <tr class="table-warning fw-bold">
              <td colspan="4" class="text-end">Grand Total</td>
              <td>₹<?= number_format($grandTotal, 2) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="mt-4 d-flex flex-wrap gap-3">
    <a href="manage_courses.php" class="btn btn-outline-primary">📘 Manage Courses</a>
    <a href="student_insights.php" class="btn btn-outline-info">📈 Student Insights</a>
    <a href="instructor_assignments.php" class="btn btn-outline-warning">📝 Assignments</a>
    <a href="earnings.php" class="btn btn-outline-success">💰 Earnings</a>
    <a href="profile.php" class="btn btn-outline-secondary">👤 Profile</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

