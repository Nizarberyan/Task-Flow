<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Classes/Task.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];

    // Retrieve the task from the database
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    $taskData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($taskData) {
        $task = Task::fromArray($taskData, $pdo);

        // Move the task to the next category based on its current status
        switch ($task->getStatus()) {
            case 'todo':
                $task->setStatus('in_progress');
                break;
            case 'in_progress':
                $task->setStatus('completed');
                break;
            default:
                // If the task is already in the final category or an unknown status, do nothing
                break;
        }

        // Update the task in the database
        $stmt = $pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        $stmt->execute([$task->getStatus(), $task->getId()]);
    }

    // Redirect back to the index page
    header('Location: /');
    exit();
}
