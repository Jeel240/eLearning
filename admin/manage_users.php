<?php
session_start();
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Enable error reporting (for localhost debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Handle AJAX delete
if (isset($_GET['ajax_delete']) && isset($_GET['user_id'])) {
    header('Content-Type: application/json');
    $user_id = intval($_GET['user_id']);

    // Get user email
    $getUser = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $getUser->bind_param("i", $user_id);
    $getUser->execute();
    $result = $getUser->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $email = $user['email'];

        // Delete related records
        $conn->query("DELETE FROM enrollments WHERE student_email = '$email'");
        $conn->query("DELETE FROM assignment_submissions WHERE user_id = $user_id");
        $conn->query("DELETE FROM certificates WHERE user_id = $user_id");
        $conn->query("DELETE FROM messages WHERE user_id = $user_id");

        // Delete user
        $deleteUser = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteUser->bind_param("i", $user_id);
        $deleteUser->execute();

        if ($deleteUser->affected_rows > 0) {
            echo json_encode(['status' => 'deleted']);
        } else {
            echo json_encode(['status' => 'not_found']);
        }
    } else {
        echo json_encode(['status' => 'invalid_user']);
    }
    exit();
}

// ✅ Fetch users + enrolled courses
$query = "
    SELECT 
        users.id AS user_id,
        users.name,
        users.email,
        GROUP_CONCAT(courses.title SEPARATOR ', ') AS enrolled_courses
    FROM users
    LEFT JOIN enrollments ON users.email = enrollments.student_email
    LEFT JOIN courses ON enrollments.course_id = courses.id
    GROUP BY users.id
";
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    .table-container {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
    }

    h3 {
      font-weight: 600;
    }

    .table th, .table td {
      vertical-align: middle;
    }

    @media (max-width: 576px) {
      .table th, .table td {
        font-size: 14px;
      }

      .btn-sm {
        padding: 4px 10px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <h3 class="mb-4 text-center text-md-start">👥 Manage Users & Enrollments</h3>

    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Enrolled Courses</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; while ($user = mysqli_fetch_assoc($users)): ?>
              <tr id="userRow<?= $user['user_id'] ?>">
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                  <?= $user['enrolled_courses'] ? htmlspecialchars($user['enrolled_courses']) : '<span class="text-muted">Not Enrolled</span>' ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['user_id'] ?>)">Delete</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function deleteUser(userId) {
      if (confirm("Are you sure you want to delete this user?")) {
        fetch('?ajax_delete=1&user_id=' + userId)
          .then(response => response.json())
          .then(data => {
            if (data.status === 'deleted') {
              document.getElementById('userRow' + userId).remove();
            } else {
              alert("Delete failed: " + data.status);
            }
          })
          .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong!");
          });
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
