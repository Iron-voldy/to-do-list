<?php

namespace Tests\Unit\Models;

use App\Models\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCanBeCreated(): void
    {
        $task = new Task('Test Task', 'Test Description');

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('Test Description', $task->getDescription());
        $this->assertFalse($task->isCompleted());
        $this->assertNull($task->getId());
    }

    public function testTaskCanBeCreatedWithId(): void
    {
        $task = new Task('Test Task', 'Test Description', false, 1);

        $this->assertEquals(1, $task->getId());
    }

    public function testTaskCanBeMarkedAsCompleted(): void
    {
        $task = new Task('Test Task', 'Test Description');
        $task->setCompleted(true);

        $this->assertTrue($task->isCompleted());
    }

    public function testTaskCanBeConvertedToArray(): void
    {
        $task = new Task(
            'Test Task',
            'Test Description',
            false,
            1,
            '2024-01-01 00:00:00',
            '2024-01-01 00:00:00'
        );

        $array = $task->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test Task', $array['title']);
        $this->assertEquals('Test Description', $array['description']);
        $this->assertFalse($array['completed']);
        $this->assertEquals('2024-01-01 00:00:00', $array['created_at']);
    }

    public function testTaskCanBeCreatedFromArray(): void
    {
        $data = [
            'id' => 1,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'completed' => false,
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];

        $task = Task::fromArray($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(1, $task->getId());
        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('Test Description', $task->getDescription());
    }

    public function testTaskGettersAndSetters(): void
    {
        $task = new Task('Original Title', 'Original Description');

        $task->setId(5);
        $task->setTitle('Updated Title');
        $task->setDescription('Updated Description');
        $task->setCompleted(true);

        $this->assertEquals(5, $task->getId());
        $this->assertEquals('Updated Title', $task->getTitle());
        $this->assertEquals('Updated Description', $task->getDescription());
        $this->assertTrue($task->isCompleted());
    }
}
