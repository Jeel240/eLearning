<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$submission_id = $_GET['id'] ?? 0;
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'];
    $stmt = $conn->prepare("UPDATE assignment_submissions SET grade = ?, status = 'completed' WHERE id = ?");
    $stmt->bind_param("si", $grade, $submission_id);

    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Grade submitted successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Failed to submit grade.</div>";
    }
}

// Fetch submission details
$stmt = $conn->prepare("
    SELECT s.*, u.name AS student_name, a.title AS assignment_title 
    FROM assignment_submissions s
    JOIN users u ON s.student_id = u.id
    JOIN assignments a ON s.assignment_id = a.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();

if (!$submission) {
    echo "<h4>Invalid Submission</h4>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h4>Grade Submission - <?= htmlspecialchars($submission['assignment_title']) ?></h4>
    <?= $msg ?>
    
    <p><strong>Student:</strong> <?= htmlspecialchars($submission['student_name']) ?></p>
    <p><strong>Submitted At:</strong> <?= date("d M Y, h:i A", strtotime($submission['submitted_at'])) ?></p>
    <p><strong>Current Grade:</strong> <?= $submission['grade'] ?? 'Not Graded' ?></p>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Assign Grade</label>
            <input type="text" name="grade" class="form-control" placeholder="e.g., A+, 85/100" required>
        </div>
        <button class="btn btn-primary">Submit Grade</button>
        <a href="view_submissions.php?assignment_id=<?= $submission['assignment_id'] ?>" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
