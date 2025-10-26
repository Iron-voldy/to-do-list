<?php

namespace App\Controllers;

use App\Services\TaskService;

/**
 * Task controller handling HTTP requests
 */
class TaskController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Get recent tasks
     */
    public function index(): void
    {
        try {
            $tasks = $this->taskService->getRecentTasks();
            $this->jsonResponse(['success' => true, 'data' => $tasks], 200);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve tasks'
            ], 500);
        }
    }

    /**
     * Create a new task
     */
    public function store(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ], 400);
                return;
            }

            $task = $this->taskService->createTask($data);
            $this->jsonResponse(['success' => true, 'data' => $task], 201);
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to create task'
            ], 500);
        }
    }

    /**
     * Mark task as completed
     */
    public function complete(int $id): void
    {
        try {
            $success = $this->taskService->completeTask($id);

            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Task completed'], 200);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to complete task'
            ], 500);
        }
    }

    /**
     * Send JSON response
     */
    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
