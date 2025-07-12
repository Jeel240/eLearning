<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student email
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_email);
$stmt->fetch();
$stmt->close();

// Fetch enrolled courses
$sql = "SELECT c.id, c.title, c.description, c.image, c.videos, e.enrolled_at 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.student_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f3f6;
            font-family: 'Segoe UI', sans-serif;
        }

        .course-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .course-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 5px;
        }

        .course-description {
            font-size: 0.95rem;
            color: #555;
            flex-grow: 1;
        }

        .enroll-date {
            font-size: 0.85rem;
            color: #888;
            margin-top: 10px;
        }

        .progress {
            height: 8px;
            margin-top: 6px;
            border-radius: 4px;
        }

        .btn-access {
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .course-title {
                font-size: 1.05rem;
            }
            .course-description {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h3 class="mb-4 text-center">📚 My Enrolled Courses</h3>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): 
                $course_id = $row['id'];
                $video_list = array_filter(array_map('trim', explode(',', $row['videos'])));
                $total_videos = count($video_list);

                // Watched videos count
                $stmt2 = $conn->prepare("SELECT COUNT(*) FROM watched_videos WHERE student_id = ? AND course_id = ?");
                $stmt2->bind_param("ii", $student_id, $course_id);
                $stmt2->execute();
                $stmt2->bind_result($watched_count);
                $stmt2->fetch();
                $stmt2->close();

                // Completion %
                $completion = ($total_videos > 0) ? round(($watched_count / $total_videos) * 100) : 0;

                // Update database (optional)
                $conn->query("UPDATE enrollments SET completion_percent = $completion WHERE student_email = '$student_email' AND course_id = $course_id");
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="course-card">
                    <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Course Image" class="course-img">
                    
                    <div class="course-title"><?= htmlspecialchars($row['title']) ?></div>
                    
                    <span class="badge bg-<?= $completion == 100 ? 'success' : 'warning' ?> small mb-2">
                        <?= $completion == 100 ? '✅ Completed' : '🕒 In Progress' ?>
                    </span>
                    
                    <div class="progress">
                        <div class="progress-bar bg-<?= $completion == 100 ? 'success' : 'info' ?>"
                             role="progressbar"
                             style="width: <?= $completion ?>%;"
                             aria-valuenow="<?= $completion ?>"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    <div class="course-description mt-2">
                        <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>...
                    </div>

                    <div class="enroll-date">
                        🗓️ Enrolled on <?= date('d M Y', strtotime($row['enrolled_at'])) ?>
                    </div>

                    <a href="view_course.php?course_id=<?= $course_id ?>" class="btn btn-primary btn-access">
                        Access Course
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            You haven't enrolled in any courses yet.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
