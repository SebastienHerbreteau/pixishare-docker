<?php

namespace App\Message;

class UploadMessage
{
    private $albumId;
    private $filePaths;
    private $dateTaken;
    private $newAlbumName;
    private $userId;

    public function __construct(?int $albumId, array $filePaths, ?\DateTimeInterface $dateTaken, ?string $newAlbumName, int $userId)
    {
        $this->albumId = $albumId;
        $this->filePaths = $filePaths;
        $this->dateTaken = $dateTaken;
        $this->newAlbumName = $newAlbumName;
        $this->userId = $userId;
    }

    public function getAlbumId(): ?int
    {
        return $this->albumId;
    }

    public function getFilePaths(): array
    {
        return $this->filePaths;
    }

    public function getDateTaken(): ?\DateTimeInterface
    {
        return $this->dateTaken;
    }

    public function getNewAlbumName(): ?string
    {
        return $this->newAlbumName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

}

