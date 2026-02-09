<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class UserService extends AbstractService
{
    public function findAll(): array
    {
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->findAll();
    }

    public function findBy(array $criteria): array
    {
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->findBy($criteria);
    }

    public function find(int $id): ?User
    {
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->find($id);
    }

    public function update(User $advertiser): User
    {
        $this->entityManager->persist($advertiser);
        $this->entityManager->flush();

        return $advertiser;
    }

    public function remove(int $id): void
    {
        $advertiser = $this->find($id);
        if ($advertiser) {
            $this->entityManager->remove($advertiser);
            $this->entityManager->flush();
        }
    }

    public function create(User $advertiser): void
    {
        $this->entityManager->persist($advertiser);
        $this->entityManager->flush();
    }
}
