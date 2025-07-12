<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Get student email
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_email);
$stmt->fetch();
$stmt->close();

// Verify enrollment
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_email = ? AND course_id = ? AND payment_status = 'Success'");
$stmt->bind_param("si", $student_email, $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: my_courses.php");
    exit();
}

// Fetch course details
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();
$assignments = $conn->query("SELECT * FROM assignments WHERE course_id = $course_id");

// Extract videos from comma-separated list
$video_list = array_filter(array_map('trim', explode(',', $course['videos'])));
$total_videos = count($video_list);

// Fetch watched videos
$stmt = $conn->prepare("SELECT video_name FROM watched_videos WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$watched_result = $stmt->get_result();
$watched_videos = [];
while ($row = $watched_result->fetch_assoc()) {
    $watched_videos[] = $row['video_name'];
}
$stmt->close();

// Track completed assignments
$completed_assignments = 0;
$total_assignments = $assignments->num_rows;

$assignments->data_seek(0);
while ($assign = $assignments->fetch_assoc()) {
    $completed_by = [];

    if (isset($assign['completed_by'])) {
        $completed_by = json_decode($assign['completed_by'], true) ?? [];
    }

    if (in_array($student_id, $completed_by)) {
        $completed_assignments++;
    }
}

// Completion logic
$isCompleted = (count($video_list) == count($watched_videos)) && ($total_assignments == $completed_assignments);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> - Course Content</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f3f6;
            font-family: 'Segoe UI', sans-serif;
        }

        .video-wrapper {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .video-wrapper video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .pdf-link {
            margin: 8px;
        }

        .assignment-item {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }

        .certificate-box {
            background: #d1e7dd;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .btn-completed {
            background-color: #198754;
            border: none;
        }

        .btn-completed:hover {
            background-color: #157347;
        }

        @media (max-width: 576px) {
            .video-wrapper {
                padding: 15px;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2><?= htmlspecialchars($course['title']) ?> - Course Content</h2>
    <p class="text-muted"><?= htmlspecialchars($course['description']) ?></p>

    <?php if ($isCompleted): ?>
        <div class="alert alert-success mt-3">
            🎉 Congratulations! You have completed all the videos<?= $total_assignments > 0 ? " and assignments" : "" ?> in this course.
        </div>
        <div class="text-center mt-4">
            <a href="certificates.php?course_id=<?= $course_id ?>" class="btn btn-lg btn-success">
                🎓 Download Your Certificate
            </a>
        </div>
    <?php endif; ?>

    <!-- 📺 Video Section -->
    <h4 class="mt-5">📺 Videos</h4>
    <div class="list-group mb-4">
        <?php foreach ($video_list as $index => $video): 
            $watched = in_array($video, $watched_videos);
        ?>
        <div class="list-group-item d-flex justify-content-between align-items-start">
            <div>
                <strong>Lecture <?= $index + 1 ?></strong><br>
                <?php if (filter_var($video, FILTER_VALIDATE_URL)): ?>
                    <a href="<?= $video ?>" target="_blank">Watch Video (Link)</a>
                <?php else: ?>
                    <video width="320" height="200" controls class="mt-2">
                        <source src="../uploads/videos/<?= $video ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <form method="POST" action="mark_video_completed.php" class="ms-3">
                <input type="hidden" name="video_name" value="<?= htmlspecialchars($video) ?>">
                <input type="hidden" name="student_id" value="<?= $student_id ?>">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                <button class="btn btn-sm btn-<?= $watched ? 'success' : 'outline-primary' ?>" <?= $watched ? 'disabled' : '' ?>>
                    <?= $watched ? '✔ Watched' : 'Mark as Watched' ?>
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 📄 PDF Section -->
    <?php if (!empty($course['pdfs'])): ?>
        <h4 class="mt-4">📄 Downloadable PDFs</h4>
        <div class="d-flex flex-wrap gap-2">
            <?php
            $pdfs = explode(",", $course['pdfs']);
            foreach ($pdfs as $pdf):
                $pdf = trim($pdf);
                if ($pdf):
            ?>
            <a href="<?= filter_var($pdf, FILTER_VALIDATE_URL) ? $pdf : "../uploads/pdfs/$pdf" ?>" 
               class="btn btn-secondary btn-sm" target="_blank">
                Open <?= basename($pdf) ?>
            </a>
            <?php endif; endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 📝 Assignment Section -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-2">
        <h4>📝 Assignments</h4>
    </div>
    <ul class="list-group">
        <?php
        $assignments->data_seek(0);
        while ($assign = $assignments->fetch_assoc()):
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <strong><?= htmlspecialchars($assign['title']) ?></strong>
            </div>
            <a href="assignments.php?course_id=<?= $course_id ?>" class="btn btn-outline-primary btn-sm">
                Upload/View Assignment
            </a>
        </li>
        <?php endwhile; ?>
    </ul>

</div>
</body>
</html>
