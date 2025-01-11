<?php

namespace App\Handler;

use App\Message\UploadMessage;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UploadMessageHandler
{
    private $uploadFilesHandler;

    public function __construct(UploadFilesHandler $uploadFilesHandler)
    {
        $this->uploadFilesHandler = $uploadFilesHandler;
    }

    public function __invoke(UploadMessage $message): void
    {
        $this->uploadFilesHandler->upload(
            $message->getAlbumId(),
            $message->getFilePaths(),
            $message->getDateTaken(),
            $message->getNewAlbumName(),
            $message->getUserId()
        );
    }
}
