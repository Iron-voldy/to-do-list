<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;

/**
 * Task service handling business logic (Single Responsibility Principle)
 */
class TaskService
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get recent incomplete tasks
     *
     * @return array
     */
    public function getRecentTasks(): array
    {
        $tasks = $this->taskRepository->getRecentIncompleteTasks(5);

        return array_map(function (Task $task) {
            return $task->toArray();
        }, $tasks);
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createTask(array $data): array
    {
        $this->validateTaskData($data);

        $task = new Task(
            $data['title'],
            $data['description'] ?? ''
        );

        $createdTask = $this->taskRepository->create($task);

        return $createdTask->toArray();
    }

    /**
     * Mark task as completed
     *
     * @param int $id
     * @return bool
     */
    public function completeTask(int $id): bool
    {
        return $this->taskRepository->markAsCompleted($id);
    }

    /**
     * Validate task data
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateTaskData(array $data): void
    {
        if (empty($data['title']) || !is_string($data['title'])) {
            throw new \InvalidArgumentException('Title is required and must be a string');
        }

        if (strlen($data['title']) > 255) {
            throw new \InvalidArgumentException('Title must not exceed 255 characters');
        }

        if (isset($data['description']) && !is_string($data['description'])) {
            throw new \InvalidArgumentException('Description must be a string');
        }
    }
}
