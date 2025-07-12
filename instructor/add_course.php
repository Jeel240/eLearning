<?php
session_start();
require '../config.php';

// Debugging mode: show all PHP and MySQL errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['instructor_id'])) {
    die("Unauthorized access.");
}

$instructor_id = $_SESSION['instructor_id'];

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? 0;
$duration = $_POST['duration'] ?? '';
$status = $_POST['status'] ?? 'draft';

// Validate status
$valid_statuses = ['draft', 'published'];
if (!in_array($status, $valid_statuses)) {
    $status = 'draft'; 
}

// Ensure upload directory exists
$imageName = null;
if (!file_exists('../uploads')) {
    mkdir('../uploads', 0777, true);
}

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $imageName);
}

// Handle video uploads
$videoPaths = [];
if (!empty($_FILES['videos']['name'][0])) {
    foreach ($_FILES['videos']['tmp_name'] as $key => $tmp) {
        $filename = uniqid() . '_' . $_FILES['videos']['name'][$key];
        move_uploaded_file($tmp, "../uploads/" . $filename);
        $videoPaths[] = $filename;
    }
}

// Handle PDF uploads
$pdfPaths = [];
if (!empty($_FILES['pdfs']['name'][0])) {
    foreach ($_FILES['pdfs']['tmp_name'] as $key => $tmp) {
        $filename = uniqid() . '_' . $_FILES['pdfs']['name'][$key];
        move_uploaded_file($tmp, "../uploads/" . $filename);
        $pdfPaths[] = $filename;
    }
}

// Prepare and bind insert
$stmt = $conn->prepare("
    INSERT INTO courses 
        (instructor_id, title, description, image, price, duration, status, videos, pdfs)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$videos_json = json_encode($videoPaths);
$pdfs_json = json_encode($pdfPaths);

// Bind types: i (int), s (string), d (double), etc.
$stmt->bind_param(
    "isssdssss",
    $instructor_id,
    $title,
    $description,
    $imageName,
    $price,
    $duration,
    $status,
    $videos_json,
    $pdfs_json
);

// Execute and handle errors
if ($stmt->execute()) {
    header("Location: manage_courses.php");
    exit;
} else {
    echo "Insert failed: " . $stmt->error;
    exit;
}
