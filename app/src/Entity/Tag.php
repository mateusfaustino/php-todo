<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tags')]
class Tag
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tags')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 7)]
    private string $color;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'tags')]
    private Collection $tasks;

    public function __construct(
        User $user,
        string $name,
        string $color = '#95a5a6'
    ) {
        $this->id = $this->generateUuid();
        $this->user = $user;
        $this->name = $name;
        $this->color = $color;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
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
            $task->addTag($this);
        }
    }

    public function removeTask(Task $task): void
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            $task->removeTag($this);
        }
    }
}
