<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationTypeEnum;

/**
 * Service class for managing Notification entities
 * Handles CRUD operations and business logic for notifications
 */
class NotificationService extends AbstractService
{
    /**
     * Find all notifications
     *
     * @return Notification[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findAll();
    }

    /**
     * Find notifications by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Notification[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a notification by its ID
     */
    public function find(string $id): ?Notification
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->find($id);
    }

    /**
     * Find notifications by user
     *
     * @return Notification[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * Find unread notifications by user
     *
     * @return Notification[]
     */
    public function findUnreadByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy(['user' => $user, 'read' => false], ['createdAt' => 'DESC']);
    }

    /**
     * Find read notifications by user
     *
     * @return Notification[]
     */
    public function findReadByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy(['user' => $user, 'read' => true], ['createdAt' => 'DESC']);
    }

    /**
     * Find notifications by type
     *
     * @return Notification[]
     */
    public function findByType(NotificationTypeEnum $type): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy(['type' => $type], ['createdAt' => 'DESC']);
    }

    /**
     * Find recent notifications
     *
     * @return Notification[]
     */
    public function findRecent(int $limit = 10): array
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * Create a new notification
     */
    public function create(Notification $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * Update an existing notification
     */
    public function update(Notification $notification): Notification
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $id): ?Notification
    {
        $notification = $this->find($id);

        if ($notification) {
            $notification->markAsRead();
            $this->entityManager->flush();
        }

        return $notification;
    }

    /**
     * Mark a notification as unread
     */
    public function markAsUnread(string $id): ?Notification
    {
        $notification = $this->find($id);

        if ($notification) {
            $notification->markAsUnread();
            $this->entityManager->flush();
        }

        return $notification;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @return int Number of notifications marked as read
     */
    public function markAllAsReadForUser(User $user): int
    {
        $unreadNotifications = $this->findUnreadByUser($user);

        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }

        $this->entityManager->flush();

        return count($unreadNotifications);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCountForUser(User $user): int
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->count(['user' => $user, 'read' => false]);
    }

    /**
     * Get notification count for a user
     */
    public function getCountByUser(User $user): int
    {
        $repository = $this->entityManager->getRepository(Notification::class);

        return $repository->count(['user' => $user]);
    }

    /**
     * Remove a notification by ID
     */
    public function remove(string $id): void
    {
        $notification = $this->find($id);

        if ($notification) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        }
    }

    /**
     * Remove all read notifications for a user
     *
     * @return int Number of notifications removed
     */
    public function removeAllReadForUser(User $user): int
    {
        $readNotifications = $this->findReadByUser($user);

        foreach ($readNotifications as $notification) {
            $this->entityManager->remove($notification);
        }

        $this->entityManager->flush();

        return count($readNotifications);
    }
}
