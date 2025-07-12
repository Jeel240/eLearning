<?php
require '../config.php';

$student_id = $_POST['student_id'];
$course_id = $_POST['course_id'];
$video_name = trim($_POST['video_name']);

// Insert watched record
$stmt = $conn->prepare("INSERT IGNORE INTO watched_videos (student_id, course_id, video_name) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $student_id, $course_id, $video_name);
$stmt->execute();
$stmt->close();

header("Location: view_course.php?course_id=$course_id");
exit();
