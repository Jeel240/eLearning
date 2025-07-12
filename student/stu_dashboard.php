<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// Fetch email
$student_email = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email FROM users WHERE id = $student_id"))['email'];

// Course Count
$enrolled_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM enrollments WHERE student_email = '$student_email'"));

// Assignments
$assignments_completed = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as completed 
    FROM assignment_submissions 
    WHERE student_id = $student_id AND status = 'completed'
"))['completed'];

$assignments_due = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS due_count
    FROM assignments a
    INNER JOIN enrollments e ON a.course_id = e.course_id
    WHERE e.student_email = '$student_email'
    AND NOT EXISTS (
        SELECT 1 FROM assignment_submissions s
        WHERE s.assignment_id = a.id AND s.student_id = $student_id
    )
"))['due_count'];

// Certificates logic
$certificates_earned = 0;
$enrolled_courses = mysqli_query($conn, "
    SELECT c.id, c.videos 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE e.student_email = '$student_email' AND e.payment_status = 'Success'
");

while ($course = mysqli_fetch_assoc($enrolled_courses)) {
    $course_id = $course['id'];
    $video_list = array_filter(array_map('trim', explode(',', $course['videos'] ?? '')));
    $total_videos = count($video_list);

    $watched_videos_query = mysqli_query($conn, "
        SELECT video_name FROM watched_videos 
        WHERE student_id = $student_id AND course_id = $course_id
    ");
    $watched = [];
    while ($v = mysqli_fetch_assoc($watched_videos_query)) {
        $watched[] = trim($v['video_name']);
    }

    $videos_watched = count(array_intersect($video_list, $watched));

    $assignment_count = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) as total FROM assignments WHERE course_id = $course_id
    "))['total'];

    $completed_assignments = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) as completed 
        FROM assignment_submissions s
        JOIN assignments a ON s.assignment_id = a.id
        WHERE s.student_id = $student_id AND s.status = 'completed' AND a.course_id = $course_id
    "))['completed'];

    if (
        $total_videos > 0 &&
        $videos_watched === $total_videos &&
        ($assignment_count == 0 || $completed_assignments == $assignment_count)
    ) {
        $certificates_earned++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding-top: 40px;
            z-index: 999;
        }

        .sidebar h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 30px;
            color: #ffc107;
        }

        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: #f8f9fa;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            margin-left: 240px;
            padding: 40px 20px;
            width: 100%;
        }

        .dashboard-cards .card {
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            text-align: center;
            background: #fff;
        }

        .card h5 {
            font-size: 18px;
            font-weight: 600;
        }

        .popup-box,
        .overlay {
            display: none;
        }

        .popup-box.active,
        .overlay.active {
            display: block;
        }

        .popup-box {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -240px;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                font-size: 26px;
                margin-bottom: 20px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

<!-- Overlay & Popup -->
<div class="overlay" id="overlay"></div>
<div class="popup-box" id="popupBox">
    <h4>Welcome, <?= htmlspecialchars($student_name) ?>!</h4>
    <p>You’re successfully logged in.</p>
    <button class="btn btn-primary" onclick="closePopup()">OK</button>
</div>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Student Panel</h2>
        <a href="#">📊 Dashboard</a>
        <a href="my_courses.php">📚 My Courses</a>
        <a href="assignments.php">📝 Assignments</a>
        <a href="certificates.php">🎓 Certificates</a>
        <a href="profile.php">👤 Profile</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <!-- Main -->
    <div class="main-content">
        <span class="menu-toggle d-block d-md-none" onclick="toggleSidebar()">☰ Menu</span>
        <h2>Welcome, <?= htmlspecialchars($student_name) ?>!</h2>
        <p class="text-muted">Here’s an overview of your progress:</p>

        <div class="row dashboard-cards mt-4 g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <h5>Enrolled Courses</h5>
                    <p class="fs-4"><?= $enrolled_count ?></p>
                    <a href="my_courses.php" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <h5>Assignments Due</h5>
                    <p class="fs-4"><?= $assignments_due ?></p>
                    <a href="assignments.php" class="btn btn-outline-warning btn-sm">View</a>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <h5>Certificates Earned</h5>
                    <p class="fs-4"><?= $certificates_earned ?></p>
                    <a href="certificates.php" class="btn btn-outline-info btn-sm">View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    function closePopup() {
        document.getElementById("popupBox").classList.remove('active');
        document.getElementById("overlay").classList.remove('active');
    }

    window.onload = function () {
        document.getElementById("popupBox").classList.add('active');
        document.getElementById("overlay").classList.add('active');
    };
</script>

</body>
</html>
