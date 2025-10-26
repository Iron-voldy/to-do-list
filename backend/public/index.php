<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Controllers\TaskController;
use App\Middleware\CorsMiddleware;
use App\Repositories\TaskRepository;
use App\Services\TaskService;

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Handle CORS
CorsMiddleware::handle();

// Get database connection
try {
    $db = Database::getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Dependency injection
$taskRepository = new TaskRepository($db);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService);

// Simple routing
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path if API is accessed via /api
$requestUri = preg_replace('#^/api#', '', $requestUri);

// Route requests
switch ($requestUri) {
    case '/tasks':
        if ($requestMethod === 'GET') {
            $taskController->index();
        } elseif ($requestMethod === 'POST') {
            $taskController->store();
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;

    case (preg_match('#^/tasks/(\d+)/complete$#', $requestUri, $matches) ? true : false):
        if ($requestMethod === 'PUT' || $requestMethod === 'PATCH') {
            $taskController->complete((int)$matches[1]);
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;

    case '/health':
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'API is healthy']);
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
