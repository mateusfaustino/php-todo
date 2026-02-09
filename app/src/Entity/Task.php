<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TaskPriorityEnum;
use App\Enum\TaskStatusEnum;
use App\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
#[ORM\HasLifecycleCallbacks]
class Task
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: TodoList::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'list_id', referencedColumnName: 'id', nullable: false)]
    private TodoList $list;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 20, enumType: TaskPriorityEnum::class)]
    private TaskPriorityEnum $priority;

    #[ORM\Column(type: 'string', length: 20, enumType: TaskStatusEnum::class)]
    private TaskStatusEnum $status;

    #[ORM\Column(type: 'date', name: 'start_date', nullable: true)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: 'date', name: 'due_date', nullable: true)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column(type: 'datetime', name: 'completed_at', nullable: true)]
    private ?\DateTime $completedAt = null;

    #[ORM\Column(type: 'integer', name: 'sort_order')]
    private int $order;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $recurrence = null;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', name: 'updated_at')]
    private DateTime $updatedAt;

    #[ORM\OneToMany(targetEntity: Subtask::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    private Collection $subtasks;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_tags')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    private Collection $tags;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    private Collection $attachments;

    #[ORM\OneToMany(targetEntity: Reminder::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    private Collection $reminders;

    #[ORM\OneToMany(targetEntity: StatusHistory::class, mappedBy: 'task', cascade: ['persist', 'remove'])]
    private Collection $statusHistories;

    public function __construct(
        TodoList $list,
        string $title,
        TaskPriorityEnum $priority = TaskPriorityEnum::MEDIUM,
        int $order = 0
    ) {
        $this->id = $this->generateUuid();
        $this->list = $list;
        $this->title = $title;
        $this->priority = $priority;
        $this->status = TaskStatusEnum::TODO;
        $this->order = $order;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->subtasks = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->reminders = new ArrayCollection();
        $this->statusHistories = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getList(): TodoList
    {
        return $this->list;
    }

    public function setList(TodoList $list): void
    {
        $this->list = $list;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPriority(): TaskPriorityEnum
    {
        return $this->priority;
    }

    public function setPriority(TaskPriorityEnum $priority): void
    {
        $this->priority = $priority;
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TaskStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getRecurrence(): ?string
    {
        return $this->recurrence;
    }

    public function setRecurrence(?string $recurrence): void
    {
        $this->recurrence = $recurrence;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Subtask>
     */
    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function addSubtask(Subtask $subtask): void
    {
        if (!$this->subtasks->contains($subtask)) {
            $this->subtasks->add($subtask);
            $subtask->setTask($this);
        }
    }

    public function removeSubtask(Subtask $subtask): void
    {
        if ($this->subtasks->contains($subtask)) {
            $this->subtasks->removeElement($subtask);
        }
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTask($this);
        }
    }

    public function removeComment(Comment $comment): void
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): void
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setTask($this);
        }
    }

    public function removeAttachment(Attachment $attachment): void
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
        }
    }

    /**
     * @return Collection<int, Reminder>
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(Reminder $reminder): void
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders->add($reminder);
            $reminder->setTask($this);
        }
    }

    public function removeReminder(Reminder $reminder): void
    {
        if ($this->reminders->contains($reminder)) {
            $this->reminders->removeElement($reminder);
        }
    }

    /**
     * @return Collection<int, StatusHistory>
     */
    public function getStatusHistories(): Collection
    {
        return $this->statusHistories;
    }

    public function addStatusHistory(StatusHistory $statusHistory): void
    {
        if (!$this->statusHistories->contains($statusHistory)) {
            $this->statusHistories->add($statusHistory);
            $statusHistory->setTask($this);
        }
    }

    public function removeStatusHistory(StatusHistory $statusHistory): void
    {
        if ($this->statusHistories->contains($statusHistory)) {
            $this->statusHistories->removeElement($statusHistory);
        }
    }

    /**
     * Business method: Complete the task
     */
    public function completeTask(): void
    {
        if ($this->status === TaskStatusEnum::COMPLETED) {
            throw new \DomainException('Task is already completed');
        }

        $previousStatus = $this->status;
        $this->status = TaskStatusEnum::COMPLETED;
        $this->completedAt = new DateTime();
    }

    /**
     * Business method: Reschedule the task
     */
    public function rescheduleTask(\DateTime $newDueDate, ?\DateTime $newStartDate = null): void
    {
        if ($newDueDate < new \DateTime('today')) {
            throw new \InvalidArgumentException('Due date cannot be in the past');
        }

        if ($newStartDate && $newStartDate > $newDueDate) {
            throw new \InvalidArgumentException('Start date must be before due date');
        }

        $this->dueDate = $newDueDate;
        
        if ($newStartDate) {
            $this->startDate = $newStartDate;
        }
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->dueDate || $this->status === TaskStatusEnum::COMPLETED) {
            return false;
        }

        return $this->dueDate < new \DateTime('today');
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === TaskStatusEnum::COMPLETED;
    }

    /**
     * Get completion percentage based on subtasks
     */
    public function getCompletionPercentage(): int
    {
        if ($this->subtasks->isEmpty()) {
            return $this->isCompleted() ? 100 : 0;
        }

        $completedSubtasks = $this->subtasks->filter(
            fn(Subtask $subtask) => $subtask->isCompleted()
        )->count();

        return (int) round(($completedSubtasks / $this->subtasks->count()) * 100);
    }
}
