<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Task;

/**
 * Service class for managing Tag entities
 * Handles CRUD operations and business logic for tags
 */
class TagService extends AbstractService
{
    /**
     * Find all tags
     *
     * @return Tag[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Tag::class);

        return $repository->findAll();
    }

    /**
     * Find tags by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Tag[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Tag::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a tag by its ID
     */
    public function find(string $id): ?Tag
    {
        $repository = $this->entityManager->getRepository(Tag::class);

        return $repository->find($id);
    }

    /**
     * Find tags by user
     *
     * @return Tag[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Tag::class);

        return $repository->findBy(['user' => $user]);
    }

    /**
     * Find tags by name (partial match)
     *
     * @return Tag[]
     */
    public function findByName(string $name): array
    {
        $repository = $this->entityManager->getRepository(Tag::class);

        return $repository->createQueryBuilder('t')
            ->where('t.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tags associated with a task
     *
     * @return Tag[]
     */
    public function findByTask(Task $task): array
    {
        return $task->getTags()->toArray();
    }

    /**
     * Create a new tag
     */
    public function create(Tag $tag): void
    {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
    }

    /**
     * Update an existing tag
     */
    public function update(Tag $tag): Tag
    {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $tag;
    }

    /**
     * Change tag color
     */
    public function changeColor(string $id, string $newColor): ?Tag
    {
        $tag = $this->find($id);

        if ($tag) {
            $tag->setColor($newColor);
            $this->entityManager->flush();
        }

        return $tag;
    }

    /**
     * Rename a tag
     */
    public function rename(string $id, string $newName): ?Tag
    {
        $tag = $this->find($id);

        if ($tag) {
            $tag->setName($newName);
            $this->entityManager->flush();
        }

        return $tag;
    }

    /**
     * Assign a tag to a task
     */
    public function assignToTask(string $tagId, Task $task): ?Tag
    {
        $tag = $this->find($tagId);

        if ($tag) {
            $tag->addTask($task);
            $this->entityManager->flush();
        }

        return $tag;
    }

    /**
     * Remove a tag from a task
     */
    public function removeFromTask(string $tagId, Task $task): ?Tag
    {
        $tag = $this->find($tagId);

        if ($tag) {
            $tag->removeTask($task);
            $this->entityManager->flush();
        }

        return $tag;
    }

    /**
     * Get tag usage count
     */
    public function getUsageCount(Tag $tag): int
    {
        return $tag->getTasks()->count();
    }

    /**
     * Remove a tag by ID
     */
    public function remove(string $id): void
    {
        $tag = $this->find($id);

        if ($tag) {
            $this->entityManager->remove($tag);
            $this->entityManager->flush();
        }
    }
}
