<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', name: 'edited_at', nullable: true)]
    private ?\DateTime $editedAt = null;

    public function __construct(
        Task $task,
        User $user,
        string $content
    ) {
        $this->id = $this->generateUuid();
        $this->task = $task;
        $this->user = $user;
        $this->content = $content;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getEditedAt(): ?\DateTime
    {
        return $this->editedAt;
    }

    /**
     * Business method: Edit the comment
     */
    public function editComment(string $newContent): void
    {
        if (empty(trim($newContent))) {
            throw new \InvalidArgumentException('Comment content cannot be empty');
        }

        if ($this->content === $newContent) {
            throw new \DomainException('New content is the same as current content');
        }

        $this->content = $newContent;
        $this->editedAt = new DateTime();
    }

    /**
     * Business method: Check if comment was edited
     */
    public function isEdited(): bool
    {
        return $this->editedAt !== null;
    }
}
