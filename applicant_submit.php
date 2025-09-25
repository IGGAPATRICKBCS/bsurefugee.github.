<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$requiredFields = ['name', 'dob', 'email', 'phone', 'country', 'language', 'program', 'education_level'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "$field is required"]);
        exit;
    }
}

// Validate file uploads
if (empty($_FILES['id_document']['name']) || empty($_FILES['academic_document']['name'])) {
    echo json_encode(['success' => false, 'message' => 'Both ID and Academic documents are required']);
    exit;
}

// File upload configuration
$uploadDir = 'uploads/';
$allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Process file uploads
$idDocumentPath = '';
$academicDocumentPath = '';

try {
    // Process ID document
    $idFile = $_FILES['id_document'];
    $idExt = strtolower(pathinfo($idFile['name'], PATHINFO_EXTENSION));
    $idFileName = uniqid('id_', true) . '.' . $idExt;
    $idTargetPath = $uploadDir . $idFileName;
    
    if (!in_array($idExt, $allowedTypes)) {
        throw new Exception('ID document must be JPG, PNG, or PDF');
    }
    
    if ($idFile['size'] > $maxSize) {
        throw new Exception('ID document is too large (max 5MB)');
    }
    
    if (!move_uploaded_file($idFile['tmp_name'], $idTargetPath)) {
        throw new Exception('Failed to upload ID document');
    }
    
    $idDocumentPath = $idTargetPath;

    // Process academic document
    $academicFile = $_FILES['academic_document'];
    $academicExt = strtolower(pathinfo($academicFile['name'], PATHINFO_EXTENSION));
    $academicFileName = uniqid('academic_', true) . '.' . $academicExt;
    $academicTargetPath = $uploadDir . $academicFileName;
    
    if (!in_array($academicExt, $allowedTypes)) {
        throw new Exception('Academic document must be JPG, PNG, or PDF');
    }
    
    if ($academicFile['size'] > $maxSize) {
        throw new Exception('Academic document is too large (max 5MB)');
    }
    
    if (!move_uploaded_file($academicFile['tmp_name'], $academicTargetPath)) {
        throw new Exception('Failed to upload academic document');
    }
    
    $academicDocumentPath = $academicTargetPath;

    // Insert applicant data
    $stmt = $conn->prepare("INSERT INTO applicants (
        name, dob, email, phone, country, language, program, 
        education_level, id_document_path, academic_document_path, background
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['name'],
        $_POST['dob'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['country'],
        $_POST['language'],
        $_POST['program'],
        $_POST['education_level'],
        $idDocumentPath,
        $academicDocumentPath,
        $_POST['background'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully!']);
} catch (Exception $e) {
    // Clean up uploaded files if there was an error
    if (file_exists($idDocumentPath)) unlink($idDocumentPath);
    if (file_exists($academicDocumentPath)) unlink($academicDocumentPath);
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>