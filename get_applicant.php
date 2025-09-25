<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Applicant ID is required']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    $stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
    $stmt->execute([$id]);
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($applicant) {
        echo json_encode(['success' => true, 'applicant' => $applicant]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Applicant not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>