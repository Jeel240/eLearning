<?php
require '../config.php';
$assignment_id = $_POST['assignment_id'];
$student_id = $_POST['student_id'];

$assign = $conn->query("SELECT completed_by FROM assignments WHERE id = $assignment_id")->fetch_assoc();
$completed = json_decode($assign['completed_by'], true) ?? [];

if (!in_array($student_id, $completed)) {
    $completed[] = $student_id;
    $updated = json_encode($completed);
    $conn->query("UPDATE assignments SET completed_by = '$updated' WHERE id = $assignment_id");
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
