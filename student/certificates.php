<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

$student_email = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email FROM users WHERE id = $student_id"))['email'];

$eligible_courses = [];

$enrolled = mysqli_query($conn, "
    SELECT c.id, c.title, c.videos 
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.student_email = '$student_email' AND e.payment_status = 'Success'
");

while ($course = mysqli_fetch_assoc($enrolled)) {
    $course_id = $course['id'];
    $title = $course['title'];
    $video_list = array_filter(array_map('trim', explode(',', $course['videos'])));
    $total_videos = count($video_list);

    $watched = (int) mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(DISTINCT video_name) as watched 
        FROM watched_videos 
        WHERE student_id = $student_id AND course_id = $course_id
    "))['watched'];

    $assignment_count = (int) mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM assignments 
        WHERE course_id = $course_id
    "))['total'];

    $completed_assignments = (int) mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS completed 
        FROM assignment_submissions s
        JOIN assignments a ON a.id = s.assignment_id
        WHERE s.student_id = $student_id AND s.status = 'completed' AND a.course_id = $course_id
    "))['completed'];

    $eligible = $total_videos > 0 &&
                $watched == $total_videos &&
                ($assignment_count === 0 || $completed_assignments == $assignment_count);

    if ($eligible) {
        $eligible_courses[] = [
            'id' => $course_id,
            'title' => $title
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🎓 My Certificates</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .certificates-container {
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .certificate-item {
            background-color: #e9f7ef;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .certificate-title {
            font-weight: 600;
            font-size: 1rem;
            color: #155724;
        }

        .btn-success {
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .certificate-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .certificate-item a {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container certificates-container">
    <h2 class="mb-4 text-center">🎓 Your Earned Certificates</h2>

    <?php if (empty($eligible_courses)): ?>
        <div class="alert alert-warning text-center">
            You haven't completed any courses yet.
        </div>
    <?php else: ?>
        <?php foreach ($eligible_courses as $course): ?>
            <div class="certificate-item">
                <div class="certificate-title"><?= htmlspecialchars($course['title']) ?></div>
                <a href="generate_certificate.php?course_id=<?= $course['id'] ?>" target="_blank" class="btn btn-success btn-sm">
                    🎓 Download Certificate
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>

