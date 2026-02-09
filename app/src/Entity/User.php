<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\UserStatusEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

class User
{

    private int $id;

    private string $name;

    private string $email;

    private string $password;

    private string $document;

    private string $phone;

    private UserStatusEnum $status;

    private DateTime $createdAt;

    private DateTime $updatedAt;

    public function __construct(
        string $name,
        string $email,
        string $document,
        string $phone
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->phone = $phone;
        $this->status = UserStatusEnum::INACTIVE;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updateTimestamps();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->updateTimestamps();
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setDocument(string $document): void
    {
        $this->document = $document;
        $this->updateTimestamps();
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
        $this->updateTimestamps();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UserStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function isActive(): bool
    {
        return UserStatusEnum::ACTIVE === $this->status;
    }

    public function activate(): void
    {
        $this->status = UserStatusEnum::ACTIVE;
        $this->updateTimestamps();
    }

    public function deactivate(): void
    {
        $this->status = UserStatusEnum::INACTIVE;
        $this->updateTimestamps();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $datetime): void
    {
        $this->updatedAt = $datetime;
    }

    public function setCreatedAt(DateTime $datetime): void
    {
        $this->createdAt = $datetime;
    }

    public function getShortName(): string
    {
        $partes = preg_split('/\s+/', trim($this->name));
        $primeiro = $partes[0] ?? '';
        $ultimo = count($partes) > 1 ? $partes[count($partes) - 1] : '';

        return trim($primeiro.' '.$ultimo);
    }

    private function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();
    }
}
