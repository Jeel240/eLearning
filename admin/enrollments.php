<?php
session_start();
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$query = "SELECT 
            e.id, 
            u.name AS student, 
            e.student_email, 
            c.title AS course, 
            e.enrolled_at 
          FROM enrollments e
          LEFT JOIN users u ON e.student_email = u.email
          LEFT JOIN courses c ON e.course_id = c.id
          ORDER BY e.enrolled_at DESC";
$enrollments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Enrollments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        h3 {
            color: #333;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            background: white;
        }

        @media (max-width: 575px) {
            h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body class="p-3 p-md-4">

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>📚 Course Enrollments</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Enrolled On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($enrollments)) : ?>
                        <tr class="text-center">
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['student'] ? htmlspecialchars($row['student']) : '<span class="text-muted">N/A</span>' ?></td>
                            <td><?= htmlspecialchars($row['student_email']) ?></td>
                            <td><?= $row['course'] ? htmlspecialchars($row['course']) : 'Unknown' ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($row['enrolled_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
