<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Classes/User.php';

$db = Database::getInstance();
$pdo = $db->getConnection();
$stmt = $pdo->query('SELECT * FROM users ORDER BY username ASC');
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users = array_map(function ($userData) {
    return User::fromArray($userData);
}, $usersData);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Users - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <header class="border-b">
        <nav class="flex items-center justify-between p-4 max-w-6xl mx-auto">
            <div class="flex items-center space-x-8">
                <h1 class="text-2xl font-normal text-gray-700">TaskFlow</h1>
                <div class="flex space-x-4 text-sm">
                    <a href="/" class="text-gray-600 hover:text-gray-900">My Tasks</a>
                    <a href="/" class="text-gray-600 hover:text-gray-900">All Tasks</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/manage-users.php" class="text-gray-600 hover:text-gray-900">Manage Users</a>
                <a href="/logout.php" class="text-gray-600 hover:text-gray-900">Logout</a>
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-normal">Manage Users</h2>
            <button id="addUserBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Username</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user->getUsername()); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td class="border px-4 py-2">
                            <a href="/edit-user.php?id=<?php echo $user->getId(); ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <a href="/delete-user.php?id=<?php echo $user->getId(); ?>" class="text-red-500 hover:text-red-700">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-gray-50 px-4 py-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add User</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form id="addUserForm">
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" id="cancelAddUserBtn" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">Cancel</button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/public/js/CreateUser.js" defer></script>
</body>

</html>