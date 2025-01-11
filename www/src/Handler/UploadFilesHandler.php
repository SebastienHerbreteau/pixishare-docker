<?php

namespace App\Handler;

use App\Entity\Photo;
use App\Entity\Album;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AlbumRepository;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\UserRepository;

class UploadFilesHandler
{
    private EntityManagerInterface $em;
    private string $galleryDirectory;
    private string $projectDir;
    private AlbumRepository $albumRepository;
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, string $galleryDirectory, string $projectDir, AlbumRepository $albumRepository, Security $security, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->galleryDirectory = $galleryDirectory;
        $this->projectDir = $projectDir;
        $this->albumRepository = $albumRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function upload(?int $albumId, array $filePaths, ?\DateTimeInterface $dateTaken, ?string $newAlbumName, int $userId): Album
    {
        $user = $this->userRepository->find($userId);
        if ($albumId !== null) {
            $album = $this->albumRepository->find($albumId);
        } else {
            $album = new Album();
            $album->setName($newAlbumName);
            $album->setCreatedAt(new \DateTimeImmutable());
            $album->setUpdatedAt(new \DateTimeImmutable());
            $album->setDateTaken($dateTaken);
            $album->setUser($user);
            $this->em->persist($album);
            $this->em->flush();
        }
        $dir = $this->galleryDirectory . $album->getId();
        $thumbnailDir = $dir . '/thumbnail';

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true);
        }

        foreach ($filePaths as $filePath) {
            // Traitement de l'image selon son type MIME
            $mimeType = mime_content_type($filePath);
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/png':
                case 'image/CR2':
                case 'image/bmp':
                    $this->makeImage($filePath, $album, $dir, $thumbnailDir, $user);
                    break;
                default:
                    break;
            }
        }
        return $album;
    }

    private function makeImage(string $filePath, Album $album, string $dir, string $thumbnailDir, User $user): bool
    {
        $photo = new Photo();

// Générer le numéro de l'image
        $existingFiles = glob($dir . '/*.webp'); // Liste des fichiers .webp dans le répertoire
        $imageNumber = count($existingFiles) + 1;
        $formatNumber = sprintf('%02d', $imageNumber);

// Noms des fichiers
        $newImageName = $formatNumber . '.webp';
        $newThumbnailName = 'm' . $formatNumber . '.webp';

// Chemins des fichiers
        $outputPath = $dir . '/' . $newImageName;
        $thumbnailPath = $thumbnailDir . '/' . $newThumbnailName;

// Appel du script Python
        $command = 'python ' . escapeshellarg($this->projectDir . '/script/image_processor.py') . ' ' .
            escapeshellarg($filePath) . ' ' . escapeshellarg($outputPath) . ' ' . escapeshellarg($thumbnailPath) . ' 2048';

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("Erreur lors du traitement de l'image : " . implode(", ", $output));
        }

// Sauvegarder les informations dans l'entité Photo
        $photo->setFilePath('images/gallery/' . $album->getId() . '/' . $newImageName);
        $photo->setThumbnail('images/gallery/' . $album->getId() . '/thumbnail/' . $newThumbnailName);
        $photo->setAlbum($album);
        $photo->setUser($user);

        $this->em->persist($photo);
        $this->em->flush();

        return true;
    }
}
