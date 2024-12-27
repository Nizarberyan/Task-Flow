<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Validate and sanitize the input data
    $username = trim($username);
    $email = trim($email);

    // Check if the username or email already exists in the database
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Username or email already exists
        $response = [
            'success' => false,
            'message' => 'Username or email already exists.'
        ];
    } else {
        // Insert the new user into the database
        $stmt = $pdo->prepare('INSERT INTO users (username, email) VALUES (?, ?)');
        $stmt->execute([$username, $email]);

        $response = [
            'success' => true,
            'message' => 'User added successfully.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
