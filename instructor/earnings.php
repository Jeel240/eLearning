<?php
session_start();
require '../config.php';

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch all transactions for this instructor
$transactions = $conn->query("
  SELECT t.*, c.title 
  FROM transactions t
  JOIN courses c ON c.id = t.course_id
  WHERE t.instructor_id = $instructor_id
  ORDER BY t.requested_at DESC
");

// Calculate total instructor earnings
$summary = $conn->query("
  SELECT SUM(instructor_earnings) AS total_earnings 
  FROM transactions 
  WHERE instructor_id = $instructor_id AND status = 'paid'
")->fetch_assoc();
$totalEarnings = $summary['total_earnings'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Earnings & Transactions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .summary-card {
      background: linear-gradient(135deg, #e3ffe7 0%, #d9e7ff 100%);
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .summary-card h2 {
      font-size: 2rem;
      margin: 0;
    }

    .table thead th {
      white-space: nowrap;
    }

    @media (max-width: 768px) {
      .summary-card h2 {
        font-size: 1.5rem;
      }

      .table td, .table th {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

<div class="container my-5">
  <h3 class="mb-4">💰 Earnings & Transactions</h3>

  <!-- Total Earnings Summary Card -->
  <div class="summary-card mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h6 class="text-muted mb-1">Total Paid Earnings</h6>
      <h2 class="text-success">₹<?= number_format($totalEarnings, 2) ?></h2>
    </div>
    <i class="bi bi-wallet2 fs-1 text-success"></i>
  </div>

  <!-- Transactions Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle bg-white">
      <thead class="table-dark text-center">
        <tr>
          <th>Course</th>
          <th>Total Revenue</th>
          <th>Your Earnings</th>
          <th>Commission</th>
          <th>Status</th>
          <th>Requested At</th>
          <th>Paid On</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($transactions->num_rows > 0): ?>
          <?php while ($row = $transactions->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td>₹<?= number_format($row['total_amount'], 2) ?></td>
              <td class="text-success fw-semibold">₹<?= number_format($row['instructor_earnings'], 2) ?></td>
              <td class="text-danger">₹<?= number_format($row['platform_commission'], 2) ?></td>
              <td class="text-center">
                <?= $row['status'] === 'paid' 
                      ? '<span class="badge bg-success">Paid</span>' 
                      : '<span class="badge bg-warning text-dark">Pending</span>' ?>
              </td>
              <td><?= date('d M Y, h:i A', strtotime($row['requested_at'])) ?></td>
              <td><?= $row['paid_at'] ? date('d M Y, h:i A', strtotime($row['paid_at'])) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted">No transactions found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <a href="instructor_dashboard.php" class="btn btn-outline-secondary mt-4">
    ← Back to Dashboard
  </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

