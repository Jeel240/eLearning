<?php
session_start();
include 'config.php'; 

// Fetch top 3 rated instructors
$topInstructors = mysqli_query($conn, "SELECT * FROM instructors ORDER BY rating DESC LIMIT 3");
$courses = mysqli_query($conn, "SELECT * FROM courses LIMIT 6");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOCs</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow sticky-top">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="index.html">MOOCs</a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>

                <!-- Search Bar -->
                <form class="d-flex me-3" action="course_details.php" method="GET" id="searchForm">
                    <div class="position-relative me-2">
                        <input 
                            class="form-control pe-5 search-bar" 
                            list="courseSuggestions" 
                            name="course_query" 
                            id="courseSearch" 
                            placeholder="Search Courses..." 
                            autocomplete="off" 
                            required >
                        <button type="button" class="btn btn-sm btn-close position-absolute top-50 end-0 translate-middle-y me-2" id="clearSearch" style="display: none;"></button>
                    </div>
                    <datalist id="courseSuggestions"></datalist>
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>

                <!-- User Dropdown (Login/Register or Profile) -->
                <ul class="navbar-nav">
                <?php if (isset($_SESSION['student_id'])): ?>
                    <li class="nav-item">
                        <a href="student/stu_dashboard.php" class="nav-link d-flex align-items-center" title="Go to Dashboard">
                            <i class="bi bi-person-circle fs-4 text-primary"></i>
                            <span class="ms-2 d-none d-md-inline"><?= $_SESSION['student_name'] ?? 'Dashboard' ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
                </ul> 
            </div>
        </div>
    </nav>

    <!-- Home Section -->
    <section class="home-section py-5" id="home">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold">
                        Start Your Learning Journey with <span class="text-primary">MOOCs</span>
                    </h1>
                    <p class="lead mt-3 text-muted">
                        Join thousands of students worldwide and explore expert-taught courses in various fields.
                        Learn at your own pace from the best instructors.
                    </p>
                    <a href="courses.php" class="btn btn-primary btn-lg mt-4">
                        🎓 Browse Courses
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Page Header -->
    <section class="page-header"  id="courses">
        <div class="container">
            <h1>Explore Our Courses</h1>
            <p>Boost your skills with our high-quality online courses</p>
        </div>
    </section>

    <!-- Courses Grid -->
    <section class="container py-5" id="courses">
        <div class="row g-4">
            <?php while ($course = mysqli_fetch_assoc($courses)) : ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100">
                        <img src="uploads/<?= htmlspecialchars($course['image']) ?>" class="card-img-top" alt="<?= $course['title'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                            <p class="card-text"><?= substr(htmlspecialchars($course['description']), 0, 100) ?>...</p>
                            <a href="course_details.php?id=<?= $course['id'] ?>" class="btn enroll-btn">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="courses.php" class="btn btn-primary btn-lg mt-4">Explore More Courses</a>
        </div>
    </section>

    <!-- Hero Section -->
    <section class="hero" id="about">
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
    <section class="team-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">👩‍🏫 Meet Our Instructors</h2>
            <div class="row g-4">
                <?php while ($ins = mysqli_fetch_assoc($topInstructors)) { ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 text-center shadow-sm border-0">
                            <img src="uploads/<?= htmlspecialchars($ins['image']) ?>" class="card-img-top rounded-circle mx-auto mt-4" alt="<?= htmlspecialchars($ins['name']) ?>" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($ins['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($ins['specialization']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <div class="row mt-4">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form">
                        <h3 class="text-center mb-4">Get In Touch</h3>
                        <form action="admin/contact_form_handler.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Your Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-submit">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <!-- About Us -->
                <div class="col-lg-4 col-md-6">
                    <h4>About MOOCs</h4>
                    <p>Empowering learners worldwide with top-quality online courses from industry experts.</p>
                </div>
    
                <!-- Quick Links -->
                <div class="col-lg-4 col-md-6">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#courses">Courses</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>
    
                <!-- Contact Info -->
                <div class="col-lg-4 col-md-12">
                    <h4>Contact Us</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Learning Street, Edutech City</li>
                        <li><i class="fas fa-phone-alt"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope"></i> support@moocs.com</li>
                    </ul>
    
                    <!-- Social Media Links -->
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
    
            <!-- Copyright -->
            <div class="text-center mt-4">
                <p>&copy; 2025 MOOCs Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>
    

    <script>
        document.getElementById("courseSearch").addEventListener("input", function () {
            const query = this.value;

            fetch(`search_suggestions.php?term=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    const datalist = document.getElementById("courseSuggestions");
                    datalist.innerHTML = '';

                    data.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.title;
                        option.setAttribute('data-id', item.id);
                        datalist.appendChild(option);
                    });
                });
        });

        // Redirect to course_details.php?id= when clicked
        document.getElementById("searchForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const input = document.getElementById("courseSearch");
            const selected = input.value;
            const datalist = document.getElementById("courseSuggestions").options;

            for (let option of datalist) {
                if (option.value.toLowerCase() === selected.toLowerCase()) {
                    const courseId = option.getAttribute("data-id");
                    if (courseId) {
                        window.location.href = `course_details.php?id=${courseId}`;
                        return;
                    }
                }
            }

            alert("Course not found!");
        });
    </script>

    <script>
        const input = document.getElementById('courseSearch');
        const clearBtn = document.getElementById('clearSearch');

        // Show the clear button only when input has value
        input.addEventListener('input', () => {
            clearBtn.style.display = input.value ? 'block' : 'none';
        });

        // Clear the input when clicking the clear button
        clearBtn.addEventListener('click', () => {
            input.value = '';
            clearBtn.style.display = 'none';
            input.focus(); // optional: return focus to input
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
     <script src="js/script.js"></script>

</body>
</html>
