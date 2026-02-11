<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\StatusHistory;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatusEnum;

/**
 * Service class for managing StatusHistory entities
 * Handles CRUD operations and business logic for status history tracking
 */
class StatusHistoryService extends AbstractService
{
    /**
     * Find all status history entries
     *
     * @return StatusHistory[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findAll();
    }

    /**
     * Find status history by criteria
     *
     * @param array<string, mixed> $criteria
     * @return StatusHistory[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a status history entry by its ID
     */
    public function find(string $id): ?StatusHistory
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->find($id);
    }

    /**
     * Find status history by task
     *
     * @return StatusHistory[]
     */
    public function findByTask(Task $task): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy(['task' => $task], ['createdAt' => 'DESC']);
    }

    /**
     * Find status history by user who made the change
     *
     * @return StatusHistory[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy(['changedByUser' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * Find status history by previous status
     *
     * @return StatusHistory[]
     */
    public function findByPreviousStatus(TaskStatusEnum $status): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy(['previousStatus' => $status], ['createdAt' => 'DESC']);
    }

    /**
     * Find status history by new status
     *
     * @return StatusHistory[]
     */
    public function findByNewStatus(TaskStatusEnum $status): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy(['newStatus' => $status], ['createdAt' => 'DESC']);
    }

    /**
     * Find recent status changes
     *
     * @return StatusHistory[]
     */
    public function findRecent(int $limit = 10): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * Create a new status history entry
     */
    public function create(StatusHistory $statusHistory): void
    {
        $this->entityManager->persist($statusHistory);
        $this->entityManager->flush();
    }

    /**
     * Log a status change for a task
     */
    public function logStatusChange(
        Task $task,
        TaskStatusEnum $previousStatus,
        TaskStatusEnum $newStatus,
        User $changedByUser
    ): StatusHistory {
        $statusHistory = new StatusHistory(
            $task,
            $previousStatus,
            $newStatus,
            $changedByUser
        );

        $this->entityManager->persist($statusHistory);
        $this->entityManager->flush();

        return $statusHistory;
    }

    /**
     * Get the last status change for a task
     */
    public function getLastChangeForTask(Task $task): ?StatusHistory
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        $results = $repository->findBy(
            ['task' => $task],
            ['createdAt' => 'DESC'],
            1
        );

        return $results[0] ?? null;
    }

    /**
     * Get status change count for a task
     */
    public function getChangeCountForTask(Task $task): int
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->count(['task' => $task]);
    }

    /**
     * Check if a task was ever completed
     */
    public function wasTaskEverCompleted(Task $task): bool
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        $count = $repository->count([
            'task' => $task,
            'newStatus' => TaskStatusEnum::COMPLETED,
        ]);

        return $count > 0;
    }

    /**
     * Get completion count for a task (how many times it was completed)
     */
    public function getCompletionCount(Task $task): int
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);

        return $repository->count([
            'task' => $task,
            'newStatus' => TaskStatusEnum::COMPLETED,
        ]);
    }

    /**
     * Get status statistics for a task
     *
     * @return array<string, int>
     */
    public function getStatusStatistics(Task $task): array
    {
        $repository = $this->entityManager->getRepository(StatusHistory::class);
        $history = $this->findByTask($task);

        $statistics = [
            'total_changes' => count($history),
            'completions' => 0,
            'cancellations' => 0,
            'reopenings' => 0,
        ];

        foreach ($history as $entry) {
            if ($entry->isCompletion()) {
                $statistics['completions']++;
            }
            if ($entry->isCancellation()) {
                $statistics['cancellations']++;
            }
            if ($entry->isReopening()) {
                $statistics['reopenings']++;
            }
        }

        return $statistics;
    }

    /**
     * Remove a status history entry by ID
     */
    public function remove(string $id): void
    {
        $statusHistory = $this->find($id);

        if ($statusHistory) {
            $this->entityManager->remove($statusHistory);
            $this->entityManager->flush();
        }
    }

    /**
     * Clear all status history for a task
     *
     * @return int Number of entries removed
     */
    public function clearHistoryForTask(Task $task): int
    {
        $history = $this->findByTask($task);

        foreach ($history as $entry) {
            $this->entityManager->remove($entry);
        }

        $this->entityManager->flush();

        return count($history);
    }
}
