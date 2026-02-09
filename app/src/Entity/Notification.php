<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\NotificationTypeEnum;
use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 50, enumType: NotificationTypeEnum::class)]
    private NotificationTypeEnum $type;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\Column(type: 'boolean', name: 'is_read')]
    private bool $read = false;

    #[ORM\Column(type: 'datetime', name: 'read_at', nullable: true)]
    private ?\DateTime $readAt = null;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    public function __construct(
        User $user,
        NotificationTypeEnum $type,
        string $title,
        string $message
    ) {
        $this->id = $this->generateUuid();
        $this->user = $user;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getType(): NotificationTypeEnum
    {
        return $this->type;
    }

    public function setType(NotificationTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Business method: Mark notification as read
     */
    public function markAsRead(): void
    {
        if ($this->read) {
            throw new \DomainException('Notification is already marked as read');
        }

        $this->read = true;
        $this->readAt = new DateTime();
    }

    /**
     * Business method: Mark notification as unread
     */
    public function markAsUnread(): void
    {
        if (!$this->read) {
            throw new \DomainException('Notification is already unread');
        }

        $this->read = false;
        $this->readAt = null;
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return !$this->read;
    }

    /**
     * Get time elapsed since creation
     */
    public function getTimeElapsed(): string
    {
        $now = new DateTime();
        $interval = $this->createdAt->diff($now);

        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        }

        if ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        }

        if ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        }

        if ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        }

        if ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        }

        return 'Just now';
    }
}
