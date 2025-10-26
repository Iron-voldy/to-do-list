<?php

namespace Tests\Unit\Services;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use App\Services\TaskService;
use Mockery;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private TaskRepositoryInterface $mockRepository;
    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(TaskRepositoryInterface::class);
        $this->taskService = new TaskService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetRecentTasksReturnsArrayOfTasks(): void
    {
        $tasks = [
            new Task('Task 1', 'Description 1', false, 1),
            new Task('Task 2', 'Description 2', false, 2),
        ];

        $this->mockRepository
            ->shouldReceive('getRecentIncompleteTasks')
            ->once()
            ->with(5)
            ->andReturn($tasks);

        $result = $this->taskService->getRecentTasks();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Task 1', $result[0]['title']);
        $this->assertEquals('Task 2', $result[1]['title']);
    }

    public function testCreateTaskWithValidData(): void
    {
        $data = [
            'title' => 'New Task',
            'description' => 'New Description'
        ];

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($task) {
                $task->setId(1);
                return $task;
            });

        $result = $this->taskService->createTask($data);

        $this->assertIsArray($result);
        $this->assertEquals('New Task', $result['title']);
        $this->assertEquals('New Description', $result['description']);
        $this->assertEquals(1, $result['id']);
    }

    public function testCreateTaskThrowsExceptionWhenTitleIsMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title is required and must be a string');

        $this->taskService->createTask(['description' => 'Description']);
    }

    public function testCreateTaskThrowsExceptionWhenTitleIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title is required and must be a string');

        $this->taskService->createTask(['title' => '', 'description' => 'Description']);
    }

    public function testCreateTaskThrowsExceptionWhenTitleIsTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must not exceed 255 characters');

        $longTitle = str_repeat('a', 256);
        $this->taskService->createTask(['title' => $longTitle]);
    }

    public function testCreateTaskThrowsExceptionWhenDescriptionIsNotString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Description must be a string');

        $this->taskService->createTask(['title' => 'Valid Title', 'description' => 123]);
    }

    public function testCreateTaskWorksWithoutDescription(): void
    {
        $data = ['title' => 'New Task'];

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($task) {
                $task->setId(1);
                return $task;
            });

        $result = $this->taskService->createTask($data);

        $this->assertEquals('New Task', $result['title']);
        $this->assertEquals('', $result['description']);
    }

    public function testCompleteTaskCallsRepository(): void
    {
        $this->mockRepository
            ->shouldReceive('markAsCompleted')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->taskService->completeTask(1);

        $this->assertTrue($result);
    }

    public function testCompleteTaskReturnsFalseWhenTaskNotFound(): void
    {
        $this->mockRepository
            ->shouldReceive('markAsCompleted')
            ->once()
            ->with(999)
            ->andReturn(false);

        $result = $this->taskService->completeTask(999);

        $this->assertFalse($result);
    }
}
