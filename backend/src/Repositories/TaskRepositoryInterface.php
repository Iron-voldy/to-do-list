<?php

namespace App\Repositories;

use App\Models\Task;

/**
 * Task repository interface (Dependency Inversion Principle)
 */
interface TaskRepositoryInterface
{
    /**
     * Get recent incomplete tasks
     *
     * @param int $limit
     * @return Task[]
     */
    public function getRecentIncompleteTasks(int $limit = 5): array;

    /**
     * Create a new task
     *
     * @param Task $task
     * @return Task
     */
    public function create(Task $task): Task;

    /**
     * Mark task as completed
     *
     * @param int $id
     * @return bool
     */
    public function markAsCompleted(int $id): bool;

    /**
     * Find task by ID
     *
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task;
}
