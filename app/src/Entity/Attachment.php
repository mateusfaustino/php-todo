<?php

declare(strict_types=1);

namespace App\Entity;

use App\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'attachments')]
class Attachment
{
    use UuidTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255, name: 'file_name')]
    private string $fileName;

    #[ORM\Column(type: 'string', length: 100, name: 'mime_type')]
    private string $mimeType;

    #[ORM\Column(type: 'integer', name: 'size_bytes')]
    private int $sizeBytes;

    #[ORM\Column(type: 'string', length: 500, name: 'storage_url')]
    private string $storageUrl;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private DateTime $createdAt;

    public function __construct(
        Task $task,
        User $user,
        string $fileName,
        string $mimeType,
        int $sizeBytes,
        string $storageUrl
    ) {
        $this->id = $this->generateUuid();
        $this->task = $task;
        $this->user = $user;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->sizeBytes = $sizeBytes;
        $this->storageUrl = $storageUrl;
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getSizeBytes(): int
    {
        return $this->sizeBytes;
    }

    public function setSizeBytes(int $sizeBytes): void
    {
        $this->sizeBytes = $sizeBytes;
    }

    public function getStorageUrl(): string
    {
        return $this->storageUrl;
    }

    public function setStorageUrl(string $storageUrl): void
    {
        $this->storageUrl = $storageUrl;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get file size in human-readable format
     */
    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->sizeBytes;
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if the attachment is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    /**
     * Check if the attachment is a document
     */
    public function isDocument(): bool
    {
        $documentMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];

        return in_array($this->mimeType, $documentMimeTypes, true);
    }

    /**
     * Get file extension
     */
    public function getFileExtension(): string
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }
}
