<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT a.id FROM assignments a JOIN courses c ON a.course_id = c.id WHERE a.id = ? AND c.instructor_id = ?");
    $stmt->bind_param("ii", $delete_id, $instructor_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $conn->query("DELETE FROM assignment_submissions WHERE assignment_id = $delete_id");
        $conn->query("DELETE FROM assignments WHERE id = $delete_id");
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error']);
        exit();
    }
}

// Handle Add 
if (isset($_POST['add_assignment'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $course_id, $title, $description);
    $stmt->execute();
    echo json_encode(['status' => 'added']);
    exit();
}

// Fetch courses
$courses = mysqli_query($conn, "SELECT * FROM courses WHERE instructor_id = $instructor_id");
$course_list = [];
while ($row = mysqli_fetch_assoc($courses)) {
    $course_list[] = $row;
}

// Fetch assignments with submission counts
$sql = "
    SELECT a.id AS assignment_id, a.title, a.description, c.title AS course_title,
    (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id) AS submission_count
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    WHERE c.instructor_id = ?
    ORDER BY a.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$assignments = $stmt->get_result();

// Prepare student names for each assignment
$studentNamesMap = [];

$assignmentIds = [];
$assignmentData = []; 

while ($row = $assignments->fetch_assoc()) {
    $assignmentIds[] = $row['assignment_id'];
    $assignmentData[] = $row;
}

if (!empty($assignmentIds)) {
    $ids = implode(",", array_map('intval', $assignmentIds));
    $query = "
        SELECT s.assignment_id, u.name 
        FROM assignment_submissions s 
        JOIN users u ON s.student_id = u.id 
        WHERE s.assignment_id IN ($ids)
    ";
    $res = $conn->query($query);

    while ($r = $res->fetch_assoc()) {
        $assignment_id = $r['assignment_id'];
        $student_name = $r['name'];
        if (!isset($studentNamesMap[$assignment_id])) {
            $studentNamesMap[$assignment_id] = [];
        }
        $studentNamesMap[$assignment_id][] = $student_name;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📄 Manage Assignments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 30px;
      margin-top: 40px;
    }

    table th, table td {
      vertical-align: middle;
    }

    @media (max-width: 768px) {
      table th, table td {
        font-size: 0.9rem;
      }

      .btn-sm {
        font-size: 0.75rem;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">📄 Manage Assignments</h3>
    <a href="instructor_dashboard.php" class="btn btn-outline-secondary">⬅️ Back</a>
  </div>

  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add Assignment</button>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th>#</th>
          <th>Course</th>
          <th>Title</th>
          <th>Submissions</th>
          <th>Students Submitted</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="assignmentTable">
        <?php $sn = 1;
        foreach ($assignmentData as $row): ?>
        <tr id="row<?= $row['assignment_id'] ?>">
          <td class="text-center"><?= $sn++ ?></td>
          <td><?= htmlspecialchars($row['course_title']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td class="text-center"><?= $row['submission_count'] ?></td>
          <td>
            <?php
            $names = $studentNamesMap[$row['assignment_id']] ?? [];
            echo $names ? implode(", ", array_map('htmlspecialchars', $names)) : "No submissions";
            ?>
          </td>
          <td class="text-center">
            <a href="view_submissions.php?assignment_id=<?= $row['assignment_id'] ?>" class="btn btn-sm btn-outline-primary mb-1">View</a>
            <button class="btn btn-sm btn-outline-danger mb-1" onclick="deleteAssignment(<?= $row['assignment_id'] ?>)">Delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">➕ Add Assignment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Course</label>
          <select name="course_id" class="form-select" required>
            <?php foreach ($course_list as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_assignment" class="btn btn-success">Add</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function deleteAssignment(id) {
  if (confirm('Are you sure you want to delete this assignment?')) {
    fetch('?delete_id=' + id)
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          document.getElementById('row' + id).remove();
        } else {
          alert('Delete failed.');
        }
      });
  }
}
</script>

</body>
</html>
