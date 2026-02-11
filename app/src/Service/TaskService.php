<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Entity\User;
use App\Enum\TaskPriorityEnum;
use App\Enum\TaskStatusEnum;
use DateTime;

/**
 * Service class for managing Task entities
 * Handles CRUD operations and business logic for tasks
 */
class TaskService extends AbstractService
{
    /**
     * Find all tasks
     *
     * @return Task[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->findAll();
    }

    /**
     * Find tasks by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Task[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a task by its ID
     */
    public function find(string $id): ?Task
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->find($id);
    }

    /**
     * Find tasks by list
     *
     * @return Task[]
     */
    public function findByList(TodoList $list): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->findBy(['list' => $list]);
    }

    /**
     * Find tasks by status
     *
     * @return Task[]
     */
    public function findByStatus(TaskStatusEnum $status): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->findBy(['status' => $status]);
    }

    /**
     * Find tasks by priority
     *
     * @return Task[]
     */
    public function findByPriority(TaskPriorityEnum $priority): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->findBy(['priority' => $priority]);
    }

    /**
     * Find overdue tasks
     *
     * @return Task[]
     */
    public function findOverdue(): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->createQueryBuilder('t')
            ->where('t.dueDate < :today')
            ->andWhere('t.status != :completed')
            ->setParameter('today', new DateTime('today'))
            ->setParameter('completed', TaskStatusEnum::COMPLETED)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tasks due today
     *
     * @return Task[]
     */
    public function findDueToday(): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        return $repository->createQueryBuilder('t')
            ->where('t.dueDate = :today')
            ->andWhere('t.status != :completed')
            ->setParameter('today', new DateTime('today'))
            ->setParameter('completed', TaskStatusEnum::COMPLETED)
            ->getQuery()
            ->getResult();
    }

    /**
     * Create a new task
     */
    public function create(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * Update an existing task
     */
    public function update(Task $task): Task
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * Complete a task
     */
    public function complete(string $id): ?Task
    {
        $task = $this->find($id);

        if ($task) {
            $task->completeTask();
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Reschedule a task
     */
    public function reschedule(string $id, DateTime $newDueDate, ?DateTime $newStartDate = null): ?Task
    {
        $task = $this->find($id);

        if ($task) {
            $task->rescheduleTask($newDueDate, $newStartDate);
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Change task priority
     */
    public function changePriority(string $id, TaskPriorityEnum $priority): ?Task
    {
        $task = $this->find($id);

        if ($task) {
            $task->setPriority($priority);
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Move task to another list
     */
    public function moveToList(string $taskId, TodoList $newList): ?Task
    {
        $task = $this->find($taskId);

        if ($task) {
            $task->setList($newList);
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Add a tag to a task
     */
    public function addTag(string $taskId, $tag): ?Task
    {
        $task = $this->find($taskId);

        if ($task) {
            $task->addTag($tag);
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Remove a tag from a task
     */
    public function removeTag(string $taskId, $tag): ?Task
    {
        $task = $this->find($taskId);

        if ($task) {
            $task->removeTag($tag);
            $this->entityManager->flush();
        }

        return $task;
    }

    /**
     * Remove a task by ID
     */
    public function remove(string $id): void
    {
        $task = $this->find($id);

        if ($task) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }
    }

    /**
     * Get task completion statistics
     *
     * @return array<string, int>
     */
    public function getStatistics(): array
    {
        $repository = $this->entityManager->getRepository(Task::class);

        $total = $repository->count([]);
        $completed = $repository->count(['status' => TaskStatusEnum::COMPLETED]);
        $todo = $repository->count(['status' => TaskStatusEnum::TODO]);
        $inProgress = $repository->count(['status' => TaskStatusEnum::IN_PROGRESS]);

        return [
            'total' => $total,
            'completed' => $completed,
            'todo' => $todo,
            'in_progress' => $inProgress,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}
