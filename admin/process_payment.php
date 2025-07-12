<?php
session_start();
require '../config.php';

if (!isset($_POST['student_name'], $_POST['student_email'], $_POST['course_id'], $_POST['payment_method'])) {
    die("Invalid form submission.");
}

$name = trim($_POST['student_name']);
$email = trim($_POST['student_email']);
$password_raw = isset($_POST['student_password']) ? $_POST['student_password'] : substr(md5(rand()), 0, 8);
$password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
$course_id = intval($_POST['course_id']);
$method = $_POST['payment_method'];
$payment_status = "Success";

// Fetch course details
$course_result = $conn->query("SELECT * FROM courses WHERE id = $course_id");
if (!$course_result || $course_result->num_rows == 0) {
    die("Course not found.");
}
$course = $course_result->fetch_assoc();
$price = $course['price'];
$platform_fee = 49;
$total_amount = $price + $platform_fee;

// Payment details
$card_number = $_POST['card_number'] ?? '';
$upi_id = $_POST['upi_id'] ?? '';
$bank_name = $_POST['bank_name'] ?? '';
$payment_details = match ($method) {
    'card' => "Card: **** **** **** " . substr($card_number, -4),
    'upi' => "UPI ID: $upi_id",
    'net_banking' => "Bank: $bank_name",
    default => "Unknown"
};

// Check if user already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $student_id = $user['id'];
    $name = $user['name'];
} else {
    // Auto-register new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hash);
    $stmt->execute();
    $student_id = $stmt->insert_id;

    // Send login credentials via email
    $login_subject = "🎓 Welcome to MOOCs - Login Details";
    $login_body = "
        <p>Hi $name,</p>
        <p>Thanks for enrolling in <strong>{$course['title']}</strong>!</p>
        <p><strong>Your Login Details:</strong></p>
        <ul>
            <li>📧 Email: <strong>$email</strong></li>
            <li>🔐 Password: <strong>$password_raw</strong></li>
        </ul>
        <p>Login here: <a href='http://yourdomain.com/login.php'>Login Now</a></p>
        <p>Happy Learning!<br>MOOCs Team</p>
    ";
    sendHtmlEmail($email, $login_subject, $login_body);
}

// Insert enrollment
$stmt = $conn->prepare("INSERT INTO enrollments (student_name, student_email, course_id, payment_method, payment_details, amount_paid, platform_fee, payment_status, enrolled_at) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssissdds", $name, $email, $course_id, $method, $payment_details, $price, $platform_fee, $payment_status);
$stmt->execute();

// Insert into transactions table after enrollment
// STEP 1: Get instructor_id for the course
$instructor_stmt = $conn->prepare("SELECT instructor_id FROM courses WHERE id = ?");
$instructor_stmt->bind_param("i", $course_id);
$instructor_stmt->execute();
$instructor_result = $instructor_stmt->get_result();
$instructor_row = $instructor_result->fetch_assoc();
$instructor_id = $instructor_row['instructor_id'];

// STEP 2: Calculate earnings
$instructor_earnings = $price; // ₹ received by instructor
$platform_commission = $platform_fee;
$total_amount = $instructor_earnings + $platform_commission;

// STEP 3: Insert into transactions table
$txn_stmt = $conn->prepare("
    INSERT INTO transactions 
    (course_id, instructor_id, student_email, total_amount, instructor_earnings, platform_commission, payment_method, payment_details, status, requested_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");
$txn_stmt->bind_param(
    "iisddsss", 
    $course_id, 
    $instructor_id, 
    $email, 
    $total_amount, 
    $instructor_earnings, 
    $platform_commission, 
    $method, 
    $payment_details
);
$txn_stmt->execute();

// Send payment confirmation
$payment_subject = "🧾 Payment Successful for {$course['title']}";
$payment_body = "
    <p>Hi $name,</p>
    <p>Your payment for <strong>{$course['title']}</strong> has been successfully received.</p>
    <p><strong>Payment Details:</strong></p>
    <ul>
        <li>💰 Amount: ₹" . number_format($total_amount, 2) . "</li>
        <li>📅 Date: " . date("d M Y, h:i A") . "</li>
        <li>📘 Course: {$course['title']}</li>
        <li>💳 Method: $payment_details</li>
    </ul>
    <p>You can now access your course materials: videos, PDFs, and more.</p>
    <p>Access Course: <a href='http://yourdomain.com/course_details.php?id=$course_id&enrolled=1'>Click Here</a></p>
    <p>Thank you for learning with us!<br>MOOCs Team</p>
";
sendHtmlEmail($email, $payment_subject, $payment_body);

// Set session and redirect to course details
$_SESSION['student_id'] = $student_id;
$_SESSION['student_name'] = $name;
header("Location: ../course_details.php?id=$course_id&enrolled=1");
exit();

// Function to send HTML emails
function sendHtmlEmail($to, $subject, $htmlBody) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: MOOCs <no-reply@yourdomain.com>\r\n";
    mail($to, $subject, $htmlBody, $headers);
}
?>
