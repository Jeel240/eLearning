<?php
session_start();
if (!isset($_POST['student_name'], $_POST['student_email'], $_POST['student_password'], $_POST['course_id'], $_POST['payment_method'])) {
    die("Invalid access");
}

// Store all posted data in session temporarily
$_SESSION['payment_data'] = $_POST;

// You can add validation here if needed
$course_id = intval($_POST['course_id']);
$method = $_POST['payment_method'];

$payment_method_text = match ($method) {
    'card' => 'Credit/Debit Card',
    'upi' => 'UPI',
    'net_banking' => 'Net Banking',
    default => 'Unknown'
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Dummy Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .container { max-width: 500px; margin-top: 50px; }
        .card { padding: 30px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm bg-white">
        <h4 class="text-center mb-3">💳 Dummy Payment Confirmation</h4>
        <p><strong>Name:</strong> <?= htmlspecialchars($_POST['student_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_POST['student_email']) ?></p>
        <p><strong>Payment Method:</strong> <?= $payment_method_text ?></p>

        <div class="alert alert-success text-center">This is a dummy payment screen. Click confirm to continue.</div>

        <form action="admin/process_payment.php" method="POST">
            <?php foreach ($_POST as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
            <button type="submit" class="btn btn-success w-100">✅ Confirm Payment</button>
        </form>
    </div>
</div>

</body>
</html>
