<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];
$msg = "";

// Fetch instructor's courses
$courses = $conn->prepare("SELECT id, title FROM courses WHERE instructor_id = ?");
$courses->bind_param("i", $instructor_id);
$courses->execute();
$courseList = $courses->get_result();

// On form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $course_id = $_POST['course_id'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $course_id, $title, $description, $due_date);
    
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Assignment created successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Failed to create assignment.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h3>Create Assignment</h3>
    <a href="instructor_assignments.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <?= $msg ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                <?php while ($course = $courseList->fetch_assoc()): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Assignment Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" required>
        </div>
        <button class="btn btn-success">Create Assignment</button>
    </form>
</div>
</body>
</html>
