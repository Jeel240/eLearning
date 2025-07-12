<?php
require 'config.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid Course ID.";
    exit();
}

$course_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "Course not found.";
    exit();
}

// Check enrollment
$enrolled = false;
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $student_email = $conn->query("SELECT email FROM users WHERE id = $student_id")->fetch_assoc()['email'];

    $check = $conn->prepare("SELECT id FROM enrollments WHERE course_id = ? AND student_email = ?");
    $check->bind_param("is", $course_id, $student_email);
    $check->execute();
    $enrolled = $check->get_result()->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> | Course Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .course-image {
            width: 100%;
            height: auto;
            border-radius: 12px;
            object-fit: cover;
            max-height: 400px;
        }
        .enroll-btn {
            background-color: #0d6efd;
            color: white;
            padding: 0.6rem 1.8rem;
            font-weight: 500;
            border: none;
            border-radius: 8px;
        }
        .enroll-btn:hover {
            background-color: #084298;
        }
        .material-section {
            margin-top: 60px;
        }
        video {
            border-radius: 10px;
            max-height: 400px;
        }
        @media (max-width: 768px) {
            .course-image {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>

<!-- Course Details -->
<div class="container my-5">
    <div class="row g-5 align-items-start">
        <div class="col-lg-6">
            <img src="uploads/<?= htmlspecialchars($course['image']) ?>" alt="<?= $course['title'] ?>" class="course-image shadow-sm">
        </div>
        <div class="col-lg-6">
            <h2 class="fw-bold"><?= htmlspecialchars($course['title']) ?></h2>
            <p class="text-muted mt-3"><strong>Price:</strong> ₹<?= number_format($course['price'], 2) ?></p>
            <p class="mt-3"><?= nl2br(htmlspecialchars($course['description'])) ?></p>

            <?php if (!$enrolled): ?>
                <a href="enroll_form.php?course_id=<?= $course['id'] ?>" class="btn enroll-btn mt-4">Enroll Now</a>
            <?php else: ?>
                <div class="alert alert-success mt-4">🎉 You are enrolled in this course!</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Materials -->
    <div class="material-section">
        <h4 class="fw-semibold mb-4">📚 Course Materials</h4>

        <?php if ($enrolled): ?>

            <?php if (!empty($course['videos'])): ?>
                <div class="mb-5">
                    <h5 class="mb-3">🎥 Video Lectures:</h5>
                    <?php
                    $videos = explode(',', $course['videos']);
                    foreach ($videos as $video):
                        $video = trim($video);
                        if ($video):
                    ?>
                        <div class="mb-4">
                            <video controls class="w-100">
                                <source src="uploads/<?= htmlspecialchars($video) ?>" type="video/mp4">
                                Your browser does not support HTML5 video.
                            </video>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($course['pdfs'])): ?>
                <div>
                    <h5 class="mb-3">📄 Downloadable PDFs:</h5>
                    <ul class="list-group list-group-flush">
                        <?php
                        $pdfs = explode(',', $course['pdfs']);
                        foreach ($pdfs as $pdf):
                            $pdf = trim($pdf);
                            if ($pdf):
                        ?>
                            <li class="list-group-item">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                <a href="uploads/<?= htmlspecialchars($pdf) ?>" target="_blank">
                                    <?= htmlspecialchars(basename($pdf)) ?>
                                </a>
                            </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning">
                🚫 Please <a href="enroll_form.php?course_id=<?= $course['id'] ?>">enroll in this course</a> to access all materials.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
