<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $upload_dir = '../uploads/assignments/';
    $filename = $_FILES['assignment_file']['name'];
    $tmp_name = $_FILES['assignment_file']['tmp_name'];

    $unique_name = time() . '_' . basename($filename);
    $file_path = $upload_dir . $unique_name;

    if (move_uploaded_file($tmp_name, $file_path)) {
        $stmt = $conn->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_path, status) VALUES (?, ?, ?, 'completed')");
        $stmt->bind_param("iis", $assignment_id, $student_id, $file_path);
        $stmt->execute();
        $success = "Assignment submitted successfully!";
    } else {
        $error = "Failed to upload file.";
    }
}

// Get all assignments for this student (enrolled courses)
$query = "
    SELECT a.id, a.title, c.title AS course_title
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    JOIN enrollments e ON e.course_id = c.id
    WHERE e.student_email = (SELECT email FROM users WHERE id = $student_id)
";
$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Assignment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .submit-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }

        .btn-primary {
            font-weight: 500;
            padding: 10px 20px;
        }

        .form-label {
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .submit-card {
                padding: 20px;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="submit-card">
        <h3 class="mb-4">📤 Submit Assignment</h3>

        <a href="assignments.php" class="btn btn-sm btn-outline-secondary mb-3">⬅ Back to Assignments</a>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Select Assignment</label>
                <select name="assignment_id" class="form-select" required>
                    <option value="">-- Select Assignment --</option>
                    <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['course_title'] . " - " . $row['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload File (PDF, DOCX, etc.)</label>
                <input type="file" name="assignment_file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">📩 Submit Assignment</button>
        </form>
    </div>
</div>

</body>
</html>

