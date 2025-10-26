<?php

namespace Tests\Integration\Repositories;

use App\Models\Task;
use App\Repositories\TaskRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    private PDO $pdo;
    private TaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create in-memory SQLite database for testing
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create table
        $this->pdo->exec('
            CREATE TABLE task (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                completed BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $this->repository = new TaskRepository($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        parent::tearDown();
    }

    public function testCreateTaskInsertsTaskIntoDatabase(): void
    {
        $task = new Task('Test Task', 'Test Description');
        $createdTask = $this->repository->create($task);

        $this->assertNotNull($createdTask->getId());
        $this->assertEquals('Test Task', $createdTask->getTitle());
        $this->assertEquals('Test Description', $createdTask->getDescription());
    }

    public function testGetRecentIncompleteTasksReturnsOnlyIncompleteTasks(): void
    {
        // Create some tasks
        $this->repository->create(new Task('Task 1', 'Description 1'));
        $this->repository->create(new Task('Task 2', 'Description 2'));
        $completedTask = $this->repository->create(new Task('Task 3', 'Description 3'));

        // Mark one as completed
        $this->repository->markAsCompleted($completedTask->getId());

        $tasks = $this->repository->getRecentIncompleteTasks(10);

        $this->assertCount(2, $tasks);
        $this->assertContainsOnlyInstancesOf(Task::class, $tasks);
    }

    public function testGetRecentIncompleteTasksRespectsLimit(): void
    {
        // Create 10 tasks
        for ($i = 1; $i <= 10; $i++) {
            $this->repository->create(new Task("Task {$i}", "Description {$i}"));
        }

        $tasks = $this->repository->getRecentIncompleteTasks(5);

        $this->assertCount(5, $tasks);
    }

    public function testGetRecentIncompleteTasksOrdersByCreatedAtDesc(): void
    {
        $task1 = $this->repository->create(new Task('First Task', 'Description'));
        sleep(1);
        $task2 = $this->repository->create(new Task('Second Task', 'Description'));

        $tasks = $this->repository->getRecentIncompleteTasks(10);

        // Most recent should be first
        $this->assertEquals($task2->getId(), $tasks[0]->getId());
        $this->assertEquals($task1->getId(), $tasks[1]->getId());
    }

    public function testMarkAsCompletedUpdatesTaskStatus(): void
    {
        $task = $this->repository->create(new Task('Task', 'Description'));
        $result = $this->repository->markAsCompleted($task->getId());

        $this->assertTrue($result);

        // Verify it's not returned in incomplete tasks
        $incompleteTasks = $this->repository->getRecentIncompleteTasks(10);
        $this->assertCount(0, $incompleteTasks);
    }

    public function testMarkAsCompletedReturnsFalseForNonExistentTask(): void
    {
        $result = $this->repository->markAsCompleted(999);

        $this->assertFalse($result);
    }

    public function testFindByIdReturnsTaskWhenExists(): void
    {
        $createdTask = $this->repository->create(new Task('Test Task', 'Description'));
        $foundTask = $this->repository->findById($createdTask->getId());

        $this->assertNotNull($foundTask);
        $this->assertEquals($createdTask->getId(), $foundTask->getId());
        $this->assertEquals('Test Task', $foundTask->getTitle());
    }

    public function testFindByIdReturnsNullWhenTaskDoesNotExist(): void
    {
        $foundTask = $this->repository->findById(999);

        $this->assertNull($foundTask);
    }
}
