<?php
include '../config.php';

// Fetch all courses from the database
$sql = "SELECT * FROM courses ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOCs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Explore Our Courses</h1>
        <p>Boost your skills with our high-quality online courses</p>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <?php while ($course = $result->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card course-card">
                    <img src="..images/<?= htmlspecialchars($course['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($course['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($course['title']); ?></h5>
                        <p class="card-text"><?= substr(htmlspecialchars($course['description']), 0, 100); ?>...</p>
                        <p class="fw-bold">$<?= number_format($course['price'], 2); ?></p>
                        <a href="course_details.php?id=<?= $course['id']; ?>" class="btn enroll-btn">Enroll Now</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

</body>
</html>
