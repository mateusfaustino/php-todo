<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, name: 'password_hash')]
    private string $passwordHash;

    #[ORM\Column(type: 'string', length: 50, name: 'timezone')]
    private string $timezone;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $projects;

    #[ORM\OneToMany(targetEntity: TodoList::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $lists;

    #[ORM\OneToMany(targetEntity: Tag::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $tags;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $attachments;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $notifications;

    public function __construct(
        string $name,
        string $email,
        string $passwordHash,
        string $timezone = 'UTC'
    ) {
        $this->id = $this->generateUuid();
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->timezone = $timezone;
        $this->createdAt = new DateTime();
        $this->projects = new ArrayCollection();
        $this->lists = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): void
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setUser($this);
        }
    }

    public function removeProject(Project $project): void
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
        }
    }

    /**
     * @return Collection<int, TodoList>
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function addList(TodoList $list): void
    {
        if (!$this->lists->contains($list)) {
            $this->lists->add($list);
            $list->setUser($this);
        }
    }

    public function removeList(TodoList $list): void
    {
        if ($this->lists->contains($list)) {
            $this->lists->removeElement($list);
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
            $tag->setUser($this);
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
            $comment->setUser($this);
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
            $attachment->setUser($this);
        }
    }

    public function removeAttachment(Attachment $attachment): void
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
        }
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): void
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }
    }

    public function removeNotification(Notification $notification): void
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
        }
    }

    public function setCreatedAt(DateTime $datetime): void
    {
        $this->createdAt = $datetime;
    }

    public function getShortName(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? $parts[count($parts) - 1] : '';

        return trim($first.' '.$last);
    }
}
