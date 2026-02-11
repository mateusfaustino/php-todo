<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TodoList;
use App\Entity\Project;
use App\Entity\User;

/**
 * Service class for managing TodoList entities
 * Handles CRUD operations and business logic for lists
 */
class TodoListService extends AbstractService
{
    /**
     * Find all lists
     *
     * @return TodoList[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findAll();
    }

    /**
     * Find lists by criteria
     *
     * @param array<string, mixed> $criteria
     * @return TodoList[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a list by its ID
     */
    public function find(string $id): ?TodoList
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->find($id);
    }

    /**
     * Find lists by user
     *
     * @return TodoList[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findBy(['user' => $user]);
    }

    /**
     * Find lists by project
     *
     * @return TodoList[]
     */
    public function findByProject(Project $project): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findBy(['project' => $project]);
    }

    /**
     * Find archived lists
     *
     * @return TodoList[]
     */
    public function findArchived(): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findBy(['archived' => true]);
    }

    /**
     * Find active (non-archived) lists
     *
     * @return TodoList[]
     */
    public function findActive(): array
    {
        $repository = $this->entityManager->getRepository(TodoList::class);

        return $repository->findBy(['archived' => false]);
    }

    /**
     * Create a new list
     */
    public function create(TodoList $list): void
    {
        $this->entityManager->persist($list);
        $this->entityManager->flush();
    }

    /**
     * Update an existing list
     */
    public function update(TodoList $list): TodoList
    {
        $this->entityManager->persist($list);
        $this->entityManager->flush();

        return $list;
    }

    /**
     * Archive a list
     */
    public function archive(string $id): ?TodoList
    {
        $list = $this->find($id);

        if ($list) {
            $list->archive();
            $this->entityManager->flush();
        }

        return $list;
    }

    /**
     * Unarchive a list
     */
    public function unarchive(string $id): ?TodoList
    {
        $list = $this->find($id);

        if ($list) {
            $list->unarchive();
            $this->entityManager->flush();
        }

        return $list;
    }

    /**
     * Reorder a list
     */
    public function reorder(string $id, int $newOrder): ?TodoList
    {
        $list = $this->find($id);

        if ($list) {
            $list->reorder($newOrder);
            $this->entityManager->flush();
        }

        return $list;
    }

    /**
     * Assign a list to a project
     */
    public function assignToProject(string $listId, ?Project $project): ?TodoList
    {
        $list = $this->find($listId);

        if ($list) {
            $list->setProject($project);
            $this->entityManager->flush();
        }

        return $list;
    }

    /**
     * Remove a list by ID
     */
    public function remove(string $id): void
    {
        $list = $this->find($id);

        if ($list) {
            $this->entityManager->remove($list);
            $this->entityManager->flush();
        }
    }
}
