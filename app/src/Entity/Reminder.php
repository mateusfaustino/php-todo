<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ReminderChannelEnum;
use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reminders')]
class Reminder
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'reminders')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\Column(type: 'datetime', name: 'reminder_datetime')]
    private DateTime $reminderDateTime;

    #[ORM\Column(type: 'string', length: 20, enumType: ReminderChannelEnum::class)]
    private ReminderChannelEnum $channel;

    #[ORM\Column(type: 'boolean')]
    private bool $sent = false;

    #[ORM\Column(type: 'datetime', name: 'sent_at', nullable: true)]
    private ?\DateTime $sentAt = null;

    public function __construct(
        Task $task,
        DateTime $reminderDateTime,
        ReminderChannelEnum $channel = ReminderChannelEnum::EMAIL
    ) {
        $this->id = $this->generateUuid();
        $this->task = $task;
        $this->reminderDateTime = $reminderDateTime;
        $this->channel = $channel;
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

    public function getReminderDateTime(): DateTime
    {
        return $this->reminderDateTime;
    }

    public function setReminderDateTime(DateTime $reminderDateTime): void
    {
        $this->reminderDateTime = $reminderDateTime;
    }

    public function getChannel(): ReminderChannelEnum
    {
        return $this->channel;
    }

    public function setChannel(ReminderChannelEnum $channel): void
    {
        $this->channel = $channel;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    /**
     * Business method: Schedule or reschedule the reminder
     */
    public function schedule(DateTime $newDateTime): void
    {
        if ($newDateTime < new DateTime()) {
            throw new \InvalidArgumentException('Reminder date/time cannot be in the past');
        }

        if ($this->sent) {
            throw new \DomainException('Cannot reschedule a reminder that has already been sent');
        }

        $this->reminderDateTime = $newDateTime;
    }

    /**
     * Business method: Cancel the reminder
     */
    public function cancel(): void
    {
        if ($this->sent) {
            throw new \DomainException('Cannot cancel a reminder that has already been sent');
        }

        // In a real application, this might set a "cancelled" flag
        // For now, we'll throw an exception to prevent cancellation
        // The actual cancellation would be handled by removing the reminder from the task
    }

    /**
     * Business method: Mark reminder as sent
     */
    public function markAsSent(): void
    {
        if ($this->sent) {
            throw new \DomainException('Reminder has already been marked as sent');
        }

        $this->sent = true;
        $this->sentAt = new DateTime();
    }

    /**
     * Check if reminder is due
     */
    public function isDue(): bool
    {
        return !$this->sent && $this->reminderDateTime <= new DateTime();
    }

    /**
     * Check if reminder is pending
     */
    public function isPending(): bool
    {
        return !$this->sent && $this->reminderDateTime > new DateTime();
    }
}
