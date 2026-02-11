<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;

/**
 * Service class for managing Project entities
 * Handles CRUD operations and business logic for projects
 */
class ProjectService extends AbstractService
{
    /**
     * Find all projects
     *
     * @return Project[]
     */
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->findAll();
    }

    /**
     * Find projects by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Project[]
     */
    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->findBy($criteria);
    }

    /**
     * Find a project by its ID
     */
    public function find(string $id): ?Project
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->find($id);
    }

    /**
     * Find projects by user
     *
     * @return Project[]
     */
    public function findByUser(User $user): array
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->findBy(['user' => $user]);
    }

    /**
     * Find archived projects
     *
     * @return Project[]
     */
    public function findArchived(): array
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->findBy(['archived' => true]);
    }

    /**
     * Find active (non-archived) projects
     *
     * @return Project[]
     */
    public function findActive(): array
    {
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->findBy(['archived' => false]);
    }

    /**
     * Create a new project
     */
    public function create(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    /**
     * Update an existing project
     */
    public function update(Project $project): Project
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    /**
     * Archive a project
     */
    public function archive(string $id): ?Project
    {
        $project = $this->find($id);

        if ($project) {
            $project->archive();
            $this->entityManager->flush();
        }

        return $project;
    }

    /**
     * Unarchive a project
     */
    public function unarchive(string $id): ?Project
    {
        $project = $this->find($id);

        if ($project) {
            $project->unarchive();
            $this->entityManager->flush();
        }

        return $project;
    }

    /**
     * Rename a project
     */
    public function rename(string $id, string $newName): ?Project
    {
        $project = $this->find($id);

        if ($project) {
            $project->rename($newName);
            $this->entityManager->flush();
        }

        return $project;
    }

    /**
     * Remove a project by ID
     */
    public function remove(string $id): void
    {
        $project = $this->find($id);

        if ($project) {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
        }
    }
}
