<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Reminder;
use App\Entity\Task;
use App\Enum\ReminderChannelEnum;
use DateTime;

/**
 * Service class for managing Reminder entities
 * Handles CRUD operations and business logic for reminders
 */
class ReminderService extends AbstractService
{
    /**
     * Find all reminders
     *
     * @return Reminder[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findAll();
    }

    /**
     * Find reminders by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Reminder[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a reminder by its ID
     */
    public function find(string $id): ?Reminder
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->find($id);
    }

    /**
     * Find reminders by task
     *
     * @return Reminder[]
     */
    public function findByTask(Task $task): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findBy(['task' => $task], ['reminderDateTime' => 'ASC']);
    }

    /**
     * Find reminders by channel
     *
     * @return Reminder[]
     */
    public function findByChannel(ReminderChannelEnum $channel): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findBy(['channel' => $channel]);
    }

    /**
     * Find pending reminders (not sent)
     *
     * @return Reminder[]
     */
    public function findPending(): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findBy(['sent' => false], ['reminderDateTime' => 'ASC']);
    }

    /**
     * Find sent reminders
     *
     * @return Reminder[]
     */
    public function findSent(): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->findBy(['sent' => true], ['sentAt' => 'DESC']);
    }

    /**
     * Find due reminders (should be sent now)
     *
     * @return Reminder[]
     */
    public function findDue(): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->createQueryBuilder('r')
            ->where('r.sent = :sent')
            ->andWhere('r.reminderDateTime <= :now')
            ->setParameter('sent', false)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming reminders
     *
     * @return Reminder[]
     */
    public function findUpcoming(int $hours = 24): array
    {
        $repository = $this->entityManager->getRepository(Reminder::class);
        $now = new DateTime();
        $future = new DateTime('+' . $hours . ' hours');

        return $repository->createQueryBuilder('r')
            ->where('r.sent = :sent')
            ->andWhere('r.reminderDateTime BETWEEN :now AND :future')
            ->setParameter('sent', false)
            ->setParameter('now', $now)
            ->setParameter('future', $future)
            ->getQuery()
            ->getResult();
    }

    /**
     * Create a new reminder
     */
    public function create(Reminder $reminder): void
    {
        $this->entityManager->persist($reminder);
        $this->entityManager->flush();
    }

    /**
     * Update an existing reminder
     */
    public function update(Reminder $reminder): Reminder
    {
        $this->entityManager->persist($reminder);
        $this->entityManager->flush();

        return $reminder;
    }

    /**
     * Schedule a reminder for a new date/time
     */
    public function schedule(string $id, DateTime $newDateTime): ?Reminder
    {
        $reminder = $this->find($id);

        if ($reminder) {
            $reminder->schedule($newDateTime);
            $this->entityManager->flush();
        }

        return $reminder;
    }

    /**
     * Mark a reminder as sent
     */
    public function markAsSent(string $id): ?Reminder
    {
        $reminder = $this->find($id);

        if ($reminder) {
            $reminder->markAsSent();
            $this->entityManager->flush();
        }

        return $reminder;
    }

    /**
     * Change reminder channel
     */
    public function changeChannel(string $id, ReminderChannelEnum $channel): ?Reminder
    {
        $reminder = $this->find($id);

        if ($reminder) {
            $reminder->setChannel($channel);
            $this->entityManager->flush();
        }

        return $reminder;
    }

    /**
     * Process due reminders (mark as sent)
     *
     * @return int Number of reminders processed
     */
    public function processDueReminders(): int
    {
        $dueReminders = $this->findDue();

        foreach ($dueReminders as $reminder) {
            $reminder->markAsSent();
        }

        $this->entityManager->flush();

        return count($dueReminders);
    }

    /**
     * Get reminder count for a task
     */
    public function getCountByTask(Task $task): int
    {
        $repository = $this->entityManager->getRepository(Reminder::class);

        return $repository->count(['task' => $task]);
    }

    /**
     * Remove a reminder by ID
     */
    public function remove(string $id): void
    {
        $reminder = $this->find($id);

        if ($reminder) {
            $this->entityManager->remove($reminder);
            $this->entityManager->flush();
        }
    }
}
