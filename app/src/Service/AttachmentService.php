<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Task;
use App\Entity\User;

/**
 * Service class for managing Attachment entities
 * Handles CRUD operations and business logic for attachments
 */
class AttachmentService extends AbstractService
{
    /**
     * Find all attachments
     *
     * @return Attachment[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->findAll();
    }

    /**
     * Find attachments by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Attachment[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find an attachment by its ID
     */
    public function find(string $id): ?Attachment
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->find($id);
    }

    /**
     * Find attachments by task
     *
     * @return Attachment[]
     */
    public function findByTask(Task $task): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->findBy(['task' => $task], ['createdAt' => 'DESC']);
    }

    /**
     * Find attachments by user
     *
     * @return Attachment[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * Find image attachments
     *
     * @return Attachment[]
     */
    public function findImages(): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->createQueryBuilder('a')
            ->where('a.mimeType LIKE :image')
            ->setParameter('image', 'image/%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find attachments by MIME type
     *
     * @return Attachment[]
     */
    public function findByMimeType(string $mimeType): array
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->findBy(['mimeType' => $mimeType]);
    }

    /**
     * Create a new attachment
     */
    public function create(Attachment $attachment): void
    {
        $this->entityManager->persist($attachment);
        $this->entityManager->flush();
    }

    /**
     * Update an existing attachment
     */
    public function update(Attachment $attachment): Attachment
    {
        $this->entityManager->persist($attachment);
        $this->entityManager->flush();

        return $attachment;
    }

    /**
     * Get total size of attachments for a task
     */
    public function getTotalSizeByTask(Task $task): int
    {
        $attachments = $this->findByTask($task);

        return array_sum(array_map(fn(Attachment $a) => $a->getSizeBytes(), $attachments));
    }

    /**
     * Get formatted total size for a task
     */
    public function getFormattedTotalSizeByTask(Task $task): string
    {
        $totalBytes = $this->getTotalSizeByTask($task);

        if ($totalBytes < 1024) {
            return $totalBytes . ' B';
        }

        if ($totalBytes < 1024 * 1024) {
            return round($totalBytes / 1024, 2) . ' KB';
        }

        return round($totalBytes / (1024 * 1024), 2) . ' MB';
    }

    /**
     * Get attachment count for a task
     */
    public function getCountByTask(Task $task): int
    {
        $repository = $this->entityManager->getRepository(Attachment::class);

        return $repository->count(['task' => $task]);
    }

    /**
     * Check if attachment is an image
     */
    public function isImage(string $id): bool
    {
        $attachment = $this->find($id);

        return $attachment ? $attachment->isImage() : false;
    }

    /**
     * Remove an attachment by ID
     */
    public function remove(string $id): void
    {
        $attachment = $this->find($id);

        if ($attachment) {
            $this->entityManager->remove($attachment);
            $this->entityManager->flush();
        }
    }
}
