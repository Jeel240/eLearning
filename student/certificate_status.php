<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

$student_query = mysqli_query($conn, "SELECT email FROM users WHERE id = $student_id");
$student_email = mysqli_fetch_assoc($student_query)['email'];

$course_id = $_GET['course_id'] ?? 0;

$course_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM courses WHERE id = $course_id"));
$course_title = $course_data['title'] ?? 'Unknown Course';

// Force everything to integer
$assignment_count = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM assignments WHERE course_id = $course_id"))['total'];

$assignments_completed = (int) mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS completed
    FROM assignment_submissions s
    JOIN assignments a ON s.assignment_id = a.id
    WHERE s.student_id = $student_id AND s.status = 'completed' AND a.course_id = $course_id
"))['completed'];

$total_videos = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM course_videos WHERE course_id = $course_id"))['total'];

$videos_watched = (int) mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(DISTINCT video_name) AS watched
    FROM watched_videos
    WHERE student_id = $student_id AND course_id = $course_id
"))['watched'];

// Check enrollment
$is_enrolled = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM enrollments WHERE course_id = $course_id AND student_email = '$student_email' AND payment_status = 'Success'")) > 0;

// Certificate eligibility logic
$eligible = $is_enrolled &&
            $total_videos > 0 &&
            $videos_watched == $total_videos &&
            ($assignment_count == 0 || $assignments_completed == $assignment_count);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate Status - <?= htmlspecialchars($course_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <h2>🎓 Certificate Status</h2>
        <p>You must complete all required videos<?= $assignment_count > 0 ? " and assignments" : "" ?> in <strong><?= htmlspecialchars($course_title) ?></strong> to earn your certificate.</p>

        <?php if ($eligible): ?>
            <a href="generate_certificate.php?course_id=<?= $course_id ?>" class="btn btn-success mt-3" target="_blank">
                ✅ Download Your Certificate
            </a>
        <?php else: ?>
            <p class="text-warning mt-3">You have not yet completed all requirements.</p>
        <?php endif; ?>
    </div>

    <!-- Optional: Debug Info (Remove on production) -->
    <!--
    <pre class="text-start">
        Course: <?= $course_title . " (ID: $course_id)" ?>  
        Enrolled: <?= $is_enrolled ? 'Yes' : 'No' ?>  
        Total Videos: <?= $total_videos ?>  
        Watched Videos: <?= $videos_watched ?>  
        Total Assignments: <?= $assignment_count ?>  
        Completed Assignments: <?= $assignments_completed ?>  
        Eligible: <?= $eligible ? 'Yes' : 'No' ?>  
    </pre>
    -->
</body>
</html>
