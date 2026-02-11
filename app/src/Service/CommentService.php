<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;

/**
 * Service class for managing Comment entities
 * Handles CRUD operations and business logic for comments
 */
class CommentService extends AbstractService
{
    /**
     * Find all comments
     *
     * @return Comment[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->findAll();
    }

    /**
     * Find comments by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Comment[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a comment by its ID
     */
    public function find(string $id): ?Comment
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->find($id);
    }

    /**
     * Find comments by task
     *
     * @return Comment[]
     */
    public function findByTask(Task $task): array
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->findBy(['task' => $task], ['createdAt' => 'DESC']);
    }

    /**
     * Find comments by user
     *
     * @return Comment[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * Find recent comments
     *
     * @return Comment[]
     */
    public function findRecent(int $limit = 10): array
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * Create a new comment
     */
    public function create(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    /**
     * Update an existing comment
     */
    public function update(Comment $comment): Comment
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    /**
     * Edit a comment's content
     */
    public function edit(string $id, string $newContent): ?Comment
    {
        $comment = $this->find($id);

        if ($comment) {
            $comment->editComment($newContent);
            $this->entityManager->flush();
        }

        return $comment;
    }

    /**
     * Get comment count for a task
     */
    public function getCountByTask(Task $task): int
    {
        $repository = $this->entityManager->getRepository(Comment::class);

        return $repository->count(['task' => $task]);
    }

    /**
     * Check if a comment has been edited
     */
    public function isEdited(string $id): bool
    {
        $comment = $this->find($id);

        return $comment ? $comment->getEditedAt() !== null : false;
    }

    /**
     * Remove a comment by ID
     */
    public function remove(string $id): void
    {
        $comment = $this->find($id);

        if ($comment) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }
    }
}
