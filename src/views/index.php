<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/../Classes/Task.php';
require_once __DIR__ . '/../Classes/Feature.php';
require_once __DIR__ . '/../Classes/Bug.php';

// Fetch users for assignment (if not already included)
$db = Database::getInstance();
$pdo = $db->getConnection();

// Fetch tasks from the database
$db = Database::getInstance();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
    $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $tasks = array_map(function ($taskData) use ($pdo) {
        return Task::fromArray($taskData, $pdo);
    }, $tasksData);
} catch (PDOException $e) {
    // Handle the exception, e.g., log the error, set $tasks to an empty array
    error_log("Could not fetch tasks: " . $e->getMessage());
    $tasks = [];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/public/js/index.js" defer></script>
    <script src="/public/js/CreateTask.js" defer></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <header class="border-b">
        <nav class="flex items-center justify-between p-4 max-w-6xl mx-auto">
            <div class="flex items-center space-x-8">
                <h1 class="text-2xl font-normal text-gray-700">TaskFlow</h1>
                <div class="flex space-x-4 text-sm">
                    <a href="#" class="text-gray-600 hover:text-gray-900">My Tasks</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">All Tasks</a>
                    <a href="/src/views/manage users.php" class="text-gray-600 hover:text-gray-900">Manage Users</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button
                    id="newTaskButton"
                    class="text-sm bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    + New Task
                </button>
                <img
                    src="../assets/images/avatar.jpg"
                    alt="User Avatar"
                    class="w-8 h-8 rounded-full" />
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto mt-12 px-4">
        <div class="flex justify-center mb-8">
            <div class="w-full max-w-2xl">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search tasks..."
                        class="w-full px-5 py-3 rounded-full border focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm" />
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <svg
                            class="w-5 h-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Three Columns Layout -->
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <!-- Pending Column -->
            <div class="w-full md:w-1/3 bg-gradient-to-r from-yellow-100 to-yellow-200 border border-yellow-300 rounded-lg p-4 shadow-md">
                <h2 class="flex items-center text-xl font-semibold mb-4 text-yellow-800">
                    <!-- Pending Icon -->
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pending
                </h2>
                <div class="space-y-4">
                    <?php if (empty($tasks)): ?>
                        <p class="text-gray-500">No tasks available.</p>
                    <?php else: ?>
                        <?php foreach ($tasks as $task) : ?>
                            <?php if ($task->getStatus() === 'pending') : ?>
                                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200 hover:shadow-lg transition-shadow flex flex-col">
                                    <!-- Pills Container -->
                                    <div class="flex space-x-2 mb-2">
                                        <!-- Priority Badge -->
                                        <span
                                            class="<?php
                                                    switch ($task->getPriority()) {
                                                        case 'urgent':
                                                            echo 'bg-red-200 text-red-800';
                                                            break;
                                                        case 'high':
                                                            echo 'bg-red-200 text-red-800';
                                                            break;
                                                        case 'medium':
                                                            echo 'bg-yellow-200 text-yellow-800';
                                                            break;
                                                        case 'low':
                                                            echo 'bg-green-200 text-green-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-200 text-gray-800';
                                                    }
                                                    ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo ucfirst(htmlspecialchars($task->getPriority(), ENT_QUOTES, 'UTF-8')); ?>
                                        </span>

                                        <!-- Type Badge -->
                                        <span
                                            class="<?php
                                                    switch ($task->getType()) {
                                                        case 'feature':
                                                            echo 'bg-blue-200 text-blue-800';
                                                            break;
                                                        case 'bug':
                                                            echo 'bg-red-200 text-red-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-200 text-gray-800';
                                                    }
                                                    ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo ucfirst(htmlspecialchars($task->getType(), ENT_QUOTES, 'UTF-8')); ?>
                                        </span>

                                        <!-- Severity Badge (Only for Bugs) -->
                                        <?php if ($task->getType() === 'bug') : ?>
                                            <span
                                                class="<?php
                                                        switch (strtolower($task->getSeverity())) {
                                                            case 'critical':
                                                                echo 'bg-purple-700 text-white';
                                                                break;
                                                            case 'high':
                                                                echo 'bg-purple-500 text-white';
                                                                break;
                                                            case 'medium':
                                                                echo 'bg-purple-300 text-purple-800';
                                                                break;
                                                            case 'low':
                                                                echo 'bg-purple-100 text-purple-800';
                                                                break;
                                                            default:
                                                                echo 'bg-gray-200 text-gray-800';
                                                        }
                                                        ?> px-2 py-1 rounded-full text-xs font-semibold">
                                                <?php echo ucfirst(htmlspecialchars($task->getSeverity(), ENT_QUOTES, 'UTF-8')); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Task Details -->
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-800 mb-1">
                                            <?php echo htmlspecialchars($task->getTitle(), ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>

                                        <p class="text-sm text-gray-700"><?php echo $task->getDescription() . "<br> Assigned To: " . ($task->getAssignedTo() ? $task->getAssignedTo()->getUserName() : 'N/A'); ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?php if ($task->getType() === 'feature'): ?>
                                                Feature Type: <?php echo htmlspecialchars($task->getFeatureType(), ENT_QUOTES, 'UTF-8'); ?><br>
                                                Target Date: <?php echo htmlspecialchars($task->getTargetDate()->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
                                            <?php elseif ($task->getType() === 'bug'): ?>
                                                Severity:
                                                <?php
                                                // Severity Badge is already displayed above
                                                ?>
                                                Created At: <?php echo htmlspecialchars($task->getCreatedAt()->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <!-- Move to Next Category Button -->
                                    <div class="mt-4">
                                        <form action="/move-task.php" method="POST" class="move-task-form">
                                            <input type="hidden" name="task_id" value="<?php echo $task->getId(); ?>">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-full shadow-md transition duration-300 ease-in-out">
                                                Move to In Progress
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="w-full md:w-1/3 bg-gradient-to-r from-blue-100 to-blue-200 border border-blue-300 rounded-lg p-4 shadow-md">
                <h2 class="flex items-center text-xl font-semibold mb-4 text-blue-800">
                    <!-- In Progress Icon -->
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    In Progress
                </h2>
                <div class="space-y-4">
                    <?php foreach ($tasks as $task) : ?>
                        <?php if ($task->getStatus() === 'in_progress') : ?>
                            <div
                                class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200 hover:shadow-lg transition-shadow flex flex-col">
                                <!-- Pills Container -->
                                <div class="flex space-x-2 mb-2">
                                    <!-- Priority Badge -->
                                    <span
                                        class="<?php
                                                switch ($task->getPriority()) {
                                                    case 'high':
                                                        echo 'bg-red-200 text-red-800';
                                                        break;
                                                    case 'medium':
                                                        echo 'bg-yellow-200 text-yellow-800';
                                                        break;
                                                    case 'low':
                                                        echo 'bg-green-200 text-green-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-200 text-gray-800';
                                                }
                                                ?> px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo ucfirst(htmlspecialchars($task->getPriority(), ENT_QUOTES, 'UTF-8')); ?>
                                    </span>

                                    <!-- Type Badge -->
                                    <span
                                        class="<?php
                                                switch ($task->getType()) {
                                                    case 'feature':
                                                        echo 'bg-blue-200 text-blue-800';
                                                        break;
                                                    case 'bug':
                                                        echo 'bg-red-200 text-red-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-200 text-gray-800';
                                                }
                                                ?> px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo ucfirst(htmlspecialchars($task->getType(), ENT_QUOTES, 'UTF-8')); ?>
                                    </span>

                                    <!-- Severity Badge (Only for Bugs) -->
                                    <?php if ($task->getType() === 'bug') : ?>
                                        <span
                                            class="<?php
                                                    switch (strtolower($task->getSeverity())) {
                                                        case 'critical':
                                                            echo 'bg-purple-700 text-white';
                                                            break;
                                                        case 'high':
                                                            echo 'bg-purple-500 text-white';
                                                            break;
                                                        case 'medium':
                                                            echo 'bg-purple-300 text-purple-800';
                                                            break;
                                                        case 'low':
                                                            echo 'bg-purple-100 text-purple-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-200 text-gray-800';
                                                    }
                                                    ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo ucfirst(htmlspecialchars($task->getSeverity(), ENT_QUOTES, 'UTF-8')); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Task Details -->
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800 mb-1">
                                        <?php echo htmlspecialchars($task->getTitle(), ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <p class="text-sm text-gray-700"><?php echo $task->getDescription() . "<br> Assigned To: " . ($task->getAssignedTo() ? $task->getAssignedTo()->getUserName() : 'N/A'); ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?php if ($task->getType() === 'feature'): ?>
                                            Feature Type: <?php echo htmlspecialchars($task->getFeatureType(), ENT_QUOTES, 'UTF-8'); ?><br>
                                            Target Date: <?php echo htmlspecialchars($task->getTargetDate()->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php elseif ($task->getType() === 'bug'): ?>
                                            Severity:
                                            <?php
                                            // Severity Badge is already displayed above
                                            ?>
                                            Created At: <?php echo htmlspecialchars($task->getCreatedAt()->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <!-- Move to Next Category Button -->
                                <div class="mt-4">
                                    <form action="/move-task.php" method="POST" class="move-task-form">
                                        <input type="hidden" name="task_id" value="<?php echo $task->getId(); ?>">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-full shadow-md transition duration-300 ease-in-out">
                                            Move to Completed
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Completed Column -->
            <div class="w-full md:w-1/3 bg-gradient-to-r from-green-100 to-green-200 border border-green-300 rounded-lg p-4 shadow-md">
                <h2 class="flex items-center text-xl font-semibold mb-4 text-green-800">
                    <!-- Completed Icon -->
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    Completed
                </h2>
                <div class="space-y-4">
                    <?php foreach ($tasks as $task) : ?>
                        <?php if ($task->getStatus() === 'completed') : ?>
                            <div
                                class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200 hover:shadow-lg transition-shadow flex flex-col">
                                <!-- Pills Container -->
                                <div class="flex space-x-2 mb-2">
                                    <!-- Priority Badge -->
                                    <span
                                        class="<?php
                                                switch ($task->getPriority()) {
                                                    case 'high':
                                                        echo 'bg-red-200 text-red-800';
                                                        break;
                                                    case 'medium':
                                                        echo 'bg-yellow-200 text-yellow-800';
                                                        break;
                                                    case 'low':
                                                        echo 'bg-green-200 text-green-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-200 text-gray-800';
                                                }
                                                ?> px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo ucfirst(htmlspecialchars($task->getPriority(), ENT_QUOTES, 'UTF-8')); ?>
                                    </span>

                                    <!-- Type Badge -->
                                    <span
                                        class="<?php
                                                switch ($task->getType()) {
                                                    case 'feature':
                                                        echo 'bg-blue-200 text-blue-800';
                                                        break;
                                                    case 'bug':
                                                        echo 'bg-red-200 text-red-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-200 text-gray-800';
                                                }
                                                ?> px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo ucfirst(htmlspecialchars($task->getType(), ENT_QUOTES, 'UTF-8')); ?>
                                    </span>

                                    <!-- Severity Badge (Only for Bugs) -->
                                    <?php if ($task->getType() === 'bug') : ?>
                                        <span
                                            class="<?php
                                                    switch (strtolower($task->getSeverity())) {
                                                        case 'critical':
                                                            echo 'bg-purple-700 text-white';
                                                            break;
                                                        case 'high':
                                                            echo 'bg-purple-500 text-white';
                                                            break;
                                                        case 'medium':
                                                            echo 'bg-purple-300 text-purple-800';
                                                            break;
                                                        case 'low':
                                                            echo 'bg-purple-100 text-purple-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-200 text-gray-800';
                                                    }
                                                    ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?php echo ucfirst(htmlspecialchars($task->getSeverity(), ENT_QUOTES, 'UTF-8')); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Task Details -->
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800 mb-1">
                                        <?php echo $task->getTitle(); ?>
                                    </h3>
                                    <p class="text-sm text-gray-700"><?php echo $task->getDescription() . "<br> Assigned To: " . ($task->getAssignedTo() ? $task->getAssignedTo()->getUserName() : 'N/A'); ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?php if ($task->getType() === 'feature'): ?>
                                            Feature Type: <?php echo $task->getFeatureType(); ?><br>
                                            Target Date: <?php echo $task->getTargetDate()->format('Y-m-d'); ?>
                                        <?php elseif ($task->getType() === 'bug'): ?>
                                            Severity:
                                            <?php
                                            // Severity Badge is already displayed above
                                            ?>
                                            Created At: <?php echo htmlspecialchars($task->getCreatedAt()->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
            </div>
        </div>
        </div>
    </main>

    <!-- Create Task Modal -->
    <div
        id="createTaskModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div
            class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Create New Task
                </h3>
                <form id="createTaskForm" action="../../src/Controllers/CreateTask.php" method="POST">
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Task Type</label>
                            <select
                                name="taskType"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="feature">Feature</option>
                                <option value="bug">Bug</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input
                                type="text"
                                name="title"
                                required
                                minlength="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea
                                name="description"
                                required
                                minlength="10"
                                rows="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"></textarea>
                        </div>

                        <div id="featureFields">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Feature Type</label>
                                <select
                                    name="featureType"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                    <option value="feature">Feature</option>
                                    <option value="improvement">Improvement</option>
                                    <option value="bugfix">Bugfix</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Target Date</label>
                                <input
                                    type="date"
                                    name="targetDate"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" />
                            </div>
                        </div>

                        <div id="bugFields" class="hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Severity</label>
                                <select
                                    name="severity"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Priority</label>
                                <select
                                    name="priority"
                                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assign To</label>
                            <select
                                name="assigned_to"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="">Select User</option>
                                <?php
                                $users = $db->query("SELECT id, username, email FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($users as $user) {
                                    echo '<option value="' . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . '">' .
                                        htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . ')</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button
                                type="button"
                                onclick="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                Create Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.move-task-form').submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var taskId = form.find('input[name="task_id"]').val();

                $.ajax({
                    type: 'POST',
                    url: '/move-task.php',
                    data: {
                        task_id: taskId
                    },
                    success: function(response) {
                        // Reload the page after successful task movement
                        location.reload();
                    },
                    error: function() {
                        alert('An error occurred while moving the task. Please try again.');
                    }
                });
            });
        });
    </script>

</body>

</html>