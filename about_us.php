<?php
include 'config.php'; 

$sql = "SELECT * FROM instructors";
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>About Us</h1>
            <p>Empowering learners worldwide with high-quality online courses</p>
        </div>
    </section>

    <!-- Who We Are & Mission -->
    <section class="about-section">
        <div class="container">
            <div class="row g-4">
                <!-- Who We Are -->
                <div class="col-lg-6">
                    <div class="info-box">
                        <i class="fas fa-users icon"></i>
                        <h3>Who We Are</h3>
                        <ul>
                            <li>A team of experienced instructors and industry leaders.</li>
                            <li>Serving learners in <strong>50+ countries</strong> with diverse courses.</li>
                            <li>Using the latest tech like <strong>AI, VR & interactive content</strong>.</li>
                            <li>Learn anytime, anywhere, at your own pace.</li>
                            <li>A strong <strong>community of learners & expert mentors</strong>.</li>
                            <li>Courses accredited by <strong>top universities & institutions</strong>.</li>
                            <li>Hands-on projects & job-ready skills for career growth.</li>
                            <li>Affordable learning with scholarships & free courses.</li>
                        </ul>
                    </div>
                </div>

                <!-- Our Mission -->
                <div class="col-lg-6">
                    <div class="info-box">
                        <i class="fas fa-bullseye icon"></i>
                        <h3>Our Mission</h3>
                        <ul>
                            <li>Making high-quality education accessible to everyone, everywhere.</li>
                            <li>Providing industry-relevant courses to bridge the <strong>skill gap</strong>.</li>
                            <li>Encouraging <strong>lifelong learning</strong> through flexible programs.</li>
                            <li>Offering <strong>real-world projects & hands-on training</strong>.</li>
                            <li>Helping students with <strong>certifications, internships & job placement</strong>.</li>
                            <li>Using the latest tech like <strong>AI, VR & gamification</strong> to enhance learning.</li>
                            <li>Providing <strong>scholarships & financial aid</strong> to underserved learners.</li>
                            <li>Partnering with <strong>top universities & industry leaders</strong> for recognition.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet the Team Section -->
    <section class="team">
        <div class="container">
            <h2>Meet Our Instructors</h2>
            <div class="row g-4 mt-4">
                <?php while ($instructor = $result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="team-member text-center">
                            <img src="uploads/<?= htmlspecialchars($instructor['image']); ?>" alt="Instructor">
                            <h4><?= htmlspecialchars($instructor['name']); ?></h4>
                            <p><?= htmlspecialchars($instructor['specialization']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

</body>
</html>