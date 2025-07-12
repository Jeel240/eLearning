<?php
require 'config.php';

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if ($term !== '') {
    $stmt = $conn->prepare("SELECT id, title FROM courses WHERE title LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['id'],
            'title' => $row['title']
        ];
    }

    echo json_encode($suggestions);
}
?>
