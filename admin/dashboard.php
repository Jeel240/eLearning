<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require '../config.php'; 

// ✅ Count data from DB
$users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$courses = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
$instructors = mysqli_query($conn, "SELECT COUNT(*) AS total FROM instructors");
$enrollments = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments");

$totalUsers = mysqli_fetch_assoc($users)['total'];
$totalCourses = mysqli_fetch_assoc($courses)['total'];
$totalInstructors = mysqli_fetch_assoc($instructors)['total'];
$totalEnrollments = mysqli_fetch_assoc($enrollments)['total'];

$certificates = mysqli_query($conn, "SELECT COUNT(*) AS total FROM certificates");
$totalCertificates = mysqli_fetch_assoc($certificates)['total'];

// Fetch certificate details (user, course)
$certificateDetails = mysqli_query($conn, "
    SELECT u.name AS user_name, c.title AS course_title, cert.issued_at 
    FROM certificates cert
    JOIN users u ON cert.user_id = u.id
    JOIN courses c ON cert.course_id = c.id
    ORDER BY cert.issued_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      min-height: 100vh;
    }

    .sidebar a {
      display: block;
      color: white;
      padding: 12px 25px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #495057;
    }

    .sidebar h2 {
      font-size: 22px;
      text-align: center;
      margin: 20px 0;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        min-height: auto;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-3">
  <button class="btn btn-outline-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
    <i class="fas fa-bars"></i>
  </button>
  <span class="navbar-brand ms-2">Admin Dashboard</span>
</nav>

<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-start text-bg-dark d-md-none" tabindex="-1" id="sidebarMenu">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">MOOCs Admin</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="sidebar px-0">
      <a href="#" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
      <a href="manage_courses.php"><i class="fas fa-book me-2"></i>Manage Courses</a>
      <a href="manage_users.php"><i class="fas fa-users me-2"></i>Manage Users</a>
      <a href="manage_instructors.php"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Instructors</a>
      <a href="enrollments.php"><i class="fas fa-user-graduate me-2"></i>Enrollments</a>
      <a href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
      <a href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>
  </div>
</div>

<!-- Static Sidebar for Desktop -->
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 d-none d-md-block bg-dark sidebar">
        <a href="#" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
        <a href="manage_courses.php"><i class="fas fa-book me-2"></i>Manage Courses</a>
        <a href="manage_users.php"><i class="fas fa-users me-2"></i>Manage Users</a>
        <a href="manage_instructors.php"><i class="fas fa-chalkboard-teacher me-2"></i>Manage Instructors</a>
        <a href="enrollments.php"><i class="fas fa-user-graduate me-2"></i>Enrollments</a>
        <a href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
        <a href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
      <div class="bg-white p-4 rounded shadow-sm mb-4">
        <h4>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> 👋</h4>
        <p>You're logged into the admin dashboard. Use the menu to manage your platform.</p>
      </div>

      <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
          <div class="card text-white bg-primary">
            <div class="card-body">
              <h6 class="card-title">Total Users</h6>
              <h4><?= $totalUsers ?></h4>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card text-white bg-success">
            <div class="card-body">
              <h6 class="card-title">Courses</h6>
              <h4><?= $totalCourses ?></h4>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card text-white bg-info">
            <div class="card-body">
              <h6 class="card-title">Instructors</h6>
              <h4><?= $totalInstructors ?></h4>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card text-white bg-dark">
            <div class="card-body">
              <h6 class="card-title">Enrollments</h6>
              <h4><?= $totalEnrollments ?></h4>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card text-dark bg-warning">
            <div class="card-body">
              <h6 class="card-title">Certificates</h6>
              <h4><?= $totalCertificates ?></h4>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
