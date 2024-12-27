<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Classes/Task.php';
require_once __DIR__ . '/src/Classes/Feature.php';
require_once __DIR__ . '/src/Classes/Bug.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
    $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $tasks = array_map(function ($taskData) use ($pdo) {
        return Task::fromArray($taskData, $pdo);
    }, $tasksData);
} catch (PDOException $e) {
    $_SESSION['error'] = "Could not fetch tasks: " . $e->getMessage();
    $tasks = [];
}

// var_dump($tasksData);


require_once __DIR__ . '/src/views/index.php';
