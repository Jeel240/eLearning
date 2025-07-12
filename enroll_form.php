<?php
session_start();
include 'config.php';

// Fetch courses
$courses = $conn->query("SELECT * FROM courses");

// Preselect course if passed via GET
$selected_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll in a Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-section { max-width: 600px; margin: auto; }
        .form-label { font-weight: 500; }
        .price-tag { font-weight: bold; color: #198754; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="form-section bg-white shadow p-4 rounded">
        <h3 class="text-center mb-4">🎓 Enroll in a Course</h3>

        <form action="dummy_payment.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="student_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="student_email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="student_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Course</label>
                <select name="course_id" class="form-select" id="courseSelect" required>
                    <option value="">-- Choose a Course --</option>
                    <?php while ($row = $courses->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" data-price="<?= $row['price'] ?>"
                            <?= $row['id'] == $selected_course_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div id="coursePrice" class="mt-2"></div>
            </div>

            <hr>
            <h5 class="mb-3">💳 Payment Method</h5>

            <div class="mb-3">
                <label class="form-label">Select Payment Method</label>
                <select name="payment_method" class="form-select" id="paymentMethod" required>
                    <option value="">-- Choose --</option>
                    <option value="card">Credit/Debit Card</option>
                    <option value="upi">UPI</option>
                    <option value="net_banking">Net Banking</option>
                </select>
            </div>

            <!-- Card Fields -->
            <div id="cardFields" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">Card Number</label>
                    <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="month" name="expiry_date" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CVV</label>
                        <input type="password" name="cvv" class="form-control" placeholder="123">
                    </div>
                </div>
            </div>

            <!-- UPI Field -->
            <div id="upiField" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">UPI ID</label>
                    <input type="text" name="upi_id" class="form-control" placeholder="yourname@upi">
                </div>
            </div>

            <!-- Net Banking Field -->
            <div id="netBankingField" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">Select Bank</label>
                    <select name="bank_name" class="form-select">
                        <option value="">-- Select Bank --</option>
                        <option value="HDFC">HDFC Bank</option>
                        <option value="ICICI">ICICI Bank</option>
                        <option value="SBI">SBI</option>
                        <option value="Axis">Axis Bank</option>
                        <option value="BOB">Bank of Baroda</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100">Pay & Enroll</button>
        </form>
    </div>
</div>

<script>
    // Show course price on selection
    document.getElementById("courseSelect").addEventListener("change", function () {
        const price = this.options[this.selectedIndex].getAttribute('data-price');
        document.getElementById("coursePrice").innerHTML = price 
            ? `Course Price: <span class="price-tag">₹${parseFloat(price).toFixed(2)}</span>` 
            : "";
    });

    // Trigger price display if course is already selected
    window.onload = () => {
        const select = document.getElementById("courseSelect");
        if (select.value) select.dispatchEvent(new Event("change"));
    };
</script>

</body>
</html>
