<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TaskStatusEnum;
use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'status_histories')]
class StatusHistory
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'statusHistories')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\Column(type: 'string', length: 20, name: 'previous_status', enumType: TaskStatusEnum::class)]
    private TaskStatusEnum $previousStatus;

    #[ORM\Column(type: 'string', length: 20, name: 'new_status', enumType: TaskStatusEnum::class)]
    private TaskStatusEnum $newStatus;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'changed_by_user_id', referencedColumnName: 'id', nullable: false)]
    private User $changedByUser;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    public function __construct(
        Task $task,
        TaskStatusEnum $previousStatus,
        TaskStatusEnum $newStatus,
        User $changedByUser
    ) {
        $this->id = $this->generateUuid();
        $this->task = $task;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->changedByUser = $changedByUser;
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

    public function getPreviousStatus(): TaskStatusEnum
    {
        return $this->previousStatus;
    }

    public function setPreviousStatus(TaskStatusEnum $previousStatus): void
    {
        $this->previousStatus = $previousStatus;
    }

    public function getNewStatus(): TaskStatusEnum
    {
        return $this->newStatus;
    }

    public function setNewStatus(TaskStatusEnum $newStatus): void
    {
        $this->newStatus = $newStatus;
    }

    public function getChangedByUser(): User
    {
        return $this->changedByUser;
    }

    public function setChangedByUser(User $changedByUser): void
    {
        $this->changedByUser = $changedByUser;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get formatted status change description
     */
    public function getStatusChangeDescription(): string
    {
        return sprintf(
            'Status changed from "%s" to "%s" by %s',
            $this->previousStatus->value,
            $this->newStatus->value,
            $this->changedByUser->getName()
        );
    }

    /**
     * Check if this represents a completion event
     */
    public function isCompletion(): bool
    {
        return $this->newStatus === TaskStatusEnum::COMPLETED;
    }

    /**
     * Check if this represents a cancellation event
     */
    public function isCancellation(): bool
    {
        return $this->newStatus === TaskStatusEnum::CANCELLED;
    }

    /**
     * Check if this represents a reopening event
     */
    public function isReopening(): bool
    {
        return $this->previousStatus === TaskStatusEnum::COMPLETED 
            && $this->newStatus !== TaskStatusEnum::COMPLETED;
    }
}
