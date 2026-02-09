<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subtasks')]
class Subtask
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'subtasks')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'boolean')]
    private bool $completed = false;

    #[ORM\Column(type: 'integer', name: 'sort_order')]
    private int $order;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    public function __construct(
        Task $task,
        string $title,
        int $order = 0
    ) {
        $this->id = $this->generateUuid();
        $this->task = $task;
        $this->title = $title;
        $this->order = $order;
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Business method: Complete the subtask
     */
    public function completeSubtask(): void
    {
        if ($this->completed) {
            throw new \DomainException('Subtask is already completed');
        }
        
        $this->completed = true;
    }

    /**
     * Business method: Uncomplete the subtask
     */
    public function uncompleteSubtask(): void
    {
        if (!$this->completed) {
            throw new \DomainException('Subtask is not completed');
        }
        
        $this->completed = false;
    }

    /**
     * Business method: Reorder the subtask
     */
    public function reorderSubtask(int $newOrder): void
    {
        if ($newOrder < 0) {
            throw new \InvalidArgumentException('Order must be a positive integer');
        }
        
        $this->order = $newOrder;
    }
}
