-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 02:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moocs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(5, 'Admin', 'admin@example.com', '$2y$10$w35TNU.ycq6PvItRM9FQau6i2QskGnQ/ZAoDuHVI4UoEnIKFA13tm', '2025-07-04 06:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_id`, `title`, `description`, `created_at`) VALUES
(1, 1, 'Assignment 1', 'Complete first project!\r\n', '2025-07-05 05:13:45'),
(2, 2, 'Assignment 1', 'Complete your first Project!', '2025-07-05 05:29:51'),
(3, 5, 'Assignment 1', 'Complete first app!', '2025-07-05 05:56:40'),
(4, 6, 'Assignment 1', 'Complete first project!', '2025-07-05 05:56:56'),
(5, 7, 'Assignment 1', 'Submit your first Project!', '2025-07-05 05:57:26'),
(6, 3, 'Assignment 1', 'Submit your first Project!', '2025-07-05 06:00:40'),
(7, 4, 'Assignment 1', 'Submit your first Project!', '2025-07-05 06:00:46'),
(8, 8, 'Assignment 1', 'Complete your first pro', '2025-07-08 09:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `submission_file` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `marks` int(11) DEFAULT 0,
  `status` enum('pending','submitted','completed') DEFAULT 'pending',
  `grade` varchar(10) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `student_id`, `assignment_id`, `submission_file`, `submitted_at`, `marks`, `status`, `grade`, `file_path`) VALUES
(1, 1, 1, NULL, '2025-07-08 05:24:16', 0, 'completed', '8/10', '../uploads/assignments/1751952256_reportfile.pdf'),
(2, 2, 2, NULL, '2025-07-08 08:22:28', 0, 'completed', NULL, '../uploads/assignments/1751962948_Jeel Resume1.pdf'),
(3, 3, 2, NULL, '2025-07-08 08:25:40', 0, 'completed', NULL, '../uploads/assignments/1751963140_appen.pdf'),
(4, 3, 3, NULL, '2025-07-08 08:25:50', 0, 'completed', NULL, '../uploads/assignments/1751963150_appen.pdf'),
(5, 4, 6, NULL, '2025-07-08 08:28:06', 0, 'completed', NULL, '../uploads/assignments/1751963286_reportfile.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `issued_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `course_id`, `certificate_file`, `issued_at`) VALUES
(1, 1, 1, NULL, '2025-07-08 11:08:24'),
(2, 4, 3, 'certificate_4_3_1751963605.pdf', '2025-07-08 14:03:25'),
(3, 2, 2, 'certificate_2_2_1751976517.pdf', '2025-07-08 17:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`, `created_at`) VALUES
(1, 'Avani Patel', 'avani@gmail.com', 'Payment', 'Payment failed!', '2025-07-08 10:16:30', '2025-07-08 15:46:30');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `videos` text DEFAULT NULL,
  `pdfs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `instructor_id`, `title`, `description`, `image`, `price`, `duration`, `status`, `created_at`, `videos`, `pdfs`) VALUES
(1, 1, 'Web Development', 'Web development involves creating and maintaining websites. It includes everything from building basic static pages to dynamic and complex web applications. Web developers work with HTML, CSS, JavaScript, and back-end technologies like Node.js, Python, and databases to create user-friendly, interactive websites and web apps.', '1751610535_img1.jpg', 3000.00, '3 Months', 'published', '2025-07-04 06:28:55', '[\"686774a7be73b_5762200-uhd_3840_2160_24fps.mp4\"]', '[\"686774a7bea38_reportfile.pdf\"]'),
(2, 1, 'Cyber Security', 'Cybersecurity focuses on protecting computer systems, networks, and data from unauthorized access, attacks, or damage. This course teaches the essential skills to defend against cyber threats like hacking, malware, phishing, and other security risks. Topics include cryptography, network security, ethical hacking, and risk management.', '1751693335_img4.jpg', 5000.00, '6 Months', 'published', '2025-07-05 05:28:17', '[]', '[]'),
(3, 2, 'Data Science', 'Data science is all about analyzing and interpreting large datasets to extract meaningful insights and solve complex problems. It combines statistical analysis, programming, and machine learning. Data scientists use tools like Python, R, SQL, and machine learning algorithms to analyze trends, make predictions, and drive data-driven decision-making.', '1751693478_img3.jpg', 4000.00, '4 Months', 'published', '2025-07-05 05:31:18', '[\"6868b8a6988e0_6876326-hd_1920_1080_25fps.mp4\"]', '[\"6868b8a6998c2_appen.pdf\"]'),
(4, 2, 'Entrepreneurship', 'Entrepreneurship involves identifying business opportunities, taking risks, and creating new ventures or innovations. This course covers business planning, market research, financial management, and startup strategies. It equips learners with the skills to launch and grow successful businesses, whether in the tech industry or other sectors.', '1751693540_img6.jpg', 2500.00, '2 Months', 'published', '2025-07-05 05:32:20', '[\"6868b8e4972a9_5762406-uhd_3840_2160_24fps.mp4\"]', '[\"6868b8e497aff_Jeel Resume1.pdf\"]'),
(5, 3, 'Artificial Intelligence (AI)', 'AI explores the development of machines that can perform tasks typically requiring human intelligence, such as learning, problem-solving, and decision-making. This course covers machine learning, neural networks, natural language processing, and robotics, enabling students to build AI systems that can automate tasks and solve complex problems.', '1751694812_img2.jpg', 3500.00, '3 Months', 'published', '2025-07-05 05:53:32', '[\"6868bddcd725b_5762200-uhd_3840_2160_24fps.mp4\"]', '[]'),
(6, 3, 'Digital Marketing', 'Digital marketing involves promoting products or services through digital channels like social media, search engines, email, and websites. This course teaches strategies for search engine optimization (SEO), content marketing, pay-per-click (PPC) advertising, and social media marketing to drive brand awareness and sales in the digital world.', '1751694885_img5.jpg', 2500.00, '1 Months', 'published', '2025-07-05 05:54:45', '[\"6868be25318a5_6876326-hd_1920_1080_25fps.mp4\"]', '[]'),
(7, 3, 'Information Technology (IT)', 'Information Technology encompasses the use of computers, networks, and software to manage and process information. This course covers areas like IT infrastructure, programming, database management, systems analysis, and IT support. It\'s designed for students to gain a solid understanding of how technology is used to support business operations and solve technical problems.', '1751694954_img7.jpg', 4500.00, '6 Months', 'published', '2025-07-05 05:55:54', '[\"6868be6aabca6_5762200-uhd_3840_2160_24fps.mp4\"]', '[\"6868be6aacda8_photo.pdf\"]'),
(8, 4, 'Machine Learning (ML)', 'Machine Learning is a branch of artificial intelligence that enables computers to learn from data and improve their performance without being explicitly programmed. It involves building algorithms that can identify patterns, make predictions, and automate decision-making processes. Common ML techniques include supervised learning, unsupervised learning, and reinforcement learning. ML is used in applications like recommendation systems, speech recognition, image classification, and fraud detection.', '1751967722_img8.jpg', 4500.00, '5 Months', 'published', '2025-07-08 09:42:02', '[\"686ce7ea07bec_6876326-hd_1920_1080_25fps.mp4\"]', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `student_email` varchar(255) DEFAULT NULL,
  `completion_percent` int(11) DEFAULT 0,
  `payment_status` enum('Pending','Success','Failed') DEFAULT 'Pending',
  `student_name` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_details` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `platform_fee` decimal(10,2) DEFAULT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `status`, `student_email`, `completion_percent`, `payment_status`, `student_name`, `payment_method`, `payment_details`, `amount_paid`, `platform_fee`, `enrolled_at`) VALUES
(1, NULL, 1, 'active', 'jeel@gmail.com', 100, 'Success', 'Jeel Patel', 'card', 'Card: **** **** **** ', 3000.00, 49.00, '2025-07-08 10:17:27'),
(5, NULL, 2, 'active', 'avani@gmail.com', 100, 'Success', 'Avani Patel', 'upi', 'UPI ID: ', 5000.00, 49.00, '2025-07-08 13:51:26'),
(6, NULL, 2, 'active', 'user@gmail.com', 100, 'Success', 'User1', 'net_banking', 'Bank: ', 5000.00, 49.00, '2025-07-08 13:53:39'),
(8, NULL, 5, 'active', 'user@gmail.com', 100, 'Success', 'User1', 'net_banking', 'Bank: ', 3500.00, 49.00, '2025-07-08 13:55:05'),
(9, NULL, 3, 'active', 'user2@gmail.com', 0, 'Success', 'User1', 'upi', 'UPI ID: ', 4000.00, 49.00, '2025-07-08 13:57:23'),
(10, NULL, 1, 'active', 'khushi@gmail.com', 100, 'Success', 'Khushi Patel', 'upi', 'UPI ID: ', 3000.00, 49.00, '2025-07-08 14:35:10'),
(11, NULL, 8, 'active', 'avani@gmail.com', 0, 'Success', 'Avani Patel', 'card', 'Card: **** **** **** ', 4500.00, 49.00, '2025-07-08 15:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `user_id`, `bio`, `expertise`, `rating`, `name`, `email`, `password`, `specialization`, `image`, `created_at`) VALUES
(1, NULL, NULL, NULL, 0, 'Instructor 1', 'instructor1@gmail.com', '$2y$10$gzMTHQsBkrLhs0JAzROgL.v50borzwp4sxOqmff4DGoW0tm9hzzWi', 'Expert in Data Science & AI', 'Instructor2.png', '2025-07-04 11:52:59'),
(2, NULL, NULL, NULL, 0, 'Instructor 2', 'instructor2@gmail.com', '$2y$10$rJXAKQHE1yoLbY5EROzPveZgeIg6t94Qs4Lau2ErtpvowHMiK/Dny', 'Cybersecurity Specialist!', 'Instructor1.png', '2025-07-04 11:55:10'),
(3, NULL, NULL, NULL, 0, 'Instructor 3', 'instructor3@gmail.com', '$2y$10$3cf9GVLCOQsMTD8j1ZbwmeTv00QpBPi5F5dTBO.UEtVQNs/iXgCAC', 'Information Technology', 'Instructor3.png', '2025-07-05 11:22:18'),
(4, NULL, NULL, NULL, 0, 'Instructor 4', 'instructor4@gmail.com', '$2y$10$uYEiwAHRvtI2pwTJLac6fOdxz0N483SIvkfGtPPoyacEwEfklicOO', 'Expert in Machine Learning', 'Instructor1.png', '2025-07-08 15:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `pdf_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT current_timestamp(),
  `review` text DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `preference_key` varchar(100) DEFAULT NULL,
  `preference_value` text DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `user_id`, `preference_key`, `preference_value`, `site_name`, `contact_email`) VALUES
(1, NULL, NULL, NULL, 'My eLearning Site', 'admin@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_date` datetime DEFAULT current_timestamp(),
  `course_id` int(11) DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `instructor_id` int(11) DEFAULT NULL,
  `student_email` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `instructor_earnings` decimal(10,2) DEFAULT NULL,
  `platform_commission` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_details` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `currency`, `status`, `transaction_date`, `course_id`, `requested_at`, `instructor_id`, `student_email`, `total_amount`, `instructor_earnings`, `platform_commission`, `payment_method`, `payment_details`, `paid_at`) VALUES
(1, NULL, 'USD', 'pending', '2025-07-08 10:23:08', 1, '2025-07-08 10:23:08', 1, 'jeel@gmail.com', 3049.00, 3000.00, 49.00, 'card', 'Card: **** **** **** ', NULL),
(2, NULL, 'USD', 'pending', '2025-07-08 13:51:26', 2, '2025-07-08 13:51:26', 1, 'avani@gmail.com', 5049.00, 5000.00, 49.00, 'upi', 'UPI ID: ', NULL),
(3, NULL, 'USD', 'pending', '2025-07-08 13:53:39', 2, '2025-07-08 13:53:39', 1, 'user@gmail.com', 5049.00, 5000.00, 49.00, 'net_banking', 'Bank: ', NULL),
(4, NULL, 'USD', 'pending', '2025-07-08 13:53:41', 2, '2025-07-08 13:53:41', 1, 'user@gmail.com', 5049.00, 5000.00, 49.00, 'net_banking', 'Bank: ', NULL),
(5, NULL, 'USD', 'pending', '2025-07-08 13:55:05', 5, '2025-07-08 13:55:05', 3, 'user@gmail.com', 3549.00, 3500.00, 49.00, 'net_banking', 'Bank: ', NULL),
(6, NULL, 'USD', 'pending', '2025-07-08 13:57:23', 3, '2025-07-08 13:57:23', 2, 'user2@gmail.com', 4049.00, 4000.00, 49.00, 'upi', 'UPI ID: ', NULL),
(7, NULL, 'USD', 'pending', '2025-07-08 14:35:10', 1, '2025-07-08 14:35:10', 1, 'khushi@gmail.com', 3049.00, 3000.00, 49.00, 'upi', 'UPI ID: ', NULL),
(8, NULL, 'USD', 'pending', '2025-07-08 15:22:50', 8, '2025-07-08 15:22:50', 4, 'avani@gmail.com', 4549.00, 4500.00, 49.00, 'card', 'Card: **** **** **** ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','instructor','admin') DEFAULT 'student',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Jeel Patel', 'jeel@gmail.com', '$2y$10$7cjCvCCs1tFbuo3MN2jukORN4y0iYdqKRJ4Xy8iXlZ.OKIBxtHu66', 'student', '2025-07-05 11:32:30'),
(2, 'Avani Patel', 'avani@gmail.com', '$2y$10$5weTVt7cUDAby.kRXZz2pubi5CRJ11.6ASLixv8PznZZ6TaidQXPC', 'student', '2025-07-08 13:51:24'),
(3, 'User1', 'user@gmail.com', '$2y$10$TiuP3O7FE7xMQdadMfDHueNzw.CzIJnt7rzrUtK2IGtQz3Y1rIpCi', 'student', '2025-07-08 13:53:37'),
(4, 'User1', 'user2@gmail.com', '$2y$10$Xw19Nn2GPEirmcQHmbzSYeq7K8ugNXiG374aTptjmKphNWnWpV72a', 'student', '2025-07-08 13:57:21'),
(5, 'Khushi Patel', 'khushi@gmail.com', '$2y$10$4RjZ0wjhFBuAA4h1oYXZJu0U6KXOEPsnVeThxNixi3HMvzDKc1.Ji', 'student', '2025-07-08 14:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `watched_videos`
--

CREATE TABLE `watched_videos` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `watched_at` datetime DEFAULT current_timestamp(),
  `course_id` int(11) DEFAULT NULL,
  `video_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watched_videos`
--

INSERT INTO `watched_videos` (`id`, `student_id`, `watched_at`, `course_id`, `video_name`) VALUES
(2, NULL, '2025-07-08 11:05:02', 1, '[\"686774a7be73b_5762200-uhd_3840_2160_24fps.mp4\"]'),
(3, NULL, '2025-07-08 13:52:05', 2, '[]'),
(4, NULL, '2025-07-08 13:55:24', 5, '[\"6868bddcd725b_5762200-uhd_3840_2160_24fps.mp4\"]'),
(5, NULL, '2025-07-08 13:56:19', 2, '[]'),
(6, NULL, '2025-07-08 13:57:47', 3, '[\"6868b8a6988e0_6876326-hd_1920_1080_25fps.mp4\"]'),
(7, NULL, '2025-07-08 14:46:15', 1, '[\"686774a7be73b_5762200-uhd_3840_2160_24fps.mp4\"]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `watched_videos`
--
ALTER TABLE `watched_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `watched_videos`
--
ALTER TABLE `watched_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `instructors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lectures`
--
ALTER TABLE `lectures`
  ADD CONSTRAINT `lectures_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `watched_videos`
--
ALTER TABLE `watched_videos`
  ADD CONSTRAINT `watched_videos_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
