<?php
session_start();
require '../config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student's enrolled course IDs
$enrolled_course_ids = [];
$query = "SELECT course_id FROM enrollments WHERE student_email = (SELECT email FROM users WHERE id = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $enrolled_course_ids[] = $row['course_id'];
}

$assignments = [];

if (!empty($enrolled_course_ids)) {
    $placeholders = implode(',', array_fill(0, count($enrolled_course_ids), '?'));
    $query = "
    SELECT 
        a.id,
        a.title,
        c.title AS course_title,
        s.status AS submission_status,
        s.grade,
        s.file_path
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    LEFT JOIN assignment_submissions s 
        ON s.assignment_id = a.id AND s.student_id = ?
    WHERE a.course_id IN ($placeholders)
    ";

    $types = 'i' . str_repeat('i', count($enrolled_course_ids)); // Only 1 student_id now
    $params = array_merge([$student_id], $enrolled_course_ids);

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $assignments = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .assignment-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        .table th, .table td {
            vertical-align: middle !important;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.45em 0.65em;
        }

        @media (max-width: 576px) {
            .table th, .table td {
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }
        }
    </style>
</head>
<body>

<div class="container assignment-box">
    <h3 class="mb-4 text-center">📝 Your Assignments</h3>

    <?php if ($assignments && $assignments->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Marks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1; while ($row = $assignments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= htmlspecialchars($row['course_title']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td>
                                <?php if ($row['submission_status'] === 'completed'): ?>
                                    <span class="badge bg-success">Submitted</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $row['grade'] !== null ? htmlspecialchars($row['grade']) : "-" ?>
                            </td>
                            <td>
                                <?php if ($row['submission_status'] === 'completed' && $row['file_path']): ?>
                                    <a href="../uploads/assignments/<?= htmlspecialchars($row['file_path']) ?>" class="btn btn-sm btn-secondary" download>
                                        📥 View File
                                    </a>
                                <?php else: ?>
                                    <a href="submit_assignment.php?assignment_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                        📤 Submit
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No assignments found for your enrolled courses.</div>
    <?php endif; ?>
</div>

</body>
</html>
