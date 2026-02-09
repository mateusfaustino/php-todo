<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
class Project
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 7)]
    private string $color;

    #[ORM\Column(type: 'boolean')]
    private bool $archived = false;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    #[ORM\OneToMany(targetEntity: TodoList::class, mappedBy: 'project', cascade: ['persist', 'remove'])]
    private Collection $lists;

    public function __construct(
        User $user,
        string $name,
        string $color = '#3498db'
    ) {
        $this->id = $this->generateUuid();
        $this->user = $user;
        $this->name = $name;
        $this->color = $color;
        $this->createdAt = new DateTime();
        $this->lists = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
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
            $list->setProject($this);
        }
    }

    public function removeList(TodoList $list): void
    {
        if ($this->lists->contains($list)) {
            $this->lists->removeElement($list);
        }
    }

    /**
     * Business method: Archive the project
     */
    public function archive(): void
    {
        $this->archived = true;
    }

    /**
     * Business method: Unarchive the project
     */
    public function unarchive(): void
    {
        $this->archived = false;
    }

    /**
     * Business method: Rename the project
     */
    public function rename(string $newName): void
    {
        if (empty(trim($newName))) {
            throw new \InvalidArgumentException('Project name cannot be empty');
        }
        
        $this->name = $newName;
    }
}
