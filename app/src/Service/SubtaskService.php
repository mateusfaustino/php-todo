<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Subtask;
use App\Entity\Task;

/**
 * Service class for managing Subtask entities
 * Handles CRUD operations and business logic for subtasks
 */
class SubtaskService extends AbstractService
{
    /**
     * Find all subtasks
     *
     * @return Subtask[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->findAll();
    }

    /**
     * Find subtasks by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Subtask[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a subtask by its ID
     */
    public function find(string $id): ?Subtask
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->find($id);
    }

    /**
     * Find subtasks by task
     *
     * @return Subtask[]
     */
    public function findByTask(Task $task): array
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->findBy(['task' => $task]);
    }

    /**
     * Find completed subtasks
     *
     * @return Subtask[]
     */
    public function findCompleted(): array
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->findBy(['completed' => true]);
    }

    /**
     * Find pending subtasks
     *
     * @return Subtask[]
     */
    public function findPending(): array
    {
        $repository = $this->entityManager->getRepository(Subtask::class);

        return $repository->findBy(['completed' => false]);
    }

    /**
     * Create a new subtask
     */
    public function create(Subtask $subtask): void
    {
        $this->entityManager->persist($subtask);
        $this->entityManager->flush();
    }

    /**
     * Update an existing subtask
     */
    public function update(Subtask $subtask): Subtask
    {
        $this->entityManager->persist($subtask);
        $this->entityManager->flush();

        return $subtask;
    }

    /**
     * Complete a subtask
     */
    public function complete(string $id): ?Subtask
    {
        $subtask = $this->find($id);

        if ($subtask) {
            $subtask->completeSubtask();
            $this->entityManager->flush();
        }

        return $subtask;
    }

    /**
     * Uncomplete a subtask
     */
    public function uncomplete(string $id): ?Subtask
    {
        $subtask = $this->find($id);

        if ($subtask) {
            $subtask->uncompleteSubtask();
            $this->entityManager->flush();
        }

        return $subtask;
    }

    /**
     * Reorder a subtask
     */
    public function reorder(string $id, int $newOrder): ?Subtask
    {
        $subtask = $this->find($id);

        if ($subtask) {
            $subtask->reorderSubtask($newOrder);
            $this->entityManager->flush();
        }

        return $subtask;
    }

    /**
     * Toggle subtask completion status
     */
    public function toggle(string $id): ?Subtask
    {
        $subtask = $this->find($id);

        if ($subtask) {
            if ($subtask->isCompleted()) {
                $subtask->uncompleteSubtask();
            } else {
                $subtask->completeSubtask();
            }
            $this->entityManager->flush();
        }

        return $subtask;
    }

    /**
     * Get completion percentage for a task's subtasks
     */
    public function getCompletionPercentage(Task $task): int
    {
        $subtasks = $this->findByTask($task);

        if (empty($subtasks)) {
            return 0;
        }

        $completed = count(array_filter($subtasks, fn(Subtask $s) => $s->isCompleted()));

        return (int) round(($completed / count($subtasks)) * 100);
    }

    /**
     * Remove a subtask by ID
     */
    public function remove(string $id): void
    {
        $subtask = $this->find($id);

        if ($subtask) {
            $this->entityManager->remove($subtask);
            $this->entityManager->flush();
        }
    }
}
