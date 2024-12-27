<?php
// src/Controllers/CreateTask.php

require_once __DIR__ . '/../Classes/Task.php';
require_once __DIR__ . '/../Classes/Feature.php';
require_once __DIR__ . '/../Classes/Bug.php';
require_once __DIR__ . '/../../config/database.php';

session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize database connection
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Sanitize and validate input data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $type = trim($_POST['taskType']);
    $feature_type = isset($_POST['featureType']) ? trim($_POST['featureType']) : null;
    $target_date = isset($_POST['targetDate']) ? trim($_POST['targetDate']) : null;
    $severity = isset($_POST['severity']) ? trim($_POST['severity']) : null;
    $priority = trim($_POST['priority']);
    $assigned_to = isset($_POST['assigned_to']) && !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

    $errors = [];

    // Validate required fields
    if (empty($title)) {
        $errors[] = "Title is required.";
    }

    if (empty($type) || !in_array($type, ['feature', 'bug'])) {
        $errors[] = "Invalid task type.";
    }

    if (empty($priority) || !in_array($priority, ['low', 'medium', 'high'])) {
        $errors[] = "Invalid priority level.";
    }

    // Additional validations based on task type
    if ($type === 'feature') {
        if (empty($feature_type)) {
            $errors[] = "Feature type is required for feature tasks.";
        }
        if (empty($target_date)) {
            $errors[] = "Target date is required for feature tasks.";
        }
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $target_date)) {
            $errors[] = "Invalid target date format.";
        }
    }

    if ($type === 'bug') {
        if (empty($severity) || !in_array($severity, ['low', 'medium', 'high', 'critical'])) {
            $errors[] = "Invalid severity level for bug tasks.";
        }
    }

    // If there are no validation errors, proceed to create the task
    if (empty($errors)) {
        try {
            if ($type === 'feature') {
                $task = new Feature($pdo);
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'type' => $type,
                    'status' => 'pending', // Default status
                    'priority' => $priority,
                    'feature_type' => $feature_type,
                    'target_date' => $target_date,
                    'assigned_to' => $assigned_to
                ];
            } elseif ($type === 'bug') {
                $task = new Bug($pdo);
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'type' => $type,
                    'status' => 'pending', // Default status
                    'priority' => $priority,
                    'severity' => $severity,
                    'bug_priority' => $priority, // Assuming bug priority aligns with main priority
                    'assigned_to' => $assigned_to
                ];
            }

            // Debugging: Log the $data array
            error_log("Task data: " . print_r($data, true));

            // Create the task
            $result = $task->create($data);

            // Debugging: Log the result of task creation
            error_log("Task creation result: " . var_export($result, true));

            if ($result) {
                $_SESSION['success'] = "Task created successfully!";
                header("Location: /src/views/index.php");
                exit();
            } else {
                $errors[] = "Failed to create task. Please try again.";
            }

            // Return a JSON response
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            // Debugging: Log the exception message
            error_log("An error occurred: " . $e->getMessage());

            $errors[] = "An error occurred: " . $e->getMessage();

            // Return a JSON response with error message
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
            exit;
        }
    }

    // If there are validation errors, return a JSON response with errors
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
} else {
    // If accessed without POST method, redirect to main page
    header("Location: /src/views/index.php");
    exit();
}
