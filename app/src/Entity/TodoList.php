<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lists')]
class TodoList
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lists')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'lists')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: true)]
    private ?Project $project = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer', name: 'sort_order')]
    private int $order;

    #[ORM\Column(type: 'boolean')]
    private bool $archived = false;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'list', cascade: ['persist', 'remove'])]
    private Collection $tasks;

    public function __construct(
        User $user,
        string $name,
        int $order = 0
    ) {
        $this->id = $this->generateUuid();
        $this->user = $user;
        $this->name = $name;
        $this->order = $order;
        $this->createdAt = new DateTime();
        $this->tasks = new ArrayCollection();
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setList($this);
        }
    }

    public function removeTask(Task $task): void
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
        }
    }

    /**
     * Business method: Reorder the list
     */
    public function reorder(int $newOrder): void
    {
        if ($newOrder < 0) {
            throw new \InvalidArgumentException('Order must be a positive integer');
        }
        
        $this->order = $newOrder;
    }

    /**
     * Business method: Archive the list
     */
    public function archive(): void
    {
        $this->archived = true;
    }

    /**
     * Business method: Unarchive the list
     */
    public function unarchive(): void
    {
        $this->archived = false;
    }
}
