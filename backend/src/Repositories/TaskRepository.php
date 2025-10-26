<?php

namespace App\Repositories;

use App\Models\Task;
use PDO;

/**
 * Task repository implementation
 */
class TaskRepository implements TaskRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get recent incomplete tasks
     *
     * @param int $limit
     * @return Task[]
     */
    public function getRecentIncompleteTasks(int $limit = 5): array
    {
        $sql = "SELECT * FROM task
                WHERE completed = FALSE
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = [];
        while ($row = $stmt->fetch()) {
            $tasks[] = Task::fromArray($row);
        }

        return $tasks;
    }

    /**
     * Create a new task
     *
     * @param Task $task
     * @return Task
     */
    public function create(Task $task): Task
    {
        $sql = "INSERT INTO task (title, description, completed)
                VALUES (:title, :description, :completed)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $task->getTitle());
        $stmt->bindValue(':description', $task->getDescription());
        $stmt->bindValue(':completed', $task->isCompleted(), PDO::PARAM_BOOL);
        $stmt->execute();

        $id = (int)$this->db->lastInsertId();
        $task->setId($id);

        return $task;
    }

    /**
     * Mark task as completed
     *
     * @param int $id
     * @return bool
     */
    public function markAsCompleted(int $id): bool
    {
        $sql = "UPDATE task SET completed = TRUE WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Find task by ID
     *
     * @param int $id
     * @return Task|null
     */
    public function findById(int $id): ?Task
    {
        $sql = "SELECT * FROM task WHERE id = :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row ? Task::fromArray($row) : null;
    }
}
